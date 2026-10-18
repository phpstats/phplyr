<?php

declare(strict_types=1);

/*
 * The site/ folder read by the phpstats site: the manifest names files that
 * exist, and each vignette runs at its default inputs and returns blocks of
 * the documented types.
 */

use Phibble\Contracts\Table;

function siteManifest(): array
{
    return json_decode(file_get_contents(dirname(__DIR__).'/site/site.json'), true);
}

it('lists files that exist in the site manifest', function () {
    $root = dirname(__DIR__);
    $m = siteManifest();
    expect($m['package'])->toBe('phplyr');
    foreach ([...$m['benchmarks'], ...$m['verifications'], ...array_column($m['vignettes'], 'script')] as $path) {
        expect(is_file("{$root}/{$path}"))->toBeTrue($path);
    }
    foreach ($m['docs']['nav'] as $entry) {
        expect(is_file("{$root}/docs/".substr($entry['path'], 0, -4).'.html'))->toBeTrue($entry['path']);
    }
    foreach ($m['verifications'] as $path) {
        $v = json_decode(file_get_contents("{$root}/{$path}"), true);
        expect($v)->toHaveKeys(['title', 'description', 'reference', 'tolerance', 'cases']);
        expect(array_column($v['cases'], 'passed'))->each->toBeTrue();
    }
});

it('runs the vignette', function (string $slug) {
    $root = dirname(__DIR__);
    $vignette = require "{$root}/site/vignettes/{$slug}.php";
    expect($vignette)->toHaveKeys(['inputs', 'run']);
    $input = array_map(fn (array $spec) => $spec['default'], $vignette['inputs']);

    $blocks = $vignette['run']($input, "{$root}/site/data");

    $types = ['text', 'code', 'metrics', 'table', 'model', 'chart', 'output', 'note'];
    expect($blocks)->not->toBeEmpty();
    foreach ($blocks as $block) {
        expect($types)->toContain($block['type']);
        if ($block['type'] === 'table' && isset($block['data'])) {
            expect($block['data'])->toBeInstanceOf(Table::class);
        }
        if ($block['type'] === 'code') {
            expect($block['chain'])->toContain('phplyr(');
            expect($block['pipe'])->toContain('|>');
        }
    }
})->with(fn () => array_column(siteManifest()['vignettes'], 'slug'));
