<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Filter;

use Generated\Shared\Transfer\AclEntityRuleCollectionTransfer;

interface AclEntityRuleCollectionTransferFilterInterface
{
    public function filterByScope(
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        string $scope
    ): AclEntityRuleCollectionTransfer;

    public function filterByEntityClass(
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        string $entityClass
    ): AclEntityRuleCollectionTransfer;

    public function filterByScopeAndEntityClass(
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        string $scope,
        string $entityClass
    ): AclEntityRuleCollectionTransfer;

    public function filterByScopeEntityClassAndPermissionMask(
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        string $scope,
        string $entityClass,
        int $permissionMask
    ): AclEntityRuleCollectionTransfer;

    public function filterByEntityClassAndPermissionMask(
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        string $entityClass,
        int $permissionMask
    ): AclEntityRuleCollectionTransfer;

    public function filterByPermissionMask(
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        int $permissionMask
    ): AclEntityRuleCollectionTransfer;
}
