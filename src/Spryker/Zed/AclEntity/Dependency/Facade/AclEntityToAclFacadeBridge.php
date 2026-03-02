<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Dependency\Facade;

use Generated\Shared\Transfer\RolesTransfer;

class AclEntityToAclFacadeBridge implements AclEntityToAclFacadeBridgeInterface
{
    /**
     * @var \Spryker\Zed\Acl\Business\AclFacadeInterface
     */
    protected $aclFacade;

    /**
     * @param \Spryker\Zed\Acl\Business\AclFacadeInterface $aclFacade
     */
    public function __construct($aclFacade)
    {
        $this->aclFacade = $aclFacade;
    }

    public function getUserRoles(int $idUser): RolesTransfer
    {
        return $this->aclFacade->getUserRoles($idUser);
    }
}
