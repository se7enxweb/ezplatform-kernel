<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content;

use Doctrine\DBAL\ParameterType;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ObjectStateId;
use eZ\Publish\Core\Persistence\Legacy\Content\ObjectState\Gateway;
use eZ\Publish\SPI\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use eZ\Publish\SPI\Repository\Values\Filter\CriterionQueryBuilder;
use eZ\Publish\SPI\Repository\Values\Filter\FilteringCriterion;

/**
 * @internal for internal use by Repository Filtering
 */
final class ObjectStateIdQueryBuilder implements CriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof ObjectStateId;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        $value = (array)$criterion->value;

        $conditions = [];
        foreach ($value as $objectStateId) {
            $conditions[] = $queryBuilder->expr()->eq(
                'osl.contentobject_state_id',
                $queryBuilder->createNamedParameter($objectStateId, ParameterType::INTEGER)
            );
        }

        var_dump(
            __METHOD__,
            $value,
            (string) $queryBuilder->expr()->and(
                $queryBuilder->expr()->eq('content.id', 'osl.contentobject_id'),
                $queryBuilder->expr()->and(...$conditions),
            )
        );

        /** @var \eZ\Publish\API\Repository\Values\Content\Query\Criterion\ObjectStateId $criterion */
        $queryBuilder
            ->join(
                'content',
                Gateway::OBJECT_STATE_LINK_TABLE,
                'osl',
                (string) $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('content.id', 'osl.contentobject_id'),
                    $queryBuilder->expr()->or(...$conditions),
                ),
            );


        return $queryBuilder->expr()->isNotNull(
            'osl.contentobject_state_id',
        );
    }
}
