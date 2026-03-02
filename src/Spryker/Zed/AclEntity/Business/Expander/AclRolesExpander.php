<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Business\Expander;

use ArrayObject;
use Generated\Shared\Transfer\AclEntityRuleCollectionTransfer;
use Generated\Shared\Transfer\RolesTransfer;
use Generated\Shared\Transfer\RoleTransfer;
use Spryker\Zed\AclEntity\Persistence\AclEntityRepositoryInterface;

class AclRolesExpander implements AclRolesExpanderInterface
{
    /**
     * @var \Spryker\Zed\AclEntity\Persistence\AclEntityRepositoryInterface
     */
    protected $aclEntityRepository;

    public function __construct(AclEntityRepositoryInterface $aclEntityRepository)
    {
        $this->aclEntityRepository = $aclEntityRepository;
    }

    public function expandAclRoles(RolesTransfer $rolesTransfer): RolesTransfer
    {
        $aclEntityRuleCollection = $this->aclEntityRepository->getAclEntityRulesByRoles($rolesTransfer);
        $roleTransfers = $rolesTransfer->getRoles();
        $expandedRoleTransfers = new ArrayObject();

        foreach ($roleTransfers as $roleTransfer) {
            $expandedRoleTransfers->append(
                $this->expandRoleTransferWithAclEntityRules($aclEntityRuleCollection, $roleTransfer),
            );
        }

        return $rolesTransfer->setRoles($expandedRoleTransfers);
    }

    protected function expandRoleTransferWithAclEntityRules(
        AclEntityRuleCollectionTransfer $aclEntityRuleCollection,
        RoleTransfer $roleTransfer
    ): RoleTransfer {
        foreach ($aclEntityRuleCollection->getAclEntityRules() as $aclEntityRuleTransfer) {
            if ($roleTransfer->getIdAclRole() !== $aclEntityRuleTransfer->getIdAclRole()) {
                continue;
            }

            $roleTransfer->addAclEntityRule($aclEntityRuleTransfer);
        }

        return $roleTransfer;
    }
}
