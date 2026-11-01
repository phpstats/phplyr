<?php

declare(strict_types=1);

use Phibble\DataFrame;

use Phplyr\Internal;

use function Phplyr\{filter, glm, glmer, lm, lmer, pipe};

function modelsReference(string $key): array
{
    return rReference('models-reference.json')[$key];
}

function expectCoefficients(array $actual, array $expected): void
{
    expect(\count($actual))->toBe(\count($expected));
    foreach ($expected as $i => $value) {
        expectClose($actual[$i], $value, 1e-6, 1e-6, 'coefficients');
    }
}

it('explains when phit is missing', function () {
    $t = DataFrame::fromRows([['y' => 1.0, 'x' => 2.0]]);
    expect(fn () => pipe($t, lm('y ~ x')))->toThrow(RuntimeException::class, 'Phit\\fit_ols()');
    expect(fn () => pipe($t, glm('y ~ x', family: 'binomial')))->toThrow(RuntimeException::class, 'Phit\\fit_generalized_linear()');
})->skip(fn () => function_exists('Phit\\fit_lm'), 'phit is available');

require_once __DIR__.'/Fixtures/phit-stubs.php';

describe('phit function names', function () {
    afterEach(fn () => Internal::$phitNamespace = 'Phit\\');

    $t = fn () => DataFrame::fromRows([['y' => 1.0, 'x' => 2.0, 'g' => 'a']]);

    it('calls the phit 0.4 names when phit has them', function () use ($t) {
        Internal::$phitNamespace = 'PhplyrTest\\Phit04\\';
        $data = $t();

        expect(pipe($data, lm('y ~ x', weights: [1.0])))->toBe(['PhplyrTest\\Phit04\\fit_ols', ['y ~ x', $data, 'weights' => [1.0]]]);
        expect(pipe($data, glm('y ~ x', family: 'poisson')))->toBe(['PhplyrTest\\Phit04\\fit_generalized_linear', ['y ~ x', $data, 'poisson']]);
        expect(pipe($data, lmer('y ~ x + (1 | g)', reml: false)))->toBe(['PhplyrTest\\Phit04\\fit_mixed_gaussian', ['y ~ x + (1 | g)', $data, 'reml' => false]]);
        expect(pipe($data, glmer('y ~ x + (1 | g)', link: 'logit', quadraturePoints: 5)))
            ->toBe(['PhplyrTest\\Phit04\\fit_mixed_logistic', ['y ~ x + (1 | g)', $data, 'quadraturePoints' => 5]]);
        expect(pipe($data, glmer('y ~ x + (1 | g)', 'binomial', 'logit')))
            ->toBe(['PhplyrTest\\Phit04\\fit_mixed_logistic', ['y ~ x + (1 | g)', $data]]);
    });

    it('rejects a family or link that fit_mixed_logistic cannot fit', function () use ($t) {
        Internal::$phitNamespace = 'PhplyrTest\\Phit04\\';
        expect(fn () => pipe($t(), glmer('y ~ x + (1 | g)', family: 'poisson')))->toThrow(InvalidArgumentException::class, "family 'binomial'");
        expect(fn () => pipe($t(), glmer('y ~ x + (1 | g)', link: 'probit')))->toThrow(InvalidArgumentException::class, "link 'logit'");
    });

    it('calls the phit 0.3 names before phit 0.4', function () use ($t) {
        // phit 0.3's fit_ols is the matrix fit, so lm() must not call it.
        Internal::$phitNamespace = 'PhplyrTest\\Phit03\\';
        $data = $t();

        expect(pipe($data, lm('y ~ x')))->toBe(['PhplyrTest\\Phit03\\fit_lm', ['y ~ x', $data]]);
        expect(pipe($data, glm('y ~ x', family: 'binomial')))->toBe(['PhplyrTest\\Phit03\\fit_glm', ['y ~ x', $data, 'binomial']]);
        expect(pipe($data, lmer('y ~ x + (1 | g)')))->toBe(['PhplyrTest\\Phit03\\fit_lmer', ['y ~ x + (1 | g)', $data]]);
        expect(pipe($data, glmer('y ~ x + (1 | g)', link: 'logit')))->toBe(['PhplyrTest\\Phit03\\fit_glmer', ['y ~ x + (1 | g)', $data, 'binomial', 'link' => 'logit']]);
    });
});

it('fits models like R', function (string $key, Closure $fit) {
    expectCoefficients($fit()['coefficients'], modelsReference($key));
})->with([
    'lm(score ~ study_hours + prior_score + school) on schools' => ['lm', fn () => pipe(DataFrame::fromCsv(fixturePath('schools.csv')), lm('score ~ study_hours + prior_score + school'))],
    'lm(score ~ study_hours) on schools with prior_score > 60' => ['lm_filtered', fn () => pipe(DataFrame::fromCsv(fixturePath('schools.csv')), filter(fn ($r) => $r->prior_score > 60), lm('score ~ study_hours'))],
    'glm(passed ~ study_hours + practice_score, binomial) on learning' => ['glm', fn () => pipe(DataFrame::fromCsv(fixturePath('learning.csv')), glm('passed ~ study_hours + practice_score', family: 'binomial'))],
])->group('verify:models')->skip(fn () => ! function_exists('Phit\\fit_lm'), 'needs phit with fit_lm');
