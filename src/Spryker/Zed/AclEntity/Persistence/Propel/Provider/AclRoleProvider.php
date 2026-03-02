<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\Provider;

use Generated\Shared\Transfer\RolesTransfer;
use Spryker\Zed\AclEntity\Dependency\Facade\AclEntityToAclFacadeBridgeInterface;
use Spryker\Zed\AclEntity\Dependency\Facade\AclEntityToUserFacadeBridgeInterface;

class AclRoleProvider implements AclRoleProviderInterface
{
    /**
     * @var \Spryker\Zed\AclEntity\Dependency\Facade\AclEntityToUserFacadeBridgeInterface
     */
    protected static $userFacade;

    /**
     * @var \Spryker\Zed\AclEntity\Dependency\Facade\AclEntityToAclFacadeBridgeInterface
     */
    protected static $aclFacade;

    /**
     * @var \Spryker\Zed\AclEntity\Persistence\Propel\Provider\AclRoleProviderInterface|null
     */
    protected static $instance;

    /**
     * @var \Generated\Shared\Transfer\RolesTransfer|null
     */
    protected static $cache;

    final protected function __construct(
        AclEntityToUserFacadeBridgeInterface $userFacade,
        AclEntityToAclFacadeBridgeInterface $aclFacade
    ) {
        static::$userFacade = $userFacade;
        static::$aclFacade = $aclFacade;
    }

    public static function getInstance(
        AclEntityToUserFacadeBridgeInterface $userFacade,
        AclEntityToAclFacadeBridgeInterface $aclFacade
    ): AclRoleProviderInterface {
        if (!static::$instance) {
            static::$instance = new static($userFacade, $aclFacade);
        }

        return static::$instance;
    }

    public function getCurrentUserAclRoles(): RolesTransfer
    {
        if (!static::$cache) {
            static::$cache = static::$userFacade->hasCurrentUser()
                ? static::$aclFacade->getUserRoles((int)static::$userFacade->getCurrentUser()->getIdUser())
                : new RolesTransfer();
        }

        return static::$cache;
    }
}
