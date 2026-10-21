# phplyr

A PHP port of the Tidyverse's dplyr and tidyr, for [phibble](https://github.com/phpstats/phibble) tables. It runs on PHP 8.3 and later with method chains, and on PHP 8.5 with the pipe operator.

**Method chain (PHP 8.3+)**

```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, n, mean, min_rank};

$out = phplyr(DataFrame::fromCsv('sales.csv'))
    ->filter(fn ($r) => $r->price > 0)
    ->mutate(log_price: fn ($r) => log($r->price))
    ->groupBy('brand')
    ->summarise(n: n(), avg: mean('price'))
    ->mutate(rank: min_rank(desc('avg')))
    ->arrange('rank')
    ->get();
```

**Pipe (PHP 8.5)**

```php
use Phibble\DataFrame;
use function Phplyr\{filter, mutate, group_by, summarise, arrange, desc, n, mean, min_rank};

$out = DataFrame::fromCsv('sales.csv')
    |> filter(fn ($r) => $r->price > 0)
    |> mutate(log_price: fn ($r) => log($r->price))
    |> group_by('brand')
    |> summarise(n: n(), avg: mean('price'))
    |> mutate(rank: min_rank(desc('avg')))
    |> arrange('rank');
```

The two give the same table. `phplyr($t)` starts a chain, each method runs the function of that name with the same arguments, and `get()` returns the table. Functions that return something other than a table, such as `pull()` or `lm()`, end the chain and return their result. `pipe($t, filter(...), group_by('brand'))` applies functions in order and also works on every version.

Each function returns a closure that takes one `Phibble\Contracts\Table`, and delegates to the table, so the same pipeline runs on phibble's in-memory DataFrame, on a database table, and on phpolars' PolarsTable. Results follow dplyr 1.2 and tidyr 1.3. The test suite compares phplyr's output with R's; the reference scripts are in `tests/r/`.

Documentation: <https://phpstats.github.io/phplyr/>. Start with [getting started](https://phpstats.github.io/phplyr/getting-started.html) or [coming from dplyr](https://phpstats.github.io/phplyr/coming-from-dplyr.html).

## Installation

phplyr needs PHP 8.3 or later; the pipe operator needs PHP 8.5. Neither phplyr nor phibble is on Packagist yet, so add both GitHub repositories and require phplyr from its main branch:

```bash
composer config repositories.phibble vcs https://github.com/phpstats/phibble
composer config repositories.phplyr vcs https://github.com/phpstats/phplyr
composer require phpstats/phplyr:dev-main
```

Two extensions are optional:

