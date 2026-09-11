<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AclEntity\Plugin\Application;

use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface;

class AclEntityApplicationPlugin extends AbstractPlugin implements ApplicationPluginInterface
{
    /**
     * @uses \Spryker\Zed\AclEntity\AclEntityDependencyProvider::IS_ACL_ENTITY_ENABLED
     */
    protected const string IS_ACL_ENTITY_ENABLED = 'IS_ACL_ENTITY_ENABLED';

    /**
     * {@inheritDoc}
     * - Enables AclEntity for the Glue application, the way the Zed plugin does for the Merchant Portal.
     *
     * @api
     *
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    public function provide(ContainerInterface $container): ContainerInterface
    {
        $container->set(static::IS_ACL_ENTITY_ENABLED, true);
        $container->configure(static::IS_ACL_ENTITY_ENABLED, ['isGlobal' => true]);

        return $container;
    }
}
