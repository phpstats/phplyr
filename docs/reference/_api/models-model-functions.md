### `lm()` {#lm}

```php
lm(string $formula, mixed ...$options): Closure
```

Linear regression: `$t` |&gt; `lm('y ~ a + b')`. Calls phit's fit_ols (fit_lm before phit 0.4).

Returns a function of a table, which returns phit's result array. Chain method: `->lm()`. [Example](#results).

### `glm()` {#glm}

```php
glm(string $formula, string $family = 'gaussian', mixed ...$options): Closure
```

Generalised linear model: `$t` |&gt; `glm('y ~ x', family: 'binomial')`. Calls phit's fit_generalized_linear (fit_glm before phit 0.4).

Returns a function of a table, which returns phit's result array. Chain method: `->glm()`. [Example](#results).

### `lmer()` {#lmer}

```php
lmer(string $formula, mixed ...$options): Closure
```

Linear mixed model with lme4-style random effects: `$t` |&gt; `lmer('score ~ study_hours + (1 | school)')`. REML by default (reml: false for maximum likelihood). Calls phit's fit_mixed_gaussian (fit_lmer before phit 0.4).

Returns a function of a table, which returns phit's result array. Chain method: `->lmer()`.

### `glmer()` {#glmer}

```php
glmer(string $formula, string $family = 'binomial', mixed ...$options): Closure
```

Generalised linear mixed model: `$t` |&gt; `glmer('y ~ x + (1 | g)', family: 'binomial')`. Calls phit's fit_mixed_logistic (fit_glmer before phit 0.4). Both fit the binomial family with the logit link only. fit_mixed_logistic takes no family or link, so they are checked here.

Returns a function of a table, which returns phit's result array. Chain method: `->glmer()`. Throws `InvalidArgumentException` on invalid arguments.
