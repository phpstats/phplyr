### `remove_empty()` {#remove_empty}

```php
remove_empty(array|string $which = ['rows', 'cols'], float $cutoff = 1.0): Closure
```

Drop rows and/or columns that are entirely NA, as janitor's `remove_empty()`. With a cutoff below 1 (for rows or cols alone), keep those whose share of non-NA values is above the cutoff.

Returns a function of a table, which returns a table. Chain method: `->removeEmpty()`. Throws `InvalidArgumentException` on invalid arguments.
