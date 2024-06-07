<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Tests\Filter\CriterionQueryBuilder\Location;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location\BookmarkQueryBuilder;
use eZ\Publish\Core\Persistence\Legacy\Tests\Filter\BaseCriterionVisitorQueryBuilderTestCase;

/**
 * @covers \eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location\BookmarkQueryBuilder::buildQueryConstraint
 * @covers \eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location\BookmarkQueryBuilder::accepts
 */
final class BookmarkQueryBuilderTest extends BaseCriterionVisitorQueryBuilderTestCase
{
    public function getFilteringCriteriaQueryData(): iterable
    {
        yield 'Bookmarks locations for user_id=14' => [
            new Criterion\IsBookmarked(14),
            'bookmark.user_id = :dcValue1',
            ['dcValue1' => 14],
        ];

        yield 'Bookmarks locations for user_id=14 OR user_id=7' => [
            new Criterion\LogicalOr(
                [
                    new Criterion\IsBookmarked(14),
                    new Criterion\IsBookmarked(7),
                ]
            ),
            '(bookmark.user_id = :dcValue1) OR (bookmark.user_id = :dcValue2)',
            ['dcValue1' => 14, 'dcValue2' => 7],
        ];
    }

    protected function getCriterionQueryBuilders(): iterable
    {
        return [new BookmarkQueryBuilder()];
    }
}
