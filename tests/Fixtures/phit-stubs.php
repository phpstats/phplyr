<?php

// Stand-ins for phit's formula model functions. Each returns its own name
// and the arguments it received.

declare(strict_types=1);

namespace PhplyrTest\Phit {
    function fit_ols(mixed ...$args): array
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
