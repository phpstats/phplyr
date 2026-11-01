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

it('points the data URLs at the latest release', function () {
    $root = dirname(__DIR__);
    preg_match('/^## (\d+\.\d+\.\d+)/m', file_get_contents("{$root}/CHANGELOG.md"), $c);
    preg_match('/^release: (\S+)$/m', file_get_contents("{$root}/docs/_variables.yml"), $v);
    expect($v[1])->toBe('v'.$c[1]);
    $url = "phplyr/{$v[1]}/docs/data/schools.csv";
    expect(file_get_contents("{$root}/README.md"))->toContain($url);
    expect(implode("\n", siteManifest()['example']['setup']))->toContain($url);
});

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
