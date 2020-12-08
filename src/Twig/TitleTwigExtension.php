<?php

declare(strict_types=1);

namespace Rector\Website\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TitleTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        $clearTitleFilter = new TwigFilter('clear_title', function (string $title): string {
            $clearTitle = strip_tags($title);

            return str_replace('&nbsp;', ' ', $clearTitle);
        });

        return [$clearTitleFilter];
    }
}
