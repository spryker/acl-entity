<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity;

use Spryker\Zed\AclEntity\Dependency\Facade\AclEntityToAclFacadeBridge;
use Spryker\Zed\AclEntity\Dependency\Facade\AclEntityToUserFacadeBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\AclEntity\AclEntityConfig getConfig()
 */
class AclEntityDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_USER = 'FACADE_USER';
    public const FACADE_ACL = 'FACADE_ACL';

    public const SERVICE_ACL_ENTITY = 'SERVICE_ACL_ENTITY';

    public const PLUGINS_ACL_ENTITY_METADATA_COLLECTION_EXPANDER = 'PLUGINS_ACL_ENTITY_METADATA_COLLECTION_EXPANDER';
    public const PLUGINS_ACL_ENTITY_DISABLER = 'PLUGINS_ACL_ENTITY_DISABLER';
    public const PLUGINS_ACL_ENTITY_ENABLER = 'PLUGINS_ACL_ENTITY_ENABLER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addAclEntityMetadataCollectionExpanderPlugins($container);
        $container = $this->addAclEntityDisablerPlugins($container);
        $container = $this->addAclEntityEnablerPlugins($container);
        $container = $this->addAclEntityService($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container): Container
    {
        $container = parent::providePersistenceLayerDependencies($container);

        $container = $this->addUserFacade($container);
        $container = $this->addAclFacade($container);
        $container = $this->addAclEntityService($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUserFacade(Container $container): Container
    {
        $container->set(static::FACADE_USER, function (Container $container) {
            return new AclEntityToUserFacadeBridge($container->getLocator()->user()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAclFacade(Container $container): Container
    {
        $container->set(static::FACADE_ACL, function (Container $container) {
            return new AclEntityToAclFacadeBridge($container->getLocator()->acl()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAclEntityService(Container $container): Container
    {
        $container->set(static::SERVICE_ACL_ENTITY, function (Container $container) {
            return $container->getLocator()->aclEntity()->service();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAclEntityMetadataCollectionExpanderPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_ACL_ENTITY_METADATA_COLLECTION_EXPANDER, function () {
            return $this->getAclEntityMetadataCollectionExpanderPlugins();
        });

        return $container;
    }

    /**
     * @return \Spryker\Zed\AclEntityExtension\Dependency\Plugin\AclEntityMetadataConfigExpanderPluginInterface[]
     */
    protected function getAclEntityMetadataCollectionExpanderPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAclEntityDisablerPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_ACL_ENTITY_DISABLER, function () {
            return $this->getAclEntityDisablerPlugins();
        });

        return $container;
    }

    /**
     * @return \Spryker\Zed\AclEntityExtension\Dependency\Plugin\AclEntityDisablerPluginInterface[]
     */
    protected function getAclEntityDisablerPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAclEntityEnablerPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_ACL_ENTITY_ENABLER, function () {
            return $this->getAclEntityEnablerPlugins();
        });

        return $container;
    }

    /**
     * @return \Spryker\Zed\AclEntityExtension\Dependency\Plugin\AclEntityEnablerPluginInterface[]
     */
    protected function getAclEntityEnablerPlugins(): array
    {
        return [];
    }
}
