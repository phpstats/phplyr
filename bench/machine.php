<?php

/*
 * The machine a benchmark runs on, for its record: the OS, CPU, the cores the
 * process may use (taskset's pinning on Linux), memory, and PHP.
 */

declare(strict_types=1);

return function (): array {
    $sh = fn (string $cmd) => trim((string) shell_exec($cmd.' 2>/dev/null')) ?: null;
    if (PHP_OS_FAMILY === 'Linux') {
        preg_match('/^model name\s*:\s*(.+)$/m', (string) @file_get_contents('/proc/cpuinfo'), $cpu);
        preg_match('/^MemTotal:\s*(\d+)/m', (string) @file_get_contents('/proc/meminfo'), $mem);
        preg_match('/^PRETTY_NAME="?([^"\n]+)/m', (string) @file_get_contents('/etc/os-release'), $os);

        return [
            'os' => ($os[1] ?? 'Linux').', kernel '.php_uname('r'),
            'cpu' => $cpu[1] ?? null,
            'cores' => ($n = $sh('nproc')) === null ? null : (int) $n,
            'pinned_cpus' => $sh("grep Cpus_allowed_list /proc/self/status | awk '{print \$2}'"),
            'memory_gb' => isset($mem[1]) ? (int) round($mem[1] / 1024 ** 2) : null,
            'php' => PHP_VERSION,
        ];
    }

    return [
        'os' => php_uname('s').' '.php_uname('r'),
        'cpu' => $sh('sysctl -n machdep.cpu.brand_string'),
        'cores' => ($n = $sh('sysctl -n hw.ncpu')) === null ? null : (int) $n,
        'pinned_cpus' => null,
        'memory_gb' => ($m = $sh('sysctl -n hw.memsize')) === null ? null : (int) round((int) $m / 1024 ** 3),
        'php' => PHP_VERSION,
    ];
};
