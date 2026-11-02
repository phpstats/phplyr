```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, mutate, select, rename};
use function Phplyr\{group_by, summarize, arrange};
use function Phplyr\{desc, head, pivot_wider};
use function Phplyr\{across, where, if_else, col};
use function Phplyr\{n, mean, min, max, ntile, min_rank};
use function Phplyr\{lm, glm, lmer};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
```
