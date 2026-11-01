### `show_query()` {#show_query}

```php
show_query(): Closure
```

The SQL a lazy table would run (database backends only).

Returns a function of a table, which returns the SQL string. Chain method: `->showQuery()`. Throws `LogicException` on invalid arguments. [Example](#database-tables).

### `collect()` {#collect}

```php
collect(): Closure
```

Run a lazy table's query and return an in-memory Phibble\DataFrame.

Returns a function of a table, which returns a table. Chain method: `->collect()`. [Example](#database-tables).
