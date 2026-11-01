# Regenerate tests/Fixtures/janitor-reference.json: janitor 2.2's
# make_clean_names(), remove_empty(), get_dupes(), and tabyl(). Run from the
# repo root.
suppressPackageStartupMessages({
  library(janitor)
  library(dplyr)
  library(jsonlite)
})

names_in <- c(
  "Sepal.Length", "  First Name ", "first_name", "First Name", "% Done", "# of Items",
  "1st Place", "Café Crème", "ÆØÅ straße", "camelCaseVar", "HTMLParser", "a__b", "", NA,
  "x", "...", "Größe (kg)", "naïve-résumé", "id", "ID", "x2", "x_2", "Total $", "a/b",
  "über", "αβγ", "日本", "already_clean", "Var1", "var1", "2020", "x@y.com", "it's",
  "ABC123def", "a1B2", "x.1", "X", "_private", "camelCase2Var", "dataJSON", "iPhone",
  "mAX", "Ω", "Здравствуй", "€ price", "a—b", "tab\there", "100%", "#", "%", "a.b.c",
  "a..b", "TRUE", "if", "NA", "first-name", "First.Name.", "x__", "__x", "ｆｕｌｌ",
  "Ａ１", "ａｂｃ ＤＥＦ", "à la carte", "éte", "q1_2024", "Q1 2024", "pH", "ID#",
  "Customer ID", "customerID", "CustomerID", "% of total (%)", "\"quoted\"", "a b  c"
)

empty <- data.frame(
  a = c(1, NA, 2, NA, 1),
  b = c("x", NA, "y", NA, ""),
  c = c(NA, NA, NA, NA, NA),
  d = c(NA, NA, 3, NA, NA)
)

dupes <- data.frame(
  id = c(1, 2, 3, 4, 5, 6, 7),
  g = c("b", "a", "b", "a", NA, NA, "c"),
  v = c(1, 1, 1, 2, 3, 3, 4)
)

tab <- data.frame(
  g = c("b", "a", "b", NA, "a", "b", "c", "a"),
  h = c("x", "y", "x", "x", NA, "y", "", "x"),
  k = c(3, 1, 3, 2, 1, 3, NA, 2),
  l = c(TRUE, FALSE, TRUE, NA, TRUE, FALSE, TRUE, TRUE),
  f = factor(c("lo", "hi", "lo", "hi", NA, "lo", "lo", "hi"), levels = c("lo", "mid", "hi"))
)

rows <- function(d) as.data.frame(d) |> mutate(across(where(is.factor), as.character))

out <- list(
  names_in = names_in,
  clean = make_clean_names(names_in),
  remove_both = rows(remove_empty(empty, c("rows", "cols"))),
  remove_rows = rows(remove_empty(empty, "rows")),
  remove_cols = rows(remove_empty(empty, "cols")),
  remove_rows_cutoff = rows(remove_empty(empty, "rows", cutoff = 0.5)),
  remove_cols_cutoff = rows(remove_empty(empty, "cols", cutoff = 0.3)),
  dupes_all = rows(get_dupes(dupes |> select(g, v))),
  dupes_g = rows(get_dupes(dupes, g)),
  dupes_gv = rows(get_dupes(dupes, g, v)),
  dupes_v = rows(get_dupes(dupes, v)),
  tabyl_g = rows(tabyl(tab, g)),
  tabyl_g_no_na = rows(tabyl(tab, g, show_na = FALSE)),
  tabyl_k = rows(tabyl(tab, k)),
  tabyl_l = rows(tabyl(tab, l)),
  tabyl_f = rows(tabyl(tab, f)),
  tabyl_f_no_missing = rows(tabyl(tab, f, show_missing_levels = FALSE)),
  tabyl_h = rows(tabyl(tab |> filter(!is.na(h)), h)),
  tabyl_gh = rows(tabyl(tab, g, h)),
  tabyl_gh_no_na = rows(tabyl(tab, g, h, show_na = FALSE)),
  tabyl_gk = rows(tabyl(tab, g, k)),
  tabyl_fl = rows(tabyl(tab, f, l))
)

write_json(out, "tests/Fixtures/janitor-reference.json", digits = NA, auto_unbox = TRUE, pretty = TRUE, na = "null")
source("tests/r/versions.R")
record_versions("janitor-reference.json", c("janitor", "dplyr"))
