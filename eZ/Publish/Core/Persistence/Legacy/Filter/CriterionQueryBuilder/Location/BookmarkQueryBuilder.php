<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location;

use Doctrine\DBAL\ParameterType;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\IsBookmarked;
use eZ\Publish\Core\Persistence\Legacy\Bookmark\Gateway\DoctrineDatabase;
use eZ\Publish\SPI\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use eZ\Publish\SPI\Repository\Values\Filter\FilteringCriterion;

/**
 * @internal for internal use by Repository Filtering
 */
final class BookmarkQueryBuilder extends BaseLocationCriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof IsBookmarked;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        $queryBuilder
            ->joinOnce(
                'location',
                DoctrineDatabase::TABLE_BOOKMARKS,
                'bookmark',
                'location.node_id = bookmark.node_id'
            );

        return $queryBuilder->expr()->eq(
            'bookmark.user_id',
            $queryBuilder->createNamedParameter(
                (int)$criterion->value[0],
                ParameterType::INTEGER
            )
        );
    }
}
