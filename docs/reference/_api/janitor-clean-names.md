### `make_clean_names()` {#make_clean_names}

```php
make_clean_names(array $names): array
```

janitor's `make_clean_names()`: snake_case names that are unique, ASCII, and start with a letter. '%' becomes "percent", '#' becomes "number", accents and other scripts are transliterated (with ext-intl, as janitor does with stringi), names that would start with a digit get an "x" prefix, and repeats get _2, _3, ... suffixes.

Returns `array`.

### `clean_names()` {#clean_names}

```php
clean_names(): Closure
```

Rename every column with `make_clean_names()`, as janitor's `clean_names()`.

Returns a function of a table, which returns a table. Chain method: `->cleanNames()`.
