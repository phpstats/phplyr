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
});

it('has an API reference that matches the public functions', function () {
    exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg(dirname(__DIR__).'/bin/api-reference').' --check 2>&1', $out, $status);
    expect($status)->toBe(0, implode("\n", $out));
});

it('writes verification records in the site\'s evidence format', function () {
    $root = dirname(__DIR__);
    foreach (siteManifest()['verifications'] as $path) {
        $v = json_decode(file_get_contents("{$root}/{$path}"), true);
        expect($v)->toHaveKeys(['title', 'kind', 'checked', 'reference', 'comparison', 'cases', 'reproduce', 'environment']);
        $rules = array_column($v['comparison'], 'id');
        foreach ($v['cases'] as $case) {
            expect($case['status'])->toBeIn(['pass', 'fail', 'skipped']);
            expect($case['rule'])->toBeIn($rules);
            if ($case['status'] !== 'pass') {
                expect($case['reason'] ?? '')->not->toBe('', "{$path}: {$case['name']}");
            }
        }
    }
});

it('writes benchmark records in the site\'s evidence format', function () {
    $root = dirname(__DIR__);
    foreach (siteManifest()['benchmarks'] as $path) {
        $b = json_decode(file_get_contents("{$root}/{$path}"), true);
        expect($b)->toHaveKeys(['title', 'unit', 'statistic', 'implementations', 'time_spent_in', 'environment', 'rows', 'reproduce', 'source']);
        $measured = array_filter($b['rows'], fn ($r) => isset($r['peak_memory_mib']));
        expect($measured === [] || isset($b['memory_measure']))->toBeTrue($path);
        foreach ($b['rows'] as $row) {
            expect($row)->toHaveKeys(['case', 'implementation', 'value']);
            expect(array_keys($b['implementations']))->toContain($row['implementation']);
            expect($row['peak_memory_mib'] ?? null)->not->toBe(0);
        }
    }
});
