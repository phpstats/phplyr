### `lag()` {#lag}

```php
lag(mixed $x, int $n = 1, mixed $default = null, mixed $orderBy = null): Vec
```

Previous values: `lag('x')`, `lag('x', 2, default: 0, orderBy: 'year')`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. Throws `InvalidArgumentException` on invalid arguments. [Example](#window-functions).

### `lead()` {#lead}

```php
lead(mixed $x, int $n = 1, mixed $default = null, mixed $orderBy = null): Vec
```

Next values: `lead('x')`, `lead('x', 2, default: 0, orderBy: 'year')`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. Throws `InvalidArgumentException` on invalid arguments.

### `row_number()` {#row_number}

```php
row_number(mixed $x = null): Vec
```

Row numbers: 1..n, or ranks of `$x` with ties broken by position.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `min_rank()` {#min_rank}

```php
min_rank(mixed $x): Vec
```

Ranks with ties sharing the lowest rank (1, 2, 2, 4); `min_rank(desc('x'))` reverses.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#window-functions).

### `dense_rank()` {#dense_rank}

```php
dense_rank(mixed $x): Vec
```

Ranks with no gaps after ties (1, 2, 2, 3).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `percent_rank()` {#percent_rank}

```php
percent_rank(mixed $x): Vec
```

min_rank rescaled to [0, 1].

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#window-functions).

### `cume_dist()` {#cume_dist}

```php
cume_dist(mixed $x): Vec
```

Proportion of values less than or equal to each value.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `ntile()` {#ntile}

```php
ntile(mixed $x = null, int $n = 1): Vec
```

Split into `$n` buckets of near-equal size: `ntile('x', 4)`, or `ntile(n: 4)` by row.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. Throws `InvalidArgumentException` on invalid arguments. [Example](#window-functions).

### `consecutive_id()` {#consecutive_id}

```php
consecutive_id(mixed ...$xs): Vec
```

A new id each time the value (or combination) changes.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `cumsum()` {#cumsum}

```php
cumsum(mixed $x): Vec
```

Running sum; NA from the first NA onward.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `cumprod()` {#cumprod}

```php
cumprod(mixed $x): Vec
```

Running product; NA from the first NA onward.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `cummin()` {#cummin}

```php
cummin(mixed $x): Vec
```

Running minimum; NA from the first NA onward.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `cummax()` {#cummax}

```php
cummax(mixed $x): Vec
```

Running maximum; NA from the first NA onward.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `cummean()` {#cummean}

```php
cummean(mixed $x): Vec
```

Running mean.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#window-functions).

### `cumall()` {#cumall}

```php
cumall(mixed $x): Vec
```

Running `all()`: false from the first false on; NA while undecided.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `cumany()` {#cumany}

```php
cumany(mixed $x): Vec
```

Running `any()`: true from the first true on; NA while undecided.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `order_by()` {#order_by}

```php
order_by(mixed $orderBy, Vec $call): Vec
```

Evaluate a window function with the rows in another order, then put the results back: `order_by('year', cumsum('score'))`. Wrap the order in `desc()` to reverse it; NA sorts last.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.
