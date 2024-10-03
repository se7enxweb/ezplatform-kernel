<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository;

use Exception;
use eZ\Publish\API\Repository\BookmarkService as BookmarkServiceInterface;
use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Repository as RepositoryInterface;
use eZ\Publish\API\Repository\Values\Bookmark\BookmarkList;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Filter\Filter;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\SPI\Persistence\Bookmark\CreateStruct;
use eZ\Publish\SPI\Persistence\Bookmark\Handler as BookmarkHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class BookmarkService implements BookmarkServiceInterface
{
    /** @var \eZ\Publish\API\Repository\Repository */
    protected $repository;

    /** @var \eZ\Publish\SPI\Persistence\Bookmark\Handler */
    protected $bookmarkHandler;

    private LoggerInterface $logger;

    /**
     * BookmarkService constructor.
     */
    public function __construct(RepositoryInterface $repository, BookmarkHandler $bookmarkHandler, LoggerInterface $logger = null)
    {
        $this->repository = $repository;
        $this->bookmarkHandler = $bookmarkHandler;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function createBookmark(Location $location): void
    {
        $loadedLocation = $this->repository->getLocationService()->loadLocation($location->id);

        if ($this->isBookmarked($loadedLocation)) {
            throw new InvalidArgumentException('$location', 'Location is already bookmarked.');
        }

        $createStruct = new CreateStruct();
        $createStruct->name = $loadedLocation->contentInfo->name;
        $createStruct->locationId = $loadedLocation->id;
        $createStruct->userId = $this->getCurrentUserId();

        $this->repository->beginTransaction();
        try {
            $this->bookmarkHandler->create($createStruct);
            $this->repository->commit();
        } catch (Exception $ex) {
            $this->repository->rollback();
            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteBookmark(Location $location): void
    {
        $loadedLocation = $this->repository->getLocationService()->loadLocation($location->id);

        $bookmarks = $this->bookmarkHandler->loadByUserIdAndLocationId(
            $this->getCurrentUserId(),
            [$loadedLocation->id]
        );

        if (empty($bookmarks)) {
            throw new InvalidArgumentException('$location', 'Location is not bookmarked.');
        }

        $this->repository->beginTransaction();
        try {
            $this->bookmarkHandler->delete(reset($bookmarks)->id);
            $this->repository->commit();
        } catch (Exception $ex) {
            $this->repository->rollback();
            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadBookmarks(int $offset = 0, int $limit = 25): BookmarkList
    {
        $currentUserId = $this->getCurrentUserId();

        $filter = new Filter();
        try {
            $filter
                ->withCriterion(new Criterion\IsBookmarked($currentUserId))
                ->withSortClause(new SortClause\BookmarkId(Query::SORT_DESC))
                ->sliceBy($limit, $offset);

            $result = $this->repository->getlocationService()->find($filter, []);
        } catch (BadStateException $e) {
            $this->logger->debug($e);

            return new BookmarkList();
        }

        $list = new BookmarkList();
        $list->totalCount = $result->totalCount;
        $list->items = $result->locations;

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function isBookmarked(Location $location): bool
    {
        $bookmarks = $this->bookmarkHandler->loadByUserIdAndLocationId(
            $this->getCurrentUserId(),
            [$location->id]
        );

        return !empty($bookmarks);
    }

    private function getCurrentUserId(): int
    {
        return $this->repository
            ->getPermissionResolver()
            ->getCurrentUserReference()
            ->getUserId();
    }
}
