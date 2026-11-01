# Regenerate tests/Fixtures/stats-reference.json: R's descriptive statistics
# for tests/StatsTest.php. Run from the repo root: Rscript tests/r/stats.R
library(jsonlite)
source("tests/r/versions.R")
x <- c(2, 4, 4, 5, 7, 9)
out <- list(
  "mean(x)" = mean(x),
  "var(x)" = var(x),
  "sd(x)" = sd(x),
  "median(x)" = median(x),
  "quantile(x, 0.9)" = unname(quantile(x, 0.9)),
  "quantile(x, 0)" = unname(quantile(x, 0)),
  "quantile(x, 1)" = unname(quantile(x, 1)),
  "sum(x)" = sum(x),
  "min(x)" = min(x),
  "max(x)" = max(x),
  "mean(c(1, NA, 3))" = mean(c(1, NA, 3)),
  "mean(c(1, NA, 3), na.rm = TRUE)" = mean(c(1, NA, 3), na.rm = TRUE),
  "sd(c(1, NaN, 3), na.rm = TRUE)" = sd(c(1, NaN, 3), na.rm = TRUE),
  "var(1)" = var(1)
)
write_json(out, "tests/Fixtures/stats-reference.json", digits = NA, auto_unbox = TRUE, pretty = TRUE, na = "null")
record_versions("stats-reference.json", character())
