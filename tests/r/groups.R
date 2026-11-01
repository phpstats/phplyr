# Regenerate tests/Fixtures/cur-group-reference.json and drop-reference.json.
# Run from the repo root: Rscript tests/r/groups.R
suppressPackageStartupMessages({
  library(dplyr)
  library(jsonlite)
})
source("tests/r/versions.R")
panel <- read.csv("tests/Fixtures/panel.csv")

cur <- list(
  mut = panel |> group_by(label) |> mutate(gid = cur_group_id(), row = cur_group_rows()) |> ungroup() |> select(id, gid, row),
  summ = panel |> group_by(g, label) |> summarise(gid = cur_group_id(), n = n(), .groups = "drop"),
  refr = panel |> group_by(g) |> reframe(rows = cur_group_rows())
)
write_json(cur, "tests/Fixtures/cur-group-reference.json", digits = NA, auto_unbox = TRUE, pretty = TRUE, na = "null")
record_versions("cur-group-reference.json", c("dplyr"))

d <- tibble(f = factor(c("a", "a", "c"), levels = c("a", "b", "c")), s = c("x", "y", "x"), v = c(1, 2, 3))
drop <- list(
  drop = d |> group_by(f, .drop = FALSE) |> summarise(n = n(), total = sum(v)),
  count = d |> group_by(f, .drop = FALSE) |> count() |> ungroup(),
  two = d |> group_by(f, s, .drop = FALSE) |> summarise(n = n(), .groups = "drop")
)
write_json(drop, "tests/Fixtures/drop-reference.json", digits = NA, auto_unbox = TRUE, pretty = TRUE, na = "null")
record_versions("drop-reference.json", c("dplyr"))
