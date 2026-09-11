<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\AclEntity\BackendApi;

use Codeception\Test\Unit;
use Spryker\Glue\AclEntity\Api\Backend\EventSubscriber\AclEntityActivityResetRequestSubscriber;
use Spryker\Zed\AclEntity\Business\AclEntityFacadeInterface;
use SprykerTest\Glue\AclEntity\AclEntityBackendApiTester;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group AclEntity
 * @group BackendApi
 * @group AclEntityActivityResetRequestSubscriberTest
 * Add your own group annotations below this line
 */
class AclEntityActivityResetRequestSubscriberTest extends Unit
{
    /**
     * @var \SprykerTest\Glue\AclEntity\AclEntityBackendApiTester
     */
    protected AclEntityBackendApiTester $tester;

    public function testSubscribesToKernelRequestBeforeEverythingAndAfterTheIdentitySubscribers(): void
    {
        // Act
        $subscribedEvents = AclEntityActivityResetRequestSubscriber::getSubscribedEvents();

        // Assert
        $this->assertSame(
            [[['onKernelRequest', 20], ['onKernelRequest', 4]]],
            [$subscribedEvents[KernelEvents::REQUEST]],
        );
    }

    public function testOnKernelRequestResetsTheMemoizedActivityState(): void
    {
        // Arrange
        $aclEntityFacadeMock = $this->createMock(AclEntityFacadeInterface::class);
        $aclEntityFacadeMock->expects($this->once())->method('resetIsActive');

        // Act
        (new AclEntityActivityResetRequestSubscriber($aclEntityFacadeMock))->onKernelRequest(
            $this->createRequestEvent(HttpKernelInterface::MAIN_REQUEST),
        );
    }

    public function testOnKernelRequestDoesNothingForSubRequests(): void
    {
        // Arrange
        $aclEntityFacadeMock = $this->createMock(AclEntityFacadeInterface::class);
        $aclEntityFacadeMock->expects($this->never())->method('resetIsActive');

        // Act
        (new AclEntityActivityResetRequestSubscriber($aclEntityFacadeMock))->onKernelRequest(
            $this->createRequestEvent(HttpKernelInterface::SUB_REQUEST),
        );
    }

    protected function createRequestEvent(int $requestType): RequestEvent
    {
        return new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            Request::create('/token'),
            $requestType,
        );
    }
}
