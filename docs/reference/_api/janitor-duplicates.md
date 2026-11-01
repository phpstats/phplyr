### `get_dupes()` {#get_dupes}

```php
get_dupes(mixed ...$columns): Closure
```

Rows whose values in these columns (all columns when none are given) appear more than once, as janitor's `get_dupes()`: the columns, then dupe_count, then the rest, sorted by dupe_count (descending) and the columns. Grouping is kept but not used.

Returns a function of a table, which returns a table. Chain method: `->getDupes()`.
