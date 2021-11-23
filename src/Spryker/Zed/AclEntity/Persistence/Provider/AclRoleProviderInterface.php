<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Provider;

use Generated\Shared\Transfer\RolesTransfer;

interface AclRoleProviderInterface
{
    /**
     * @return \Generated\Shared\Transfer\RolesTransfer
     */
    public function getCurrentUserAclRoles(): RolesTransfer;
}
