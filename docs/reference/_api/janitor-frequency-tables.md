### `tabyl()` {#tabyl}

```php
tabyl(string $var1, ?string $var2 = null, bool $showNa = true, bool $showMissingLevels = true): Closure
```

janitor's `tabyl()` for one or two columns. One column gives its values with n and percent (and valid_percent, which leaves NA out, when there are NAs). Two columns give a crosstab of counts: one row per value of the first, one column per value of the second ("NA_" for NA, "emptystring_" for ""). Values sort as dplyr's `count()` does, with NA last and factors in level order.

Returns a function of a table, which returns a table. Chain method: `->tabyl()`.
