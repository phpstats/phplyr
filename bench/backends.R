# Time bench/backends.php's pipelines A and B with dplyr, on the same data,
# and check each result against phplyr's: the row count and the sums of the
# numeric columns must match to 1e-9 (relative). Pipeline C is A with a PHP
# closure, so it has no separate dplyr version.
#
# Run bench/backends.php with --csv-dir=bench/data first, then from the repo
# root:
#
#   Rscript bench/backends.R [bench/data] [docs/benchmarks/backends-results.json]
#
# Writes docs/benchmarks/dplyr-results.json.
suppressPackageStartupMessages({
  library(dplyr)
  library(jsonlite)
})

args <- commandArgs(trailingOnly = TRUE)
dir <- if (length(args) >= 1) args[[1]] else "bench/data"
php_results <- fromJSON(if (length(args) >= 2) args[[2]] else "docs/benchmarks/backends-results.json", simplifyVector = FALSE)
repeats <- php_results$method$repeats

pipelines <- list(
  A = function(s, st) s |>
    filter(units > 0) |>
    left_join(st, by = "store") |>
    group_by(region, product) |>
    summarise(n = n(), revenue = sum(units * price), avg_price = mean(price), .groups = "drop_last") |>
    group_by(region) |>
    mutate(rank = min_rank(desc(revenue))) |>
    filter(rank <= 10) |>
    arrange(region, rank),
  B = function(s, st) s |>
    group_by(store) |>
    mutate(prev = lag(units, order_by = day), running = cumsum(units), share = units / sum(units, na.rm = TRUE))
)

matches <- function(result, check) {
  if (nrow(result) != check$rows) return(FALSE)
  for (col in names(check$sums)) {
    got <- sum(as.numeric(result[[col]]), na.rm = TRUE)
    want <- check$sums[[col]]
    if (abs(got - want) > 1e-9 * max(1, abs(want))) return(FALSE)
  }
  TRUE
}

stores <- read.csv(file.path(dir, "stores.csv"), stringsAsFactors = FALSE)
results <- list()
for (rows in unique(vapply(php_results$results, function(r) r$rows_in, numeric(1)))) {
  sales <- read.csv(file.path(dir, sprintf("sales-%d.csv", as.integer(rows))), stringsAsFactors = FALSE)
  sales <- as_tibble(sales)
  for (id in names(pipelines)) {
    times <- numeric(0)
    for (k in seq_len(repeats)) {
      gc()
      t0 <- proc.time()[["elapsed"]]
      result <- pipelines[[id]](sales, stores)
      times <- c(times, round(proc.time()[["elapsed"]] - t0, 4))
    }
    ref <- Filter(function(r) r$pipeline == id && r$rows_in == rows && r$backend == "DataFrame", php_results$results)[[1]]
    ok <- matches(result, ref$checksums)
    results[[length(results) + 1]] <- list(
      pipeline = id, backend = "dplyr", rows_in = rows, rows_out = nrow(result),
      seconds = min(times), seconds_all = times, matches_frame = ok
    )
    message(sprintf("%9s %s dplyr %8.4f s %s", format(as.integer(rows), big.mark = ","), id, min(times), if (ok) "" else "RESULT DIFFERS"))
  }
}

write_json(list(
  schema = "phplyr-dplyr-backends/1",
  generated_at = format(Sys.time(), "%Y-%m-%dT%H:%M:%SZ", tz = "UTC"),
  method = list(
    statistic = "best", repeats = repeats,
    timing = "pipeline on a preloaded tibble; reading the CSV is not timed",
    verification = "row count and numeric column sums compared with phplyr's DataFrame result (relative 1e-9)",
    r = R.version.string, dplyr = as.character(packageVersion("dplyr"))
  ),
  results = results
), "docs/benchmarks/dplyr-results.json", auto_unbox = TRUE, pretty = TRUE, digits = NA)
