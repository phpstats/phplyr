# Records the R and package versions that wrote a fixture in
# tests/Fixtures/r-versions.json, which bin/site-verifications reads.
record_versions <- function(fixture, packages) {
  path <- "tests/Fixtures/r-versions.json"
  all <- if (file.exists(path)) jsonlite::read_json(path) else list()
  v <- list(R = paste(R.version$major, R.version$minor, sep = "."))
  for (p in packages) v[[p]] <- as.character(packageVersion(p))
  all[[fixture]] <- v
  jsonlite::write_json(all[order(names(all))], path, auto_unbox = TRUE, pretty = TRUE)
}
