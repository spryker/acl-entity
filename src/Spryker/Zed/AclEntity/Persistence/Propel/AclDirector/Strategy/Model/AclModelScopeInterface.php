<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Model;

use Generated\Shared\Transfer\AclEntityRuleCollectionTransfer;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;

interface AclModelScopeInterface
{
    public function isSupported(string $scope): bool;

    public function isCreatable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool;

    public function isUpdatable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool;

    public function isDeletable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool;

    public function isReadable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool;
}
