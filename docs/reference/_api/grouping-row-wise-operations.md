### `rowwise()` {#rowwise}

```php
rowwise(string ...$idColumns): Closure
```

Treat each row as its own group, for row-wise summaries. Optional id columns are kept as groups by `summarise()`. Needs a table that supports `rowwise()` (phibble Table contract v1).

Returns a function of a table, which returns a table. Chain method: `->rowwise()`. Throws `LogicException` on invalid arguments. [Example](#row-wise-operations).
