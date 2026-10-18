<?php

declare(strict_types=1);

use Phibble\DataFrame;

use function Phplyr\{filter, glm, lm, pipe};

function modelsReference(string $key): array
{
    return rReference('models-reference.json')[$key];
}

function expectCoefficients(array $actual, array $expected): void
{
    expect(\count($actual))->toBe(\count($expected));
    foreach ($expected as $i => $value) {
        expectClose($actual[$i], $value, 1e-6 * \max(1.0, abs($value)), 'coefficients');
    }
}

it('explains when fit_lm is missing', function () {
    $t = DataFrame::fromRows([['y' => 1.0, 'x' => 2.0]]);
    expect(fn () => pipe($t, lm('y ~ x')))->toThrow(RuntimeException::class, 'Phit\\fit_lm');
    expect(fn () => pipe($t, glm('y ~ x', family: 'binomial')))->toThrow(RuntimeException::class, 'Phit\\fit_glm');
})->skip(fn () => function_exists('Phit\\fit_lm'), 'fit_lm is available');

it('fits models like R', function () {
    $schools = DataFrame::fromCsv(fixturePath('schools.csv'));
    expectCoefficients((pipe($schools, lm('score ~ study_hours + prior_score + school')))['coefficients'], modelsReference('lm'));
    expectCoefficients(
        (pipe($schools, filter(fn ($r) => $r->prior_score > 60), lm('score ~ study_hours')))['coefficients'],
        modelsReference('lm_filtered'),
    );
    $learning = DataFrame::fromCsv(fixturePath('learning.csv'));
    expectCoefficients(
        (pipe($learning, glm('passed ~ study_hours + practice_score', family: 'binomial')))['coefficients'],
        modelsReference('glm'),
    );
})->skip(fn () => ! function_exists('Phit\\fit_lm'), 'needs phit with fit_lm');
