<?php

// Stand-ins for phit's formula model functions under phit 0.3's and phit
// 0.4's names. Each returns its own name and the arguments it received.

declare(strict_types=1);

namespace PhplyrTest\Phit03 {
    function fit_ols(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_lm(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_glm(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_lmer(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_glmer(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }
}

namespace PhplyrTest\Phit04 {
    function fit_ols(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_ols_matrix(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_generalized_linear(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_mixed_gaussian(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_mixed_logistic(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    // phit 0.4 keeps the old names as deprecated aliases.
    function fit_lm(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_glm(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_lmer(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }

    function fit_glmer(mixed ...$args): array
    {
        return [__FUNCTION__, $args];
    }
}
