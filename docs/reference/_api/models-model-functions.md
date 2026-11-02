### `lm()` {#lm}

```php
lm(string $formula, mixed ...$options): Closure
```

Linear regression: `$t` |&gt; `lm('y ~ a + b')`. Calls phit's `fit_ols()`.

Returns a function of a table, which returns phit's fit object (an array before phit 0.6). Chain method: `->lm()`. [Example](#results).

### `glm()` {#glm}

```php
glm(string $formula, string $family = 'gaussian', mixed ...$options): Closure
```

Generalised linear model: `$t` |&gt; `glm('y ~ x', family: 'binomial')`. Calls phit's `fit_generalized_linear()`.

Returns a function of a table, which returns phit's fit object (an array before phit 0.6). Chain method: `->glm()`. [Example](#results).

### `lmer()` {#lmer}

```php
lmer(string $formula, mixed ...$options): Closure
```

Linear mixed model with lme4-style random effects: `$t` |&gt; `lmer('Reaction ~ Days + (1 | Subject)')`. REML by default (reml: false for maximum likelihood). Calls phit's `fit_mixed_gaussian()`.

Returns a function of a table, which returns phit's fit object (an array before phit 0.6). Chain method: `->lmer()`.

### `glmer()` {#glmer}

```php
glmer(string $formula, string $family = 'binomial', mixed ...$options): Closure
```

Generalised linear mixed model: `$t` |&gt; `glmer('y ~ x + (1 | g)', family: 'binomial')`. Calls phit's `fit_mixed_logistic()`, which fits the binomial family with the logit link only and takes neither as an argument, so they are checked here. A link may be given as link: or as the first positional option.

Returns a function of a table, which returns phit's fit object (an array before phit 0.6). Chain method: `->glmer()`. Throws `InvalidArgumentException` on invalid arguments.
