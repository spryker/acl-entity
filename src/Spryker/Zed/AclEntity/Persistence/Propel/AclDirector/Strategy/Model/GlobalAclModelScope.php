<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Model;

use Generated\Shared\Transfer\AclEntityRuleCollectionTransfer;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Spryker\Shared\AclEntity\AclEntityConstants;

class GlobalAclModelScope implements AclModelScopeInterface
{
    public function isSupported(string $scope): bool
    {
        return $scope === AclEntityConstants::SCOPE_GLOBAL;
    }

    public function isCreatable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool
    {
        return true;
    }

    public function isUpdatable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool
    {
        return true;
    }

    public function isDeletable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool
    {
        return true;
    }

    public function isReadable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool
    {
        return true;
    }
}
