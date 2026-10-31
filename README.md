# phplyr

phplyr is a PHP library for filtering, transforming, summarizing, joining, and reshaping tables of data. Its functions and arguments follow R's [dplyr](https://dplyr.tidyverse.org) and [tidyr](https://tidyr.tidyverse.org), and it works on [phibble](https://github.com/phpstats/phibble) tables: the in-memory `DataFrame`, database tables, and Polars through phibble's `PolarsTable`.

To run the example below, create a Composer project in an empty directory, download the data, save one of the scripts as `first.php`, and run `php first.php`:

```bash
composer require phpstats/phplyr
curl -O https://raw.githubusercontent.com/phpstats/phplyr/v0.4.3/docs/data/schools.csv
```

**PHP 8.5+**

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{filter, group_by, summarize, arrange, head, desc, n, mean, sd};

$schools = DataFrame::fromCsv('schools.csv');

echo $schools |>
    filter(fn ($r) => $r->study_hours >= 5) |>
    group_by('school') |>
    summarize(students: n(), avg_score: mean('score'), sd_score: sd('score')) |>
    arrange(desc('avg_score')) |>
    head(5);
```

**PHP 8.3–8.4**

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, n, mean, sd};

$schools = DataFrame::fromCsv('schools.csv');

echo phplyr($schools)
    ->filter(fn ($r) => $r->study_hours >= 5)
    ->groupBy('school')
    ->summarize(students: n(), avg_score: mean('score'), sd_score: sd('score'))
    ->arrange(desc('avg_score'))
    ->head(5);
```

## Installation

```bash
composer require phpstats/phplyr
```

phplyr supports PHP 8.3, 8.4, and 8.5, and requires the mbstring and PDO extensions. CI tests all three PHP versions. The pipe operator needs PHP 8.5.

The statistical models need the [phit](https://github.com/phpstats/phit) extension or [phit-polyfill](https://github.com/phpstats/phit-polyfill). [Installation](https://phplyr.phpstats.org/docs/getting-started#installation) describes the requirements and the optional features.

## Documentation

The documentation is at <https://phplyr.phpstats.org/docs>:

- [Getting started](https://phplyr.phpstats.org/docs/getting-started)
- [Coming from dplyr](https://phplyr.phpstats.org/docs/coming-from-dplyr)
- Reference pages for single-table functions, joins, grouping, vector helpers, strings and dates, column selection, tidyr, janitor, models, and databases
- [Gallery](https://phplyr.phpstats.org/docs/gallery) and [Porting an R script](https://phplyr.phpstats.org/docs/port-r-script)

[CHANGELOG.md](CHANGELOG.md) lists the changes in each release.

## Development

```sh
composer install
bin/test                        # uses .ext/libphit.dylib when present
NO_EXT=1 bin/test               # without phit
PHP=/path/to/php83 bin/test     # before PHP 8.5, leaves out tests/Pipe (|>)
composer lint                   # naming conventions
```

The test suite compares phplyr with R. The scripts in `tests/r/` write the reference fixtures. The lock file is resolved for PHP 8.3 (`config.platform`), so it installs on every supported version. After adding or changing a table function, run `bin/chain-docblock` to update the method chain's `@method` list.

The documentation is a Quarto project in `docs/`. [docs/development.qmd](docs/development.qmd) describes the examples, rendering, and the files in `site/`.

## License

MIT. See [LICENSE](LICENSE).