- [phit](https://github.com/phpstats/phit) provides `lm()`, `glm()`, `lmer()`, and `glmer()`, and speeds up the descriptive helpers. Without it, the helpers run in PHP with the same results.
- [phpolars](https://github.com/phpstats/phpolars) provides the php_polars extension behind phibble's PolarsTable (`Phibble\Polars\PolarsTable`), a Polars-backed table that runs phplyr pipelines natively.

See [CHANGELOG.md](CHANGELOG.md) for what each release contains.
## Conventions

- **Columns are strings.** Write `'price'` where dplyr takes a bare name.
- **Row closures** receive `(Phibble\Row $r, Table $group)`, so `$r->price` or `$r['price']`. In a grouped table, `$group` is the row's group.
- **Vector helpers** such as `lag('x')`, `cumsum('x')`, `min_rank('x')` and `mean('x')` are evaluated once per group. A summary such as `mean('x')` used in `mutate()` is recycled within each group, as in R.
- **Helper arguments:** the first argument of a helper is its input column. A string there is a column name; it can also be `col('x')`, another helper, or a row closure. Later arguments are values, so a string there is a literal; wrap it in `col()` to use a column.
- **Dot-arguments** mostly drop the dot and read as camelCase: `.before` becomes `before`, `.keep_all` becomes `keepAll`. Six keep the dot as an underscore, because a column could have the same name: `_by`, `_keep`, `_groups`, `_id`, `_remove`, and `recode()`'s first argument. So `mutate(z: ..., _by: 'g')` groups the call, and `mutate(by: 1)` still makes a column called `by`. On a function that takes one of the camelCase options, a named argument the option claims is read as the option, not as a column of that name; `docs/coming-from-dplyr.qmd` lists the six and why they keep the prefix.
- **Positions** are 1-based, as in R: `pull(1)`, `pull(-1)`, `slice(1, 3)`.
- **NA is null.** Summaries return null when a value is missing, unless you pass `naRm: true`.
- **Shadowed built-ins:** `count`, `min`, `max`, `round`, `abs`, `log`, `str_replace`, `str_pad`, `str_split` and a few others shadow PHP's built-ins in files that import them. Write `\count()` for the built-in.

## Table functions

| Function | Example |
| --- | --- |
| `filter` | `filter(fn ($r) => $r->x > 0, between('y', 1, 5), _by: 'g')` |
| `select` | `select('id', starts_with('x'), except('tmp'))`, `select(['new' => 'old'], everything())` |
| `mutate` | `mutate(z: fn ($r) => $r->x * 2, prev: lag('x'), avg: mean('x'), drop_me: null)` |
| | Options: `_by`, `_keep: 'none'`, `before`, `after` |
| `transmute` | `transmute(z: fn ($r) => $r->x * 2)` |
| `rename` / `rename_with` | `rename(new: 'old')`, `rename_with(strtoupper(...), starts_with('x'))` |
| `relocate` | `relocate('score', before: 'g')`, `relocate('g', after: last_col())` |
| `arrange` | `arrange('a', desc('b'), desc(fn ($r) => $r->x * $r->y), byGroup: true)` |
| `group_by` / `ungroup` | `group_by('g', decade: fn ($r) => intdiv($r->year, 10) * 10, add: true)` |
| `summarise` / `summarize` | `summarise(n: n(), avg: mean('x'), across(...), _by: 'g', _groups: 'drop')` |
| `reframe` | `reframe(q: fn ($g) => [...several values...])` |
| `count` / `tally` | `count('g', 'h', wt: 'w', sort: true, name: 'rows')`, `tally()` |
| `add_count` / `add_tally` | `add_count('g', name: 'size')` |
| `distinct` | `distinct('g')`, `distinct('g', keepAll: true)` |
| `slice` | `slice(1, 3)`, `slice(-1)`. These are positions within each group. |
| `slice_head` / `slice_tail` | `slice_head(n: 3)`, `slice_tail(prop: 0.1)` |
| `slice_min` / `slice_max` | `slice_max('score', n: 2, withTies: false)` |
| `slice_sample` | `slice_sample(n: 10, replace: true, weightBy: 'w')`. Seed with `mt_srand()`. |
| `head` | `head(6)`. Ignores groups. |
| `pull` | `pull('x')`, `pull()` (the last column), `pull('x', name: 'id')` |
| `bind_rows` / `bind_cols` | `bind_rows($b, _id: 'src')`, `bind_cols($b)` |
| `glimpse` | `glimpse()`. Prints a summary and passes the table on. |
| Joins | `left_join($y, by: ['id' => 'key'], suffix: ['.x', '.y'], keep:, naMatches:, multiple:, unmatched:, relationship:)`. Also `inner_join`, `right_join`, `full_join`, `semi_join`, `anti_join` and `cross_join`. |
| `rowwise` | `rowwise('id')`. Each row becomes its own group. |

| `filter_out` | `filter_out(fn ($r) => $r->status === 'test')`. Drops rows where every condition is true, keeping NA rows (dplyr 1.2). |
| Set operations | `union`, `union_all`, `intersect`, `setdiff`, `symdiff`, `setequal` |
| `rows_*` | `rows_insert`, `rows_append`, `rows_update`, `rows_patch`, `rows_upsert`, `rows_delete`, with `by:`, `conflict:` and `unmatched:` |

**Group utilities:** `group_cols()` (a selector), `group_vars`, `n_groups`, `group_size`, `group_indices`, `group_keys`, `group_split`, `group_map`, `group_modify`, `group_walk`.

## Vector helpers

- **Summaries:** `n`, `n_distinct`, `sum`, `mean`, `median`, `sd`, `variance`, `quantile(x, p)`, `iqr`, `min`, `max`, `first`, `last`, `nth(x, n)`, `any`, `all`.
- **Windows:** `lag`, `lead` (with `default:` and `orderBy:`), `row_number`, `min_rank`, `dense_rank`, `percent_rank`, `cume_dist`, `ntile`, `cumsum`, `cumprod`, `cummin`, `cummax`, `cummean`, `cumall`, `cumany`, `consecutive_id`.
- **Conditionals and NA:** `if_else`, `case_when([cond, value], ..., default: ...)`, `replace_when`, `case_match`, `recode_values` / `replace_values` (cases or `from:` / `to:` lookup tables), `when_any` / `when_all`, `coalesce`, `na_if`, `between`, `near`.
- **Building blocks:** `col('x')`, `vec(fn ($r) => ...)`, and `->map(fn ($value, $r) => ...)` on any helper.

`sd` and `variance` use n - 1, and `quantile` uses R's type 7. The variance helper is called `variance()` because `var` is a reserved word in PHP. When the `phit` extension is loaded, the descriptive helpers use it; otherwise they fall back to pure PHP.

## tidyselect and across()

- **Selectors:** `starts_with`, `ends_with`, `contains`, `matches`, `num_range`, `everything`, `last_col`, `all_of`, `any_of`, and `where(fn ($values, $type, $name) => ...)`. `where` also takes a shortcut: `where('numeric')`.
- **Combining:** `except(...)` drops columns (dplyr's `-`). `any_selected` gives the union of selections and `all_selected` the intersection.
- **Where they work:** `select`, `relocate`, `rename_with`, `arrange`, `distinct`, `group_by`, `count` and `across`.

```php
phplyr($t)->groupBy('g')->summarise(across(starts_with('x'), ['avg' => mean(...), 'sd' => sd(...)]));
phplyr($t)->mutate(across(where('numeric'), fn ($v) => $v * 2));  // applied to each value
phplyr($t)->mutate(across(['a', 'b'], lag(...)));                 // phplyr helpers by name
phplyr($t)->filter(if_any(['a', 'b'], fn ($v) => $v > 8));

// On PHP 8.5, the same with |>:
$t |> group_by('g') |> summarise(across(starts_with('x'), ['avg' => mean(...), 'sd' => sd(...)]));
```

What an `across()` function receives:

- **A phplyr helper** passed as a first-class callable, such as `mean(...)`, is called with each column's name.
- **In `summarise()`**, any other closure receives the column's values as a list.
- **In `mutate()`**, a closure receives one value at a time, unless its first parameter is typed `array`. Then it receives the whole column.

Names default to `{col}` for one function, or `{col}_{fn}` for a named list.

## tidyr

The tidyr functions work the same way, with tidyr's argument names. The test suite compares them with tidyr output from `tests/r/tidyr.R`.

| Function | Example |
| --- | --- |
| `pivot_longer` | `pivot_longer(starts_with('wk'), namesTo: 'week', valuesTo: 'rank', namesPrefix: 'wk', valuesDropNa: true)` |
| | `namesTo` can list several names, split with `namesSep` or `namesPattern`. The name `'.value'` takes the value column's name from that part. |
| `pivot_wider` | `pivot_wider(namesFrom: 'key', valuesFrom: ['val', 'w'], valuesFill: 0, valuesFn: fn ($v) => array_sum($v))` |
| | Also takes `idCols`, `namesPrefix`, `namesSep`, `namesGlue: '{key}_{.value}'` and `namesSort`. |
| `complete` / `expand` | `complete('id', 'key', fill: ['n' => 0])`, `expand(nesting('g', 'label'), year: [2001, 2002])` |
| `crossing` / `expand_grid` | `crossing(x: [2, 1], y: ['a', 'b'])`. These return a new DataFrame. |
| `fill` | `fill('price', direction: 'downup')`. Works within groups. |
| `drop_na` / `replace_na` | `drop_na('wk1')`, `replace_na(['wk1' => 0])` |
| `uncount` | `uncount('n', _id: 'copy')` |
| `unite` | `unite('date', 'year', 'month', sep: '-', remove: false, naRm: true)` |
| `separate` | `separate('s', ['a', 'b'], sep: '-', extra: 'merge', fill: 'left', convert: true)` |
| `separate_wider_*` | `separate_wider_delim('s', '-', names: [...])`, `separate_wider_position('code', ['state' => 2, 1, 'n' => 2])`, `separate_wider_regex('code', ['l' => '[A-Z]+', 'd' => '\d+'])` |
| `separate_longer_*` | `separate_longer_delim('tags', ',')`, `separate_longer_position('code', 2)` |
| `nest` / `unnest` | `nest()` after `group_by('g')`, `nest(data: ['x', 'y'])`, `unnest('data')` |

## Expressions and databases

`col('price')->mul(1.1)->gt(100)` and helpers such as `mean('x')` build inspectable `Phibble\Expr` trees when their arguments are column names, literals, or other helpers. phplyr passes them to the table. The in-memory DataFrame evaluates them, and `Phibble\tbl($pdo, 'sales')` compiles them to SQL (SQLite, PostgreSQL, MySQL). `show_query()` returns the SQL and `collect()` runs it. A PHP closure makes a database table load its rows and continue in memory. `mutate(_keep: 'used' | 'unused')` works with expression arguments.

## Models

`lm('y ~ x + g')`, `glm('y ~ x', family: 'binomial')`, `lmer('y ~ x + (1 | g)')` and `glmer(...)` pass the table to phit's formula functions, so factor levels reach the model.

## Development

```sh
composer install
bin/test                        # uses .ext/libphit.dylib when present
NO_EXT=1 bin/test               # pure-PHP fallback
PHP=/path/to/php83 bin/test      # before PHP 8.5, leaves out tests/Pipe (|>)
Rscript tests/r/reference.R     # regenerate the dplyr reference outputs
```

phibble is installed from GitHub through a VCS repository in composer.json. The lock file is resolved for PHP 8.3 (`config.platform`), so it installs on every supported version. After adding or changing a table function, run `bin/chain-docblock` to update the method chain's `@method` list.

The documentation site is in `docs/` (Quarto). `bin/render-docs` refreshes the example output and renders it; see `docs/development.qmd`.
