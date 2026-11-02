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

it('reads the first example\'s data from the PHPstats datasets', function () {
    $root = dirname(__DIR__);
    $url = "'https://raw.githubusercontent.com/phpstats'\n    . '/datasets/main/csv'";
    $example = siteManifest()['example'];
    foreach ([file_get_contents("{$root}/README.md"), $example['pipe'], $example['chain']] as $text) {
        expect($text)->toContain($url)->toContain('"$data/penguins.csv"');
    }
    expect($example['setup'])->toBe(['composer require phpstats/phplyr']);
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
