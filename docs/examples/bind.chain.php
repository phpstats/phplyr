<?php

$a = DataFrame::fromRows([['id' => 1, 'v' => 'a']]);
$b = DataFrame::fromRows([['id' => 2, 'w' => true]]);

echo phplyr($a)->bindRows($b, id: 'source');
echo "\n", phplyr($a)->bindCols(DataFrame::fromRows([['id' => 10]]));
