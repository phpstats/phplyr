<?php

declare(strict_types=1);

/*
 * The site/ folder read by the phpstats site: the manifest names files and
 * pages that exist.
 */

function siteManifest(): array
{
    return json_decode(file_get_contents(dirname(__DIR__).'/site/site.json'), true);
}

it('lists files that exist in the site manifest', function () {
    $root = dirname(__DIR__);
    $m = siteManifest();
    expect($m['package'])->toBe('phplyr');
    foreach ([...$m['benchmarks'], ...$m['verifications']] as $path) {
        expect(is_file("{$root}/{$path}"))->toBeTrue($path);
    }
    foreach ($m['docs']['nav'] as $entry) {
        expect(is_file("{$root}/docs/".substr($entry['path'], 0, -4).'.html'))->toBeTrue($entry['path']);
    }
    foreach ($m['examples'] as $example) {
        expect(is_file("{$root}/docs/{$example['path']}.html"))->toBeTrue($example['path']);
    }
    foreach ($m['verifications'] as $path) {
        $v = json_decode(file_get_contents("{$root}/{$path}"), true);
        expect($v)->toHaveKeys(['title', 'description', 'reference', 'tolerance', 'cases']);
        expect(array_column($v['cases'], 'passed'))->each->toBeTrue();
    }
});
