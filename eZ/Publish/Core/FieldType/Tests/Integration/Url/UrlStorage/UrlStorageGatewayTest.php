<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Tests\Integration\Url\UrlStorage;

use eZ\Publish\Core\FieldType\Tests\Integration\BaseCoreFieldTypeIntegrationTest;
use eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway;

/**
 * Url Field Type external storage gateway tests.
 */
abstract class UrlStorageGatewayTest extends BaseCoreFieldTypeIntegrationTest
{
    abstract protected function getGateway(): Gateway;

    /**
     * @covers \eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage::getUrlsFromUrlLink
     */
    public function testGetUrlsFromUrlLink()
    {
        $gateway = $this->getGateway();

        $urlIds = [];
        $urlIds[] = $gateway->insertUrl('https://ibexa.co/example1');
        $urlIds[] = $gateway->insertUrl('https://ibexa.co/example2');
        $urlIds[] = $gateway->insertUrl('https://ibexa.co/example3');

        $gateway->linkUrl($urlIds[0], 10, 1);
        $gateway->linkUrl($urlIds[1], 10, 1);
        $gateway->linkUrl($urlIds[1], 12, 2);
        $gateway->linkUrl($urlIds[2], 14, 1);

        self::assertEquals(['https://ibexa.co/example1' => true, 'https://ibexa.co/example2' => true], $gateway->getUrlsFromUrlLink(10, 1), 'Did not get expected urlS for field 10');
        self::assertEquals(['https://ibexa.co/example2' => true], $gateway->getUrlsFromUrlLink(12, 2), 'Did not get expected url for field 12');
        self::assertEquals(['https://ibexa.co/example3' => true], $gateway->getUrlsFromUrlLink(14, 1), 'Did not get expected url for field 14');
        self::assertEquals([], $gateway->getUrlsFromUrlLink(15, 1), 'Expected no urls for field 15');
    }
}
