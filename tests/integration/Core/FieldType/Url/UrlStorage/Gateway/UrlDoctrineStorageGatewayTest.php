<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\integration\Core\FieldType\Url\UrlStorage\Gateway;

use eZ\Publish\Core\FieldType\Tests\Integration\Url\UrlStorage\UrlStorageGatewayTest;
use eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway as UrlStorageGateway;
use eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage;

final class UrlDoctrineStorageGatewayTest extends UrlStorageGatewayTest
{
    protected function getGateway(): UrlStorageGateway
    {
        return new DoctrineStorage($this->getDatabaseConnection());
    }
}
