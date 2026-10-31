# phplyr

phplyr is a PHP library for filtering, transforming, summarizing, joining, and reshaping tables of data. Its functions and arguments follow R's [dplyr](https://dplyr.tidyverse.org) and [tidyr](https://tidyr.tidyverse.org), and it works on [phibble](https://github.com/phpstats/phibble) tables: the in-memory `DataFrame`, database tables, and Polars through phibble's `PolarsTable`.

**PHP 8.5+**

```php
use Phibble\DataFrame;
use function Phplyr\{filter, mutate, group_by, summarize, arrange, desc, n, mean, min_rank};

$out = DataFrame::fromCsv('sales.csv') |>
    filter(fn ($r) => $r->price > 0) |>
    group_by('brand') |>
    summarize(n: n(), avg: mean('price')) |>
    mutate(rank: min_rank(desc('avg'))) |>
    arrange('rank');
```

**PHP 8.3–8.4**

```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, n, mean, min_rank};

$out = phplyr(DataFrame::fromCsv('sales.csv'))
    ->filter(fn ($r) => $r->price > 0)
    ->groupBy('brand')
    ->summarize(n: n(), avg: mean('price'))
    ->mutate(rank: min_rank(desc('avg')))
    ->arrange('rank')
    ->get();
```

## Installation

```bash
composer require phpstats/phplyr
```

composer.json allows PHP 8.3 and later 8.x versions. CI tests PHP 8.3, 8.4, and 8.5. The pipe operator needs PHP 8.5.

The [phit](https://github.com/phpstats/phit) extension is optional. It provides `lm()`, `glm()`, `lmer()`, and `glmer()`. [Compatibility and limitations](https://phplyr.phpstats.org/docs/compatibility) lists the other optional extensions and the backends.

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
