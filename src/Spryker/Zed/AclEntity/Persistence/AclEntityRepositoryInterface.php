<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence;

use Generated\Shared\Transfer\AclEntityRuleCollectionTransfer;
use Generated\Shared\Transfer\AclEntityRuleCriteriaTransfer;
use Generated\Shared\Transfer\AclEntitySegmentCollectionTransfer;
use Generated\Shared\Transfer\AclEntitySegmentCriteriaTransfer;
use Generated\Shared\Transfer\RolesTransfer;

interface AclEntityRepositoryInterface
{
    public function getAclEntityRulesByRoles(RolesTransfer $rolesTransfer): AclEntityRuleCollectionTransfer;

    public function getAclEntityRuleCollection(AclEntityRuleCriteriaTransfer $aclEntityRuleCriteriaTransfer): AclEntityRuleCollectionTransfer;

    public function getAclEntitySegmentCollection(
        AclEntitySegmentCriteriaTransfer $aclEntitySegmentCriteriaTransfer
    ): AclEntitySegmentCollectionTransfer;
}
