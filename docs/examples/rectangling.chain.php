<?php

use function Phplyr\chop;

$people = DataFrame::fromColumns([
    'id' => [1, 2, 3],
    'info' => [
        ['name' => 'ann', 'langs' => ['php', 'r'], 'city' => ['name' => 'Oslo']],
        ['name' => 'bob', 'langs' => ['sql']],
        null,
    ],
]);

echo phplyr($people)->hoist('info', town: ['city', 'name']), "\n";

echo phplyr($people)->unnestWider('info')->select('id', 'langs')->unnestLonger('langs', indicesInclude: true), "\n";

echo phplyr(DataFrame::fromColumns(['g' => ['a', 'a', 'b'], 'x' => [1, 2, 3]]))->chop('x');
