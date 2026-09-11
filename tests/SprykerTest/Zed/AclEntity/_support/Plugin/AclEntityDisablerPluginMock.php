<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AclEntity\Plugin;

use Spryker\Zed\AclEntityExtension\Dependency\Plugin\AclEntityDisablerPluginInterface;

class AclEntityDisablerPluginMock implements AclEntityDisablerPluginInterface
{
    public function __construct(protected bool $isDisabled)
    {
    }

    public function setIsDisabled(bool $isDisabled): void
    {
        $this->isDisabled = $isDisabled;
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }
}
