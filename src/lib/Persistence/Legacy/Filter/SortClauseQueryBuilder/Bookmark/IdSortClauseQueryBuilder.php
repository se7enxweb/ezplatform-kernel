<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\SortClauseQueryBuilder\Bookmark;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\BookmarkId;
use eZ\Publish\SPI\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use eZ\Publish\SPI\Repository\Values\Filter\FilteringSortClause;
use eZ\Publish\SPI\Repository\Values\Filter\SortClauseQueryBuilder;

class IdSortClauseQueryBuilder implements SortClauseQueryBuilder
{
    public function accepts(FilteringSortClause $sortClause): bool
    {
        return $sortClause instanceof BookmarkId;
    }

    public function buildQuery(
        FilteringQueryBuilder $queryBuilder,
        FilteringSortClause $sortClause
    ): void {
        /** @var \eZ\Publish\API\Repository\Values\Content\Query\SortClause $sortClause */
        $queryBuilder->addSelect('bookmark.id');
        $queryBuilder->addOrderBy('bookmark.id', $sortClause->direction);
    }
}
