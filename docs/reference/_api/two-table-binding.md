### `bind_rows()` {#bind_rows}

```php
bind_rows(mixed ...$tables): Closure
```

Stack tables under the piped one, matching columns by name; missing columns are NA: `$a` |&gt; `bind_rows($b, $c)`. Option `_id` adds a column with each row's source (1 for the piped table, 2 for the next...).

Returns a function of a table, which returns a table. Chain method: `->bindRows()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#binding).

### `bind_cols()` {#bind_cols}

```php
bind_cols(Table ...$tables): Closure
```

Put tables side by side with the piped one; all must have the same number of rows. Duplicate names are made unique as name...position, like dplyr.

Returns a function of a table, which returns a table. Chain method: `->bindCols()`. Throws `LengthException` on invalid arguments. [Example](#binding).
