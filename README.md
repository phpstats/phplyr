# phplyr

phplyr is a PHP library for filtering, transforming, summarizing, joining, and reshaping tables of data. Its functions and arguments follow R's [dplyr](https://dplyr.tidyverse.org) and [tidyr](https://tidyr.tidyverse.org), and it works on [phibble](https://github.com/phpstats/phibble) tables: the in-memory `DataFrame`, database tables, and Polars through phibble's `PolarsTable`.

## Features

- dplyr's single-table and two-table functions, with grouped window functions, `across()`, column selectors, and `join_by()` conditions.
- tidyr's pivoting, completing, splitting, nesting, and list-column functions.
- janitor's `clean_names()`, `remove_empty()`, `get_dupes()`, and `tabyl()`.
- `lm()`, `glm()`, `lmer()`, and `glmer()`, which fit models with [phit](https://github.com/phpstats/phit).
- Pipelines on phibble's in-memory `DataFrame`, on database tables as SQL, and on Polars.

## Example

To run the example below, create a Composer project in an empty directory, save one of the scripts as `first.php`, and run `php first.php`. It reads [`penguins`](https://github.com/phpstats/datasets#penguins), size measurements for 344 penguins near Palmer Station, Antarctica, by URL.

```bash
composer require phpstats/phplyr
```

**PHP 8.5+**

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{filter, group_by, summarize};
use function Phplyr\{arrange, desc, n, mean, sd};

$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo $penguins |>
    filter(fn ($r) => $r->body_mass_g !== null) |>
    group_by('species') |>
    summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g'),
        sd_mass_g: sd('body_mass_g'),
    ) |>
    arrange(desc('avg_mass_g'));
```

**PHP 8.3–8.4**

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, n, mean, sd};

$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo phplyr($penguins)
    ->filter(fn ($r) => $r->body_mass_g !== null)
    ->groupBy('species')
    ->summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g'),
        sd_mass_g: sd('body_mass_g'),
    )
    ->arrange(desc('avg_mass_g'));
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
- [Functions and methods](https://phplyr.phpstats.org/docs/reference/index), every function and method with its reference entry
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
