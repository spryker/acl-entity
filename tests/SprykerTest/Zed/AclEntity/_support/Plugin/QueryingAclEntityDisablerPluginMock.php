<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AclEntity\Plugin;

use Orm\Zed\Acl\Persistence\SpyAclRoleQuery;
use Spryker\Zed\AclEntityExtension\Dependency\Plugin\AclEntityDisablerPluginInterface;

/**
 * Decides through a database query, the way a plugin that looks the acting user up does. Every Propel
 * query asks AclEntity whether it is active, so this re-enters the evaluation that invoked the plugin.
 */
class QueryingAclEntityDisablerPluginMock implements AclEntityDisablerPluginInterface
{
    public int $queryCount = 0;

    public function isDisabled(): bool
    {
        $this->queryCount++;
        SpyAclRoleQuery::create()->findOne();

        return false;
    }
}
