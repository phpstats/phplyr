<?php

$t = DataFrame::fromRows([
    ['id' => 1, 'name' => 'Ada Lovelace', 'tags' => 'math,code'],
    ['id' => 2, 'name' => 'Grace Hopper', 'tags' => 'navy'],
]);

echo phplyr($t)->separateWiderDelim('name', ' ', names: ['first', 'last']);
echo "\n", phplyr($t)->select('id', 'tags')->separateLongerDelim('tags', ',');
echo "\n", phplyr($t)->select('id', 'name')->unite('label', 'id', 'name', sep: ': ');
