### `phplyr()` {#phplyr}

```php
phplyr(Table|Chain $table): Chain
```

Start a method chain on a table: `phplyr($t)->filter(...)->head(5)`. Each method runs the phplyr function of that name, spelled camelCase, so `->groupBy()` runs `group_by()`. `get()` returns the table, and functions that return something else, such as `pull()`, return it plainly.

Returns `Chain`.

### `pipe()` {#pipe}

```php
pipe(mixed $value, callable ...$steps): mixed
```

Apply steps to a value in order: `pipe($t, filter(...), head(5))` is `$t` |&gt; `filter(...)` |&gt; `head(5)`. A step is any callable.

Returns `mixed`.
