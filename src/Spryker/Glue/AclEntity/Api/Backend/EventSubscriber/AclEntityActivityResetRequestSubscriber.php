<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\AclEntity\Api\Backend\EventSubscriber;

use Spryker\ApiPlatform\Attribute\ApiType;
use Spryker\Zed\AclEntity\Business\AclEntityFacadeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Resets the memoized {@see AclEntityFacadeInterface::isActive()} answer before anything else runs and
 * again after the identity subscribers published the acting user.
 */
#[ApiType(types: ['backend'])]
class AclEntityActivityResetRequestSubscriber implements EventSubscriberInterface
{
    protected const int PRIORITY_BEFORE_EVERYTHING = 20;

    protected const int PRIORITY_AFTER_IDENTITY = 4;

    public function __construct(protected AclEntityFacadeInterface $aclEntityFacade)
    {
    }

    /**
     * @return array<string, array<array<int|string>>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', static::PRIORITY_BEFORE_EVERYTHING],
                ['onKernelRequest', static::PRIORITY_AFTER_IDENTITY],
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->aclEntityFacade->resetIsActive();
    }
}
