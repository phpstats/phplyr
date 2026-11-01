### `ymd()` {#ymd}

```php
ymd(mixed $x, ?string $tz = null): Vec
```

Parse year-month-day strings to dates, with any separator.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `dmy()` {#dmy}

```php
dmy(mixed $x, ?string $tz = null): Vec
```

Parse day-month-year strings to dates, with any separator.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `mdy()` {#mdy}

```php
mdy(mixed $x, ?string $tz = null): Vec
```

Parse month-day-year strings to dates, with any separator.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `ymd_hms()` {#ymd_hms}

```php
ymd_hms(mixed $x, ?string $tz = null): Vec
```

Parse year-month-day hour:minute:second strings to datetimes, in UTC or tz.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `dmy_hms()` {#dmy_hms}

```php
dmy_hms(mixed $x, ?string $tz = null): Vec
```

Parse day-month-year hour:minute:second strings to datetimes, in UTC or tz.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `mdy_hms()` {#mdy_hms}

```php
mdy_hms(mixed $x, ?string $tz = null): Vec
```

Parse month-day-year hour:minute:second strings to datetimes, in UTC or tz.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `parse_date_time()` {#parse_date_time}

```php
parse_date_time(mixed $x, array|string $orders, ?string $tz = null): Vec
```

Parse with lubridate orders: `parse_date_time('when', ['Ymd HMS', 'dmY HMS'])`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `year()` {#year}

```php
year(mixed $x): Vec
```

The year of a date or datetime.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `month()` {#month}

```php
month(mixed $x): Vec
```

The month, 1-12.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `day()` {#day}

```php
day(mixed $x): Vec
```

The day of the month.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `wday()` {#wday}

```php
wday(mixed $x, ?int $weekStart = null): Vec
```

Day of the week, 1-7; Sunday is 1 unless weekStart says otherwise (1 = Monday).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `yday()` {#yday}

```php
yday(mixed $x): Vec
```

The day of the year, 1-366.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `quarter()` {#quarter}

```php
quarter(mixed $x): Vec
```

The quarter, 1-4.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `hour()` {#hour}

```php
hour(mixed $x): Vec
```

The hour of a datetime, 0-23.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `minute()` {#minute}

```php
minute(mixed $x): Vec
```

The minute of a datetime, 0-59.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `second()` {#second}

```php
second(mixed $x): Vec
```

The second of a datetime, 0-59.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `floor_date()` {#floor_date}

```php
floor_date(mixed $x, string $unit, ?int $weekStart = null): Vec
```

Round down to a unit: 'second', 'minute', 'hour', 'day', 'week', 'month', 'quarter', or 'year', optionally with a multiple such as '15 minutes'.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `ceiling_date()` {#ceiling_date}

```php
ceiling_date(mixed $x, string $unit, ?int $weekStart = null): Vec
```

Round up to a unit, as `floor_date()`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `round_date()` {#round_date}

```php
round_date(mixed $x, string $unit, ?int $weekStart = null): Vec
```

Round to the nearest unit, as `floor_date()`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `seconds()` {#seconds}

```php
seconds(mixed $n = 1): Vec
```

A period of n seconds, for date arithmetic.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `minutes()` {#minutes}

```php
minutes(mixed $n = 1): Vec
```

A period of n minutes.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `hours()` {#hours}

```php
hours(mixed $n = 1): Vec
```

A period of n hours.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `days()` {#days}

```php
days(mixed $n = 1): Vec
```

A period of n days.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `weeks()` {#weeks}

```php
weeks(mixed $n = 1): Vec
```

A period of n weeks.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `months()` {#months}

```php
months(mixed $n = 1): Vec
```

A period of n months; adding one to a day the target month lacks gives NA, as lubridate does.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `years()` {#years}

```php
years(mixed $n = 1): Vec
```

A period of n years.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `difftime()` {#difftime}

```php
difftime(mixed $x, mixed $y, ?string $units = null): Vec
```

The difference x - y in units 'secs', 'mins', 'hours', 'days', or 'weeks', as a double.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `with_tz()` {#with_tz}

```php
with_tz(mixed $x, string $tz): Vec
```

The same instant in another time zone.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `force_tz()` {#force_tz}

```php
force_tz(mixed $x, string $tz): Vec
```

The same clock time in another time zone.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `today()` {#today}

```php
today(?string $tz = null): Vec
```

The current date, in the system time zone or tz.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `now()` {#now}

```php
now(?string $tz = null): Vec
```

The current datetime, in the system time zone or tz.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.
