<?php

// Stand-ins for phit's formula model functions. PhplyrTest\Phit04 has the
// names phit 0.4 introduced and phit 0.5 keeps, without the deprecated
// aliases phit 0.5 removed; PhplyrTest\Phit03 has phit 0.3's names. Each
// returns its own name and the arguments it received.

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

}
