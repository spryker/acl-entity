<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Business;

use Spryker\Service\AclEntity\AclEntityServiceInterface;
use Spryker\Zed\AclEntity\AclEntityDependencyProvider;
use Spryker\Zed\AclEntity\Business\Expander\AclRolesExpander;
use Spryker\Zed\AclEntity\Business\Expander\AclRolesExpanderInterface;
use Spryker\Zed\AclEntity\Business\Filter\AclEntityMetadataConfigFilter;
use Spryker\Zed\AclEntity\Business\Filter\AclEntityMetadataConfigFilterInterface;
use Spryker\Zed\AclEntity\Business\Reader\AclEntityMetadataConfigReader;
use Spryker\Zed\AclEntity\Business\Reader\AclEntityMetadataConfigReaderInterface;
use Spryker\Zed\AclEntity\Business\Reader\AclEntityReader;
use Spryker\Zed\AclEntity\Business\Reader\AclEntityReaderInterface;
use Spryker\Zed\AclEntity\Business\Validator\AclEntityMetadataConfigConsoleValidator;
use Spryker\Zed\AclEntity\Business\Validator\AclEntityMetadataConfigConsoleValidatorInterface;
use Spryker\Zed\AclEntity\Business\Validator\AclEntityMetadataConfigValidator;
use Spryker\Zed\AclEntity\Business\Validator\AclEntityMetadataConfigValidatorInterface;
use Spryker\Zed\AclEntity\Business\Validator\AclEntityRuleValidator;
use Spryker\Zed\AclEntity\Business\Validator\AclEntityRuleValidatorInterface;
use Spryker\Zed\AclEntity\Business\Validator\AclEntitySegmentConnectorValidator;
use Spryker\Zed\AclEntity\Business\Validator\AclEntitySegmentConnectorValidatorInterface;
use Spryker\Zed\AclEntity\Business\Writer\AclEntityRuleWriter;
use Spryker\Zed\AclEntity\Business\Writer\AclEntityRuleWriterInterface;
use Spryker\Zed\AclEntity\Business\Writer\AclEntitySegmentWriter;
use Spryker\Zed\AclEntity\Business\Writer\AclEntitySegmentWriterInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Spryker\Zed\AclEntity\AclEntityConfig getConfig()
 * @method \Spryker\Zed\AclEntity\Persistence\AclEntityEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\AclEntity\Persistence\AclEntityRepositoryInterface getRepository()
 */
class AclEntityBusinessFactory extends AbstractBusinessFactory
{
    public function createAclEntitySegmentWriter(): AclEntitySegmentWriterInterface
    {
        return new AclEntitySegmentWriter(
            $this->getEntityManager(),
            $this->getAclEntityService(),
            $this->createAclEntitySegmentConnectorValidator(),
        );
    }

    public function createAclEntityRuleWriter(): AclEntityRuleWriterInterface
    {
        return new AclEntityRuleWriter(
            $this->getEntityManager(),
            $this->createAclEntityRuleValidator(),
        );
    }

    public function createAclRolesExpander(): AclRolesExpanderInterface
    {
        return new AclRolesExpander(
            $this->getRepository(),
        );
    }

    public function createAclEntityMetadataConfigReader(): AclEntityMetadataConfigReaderInterface
    {
        return new AclEntityMetadataConfigReader(
            $this->getAclEntityMetadataCollectionExpanderPlugins(),
            $this->createAclEntityMetadataConfigValidator(),
            $this->createAclEntityMetadataConfigFilter(),
            $this->getConfig(),
        );
    }

    public function createAclEntityReader(): AclEntityReaderInterface
    {
        return new AclEntityReader(
            $this->getIsAclEntityEnabled(),
            $this->getAclEntityDisablerPlugins(),
        );
    }

    /**
     * @return array<\Spryker\Zed\AclEntityExtension\Dependency\Plugin\AclEntityMetadataConfigExpanderPluginInterface>
     */
    public function getAclEntityMetadataCollectionExpanderPlugins(): array
    {
        return $this->getProvidedDependency(
            AclEntityDependencyProvider::PLUGINS_ACL_ENTITY_METADATA_COLLECTION_EXPANDER,
        );
    }

    /**
     * @return array<\Spryker\Zed\AclEntityExtension\Dependency\Plugin\AclEntityDisablerPluginInterface>
     */
    public function getAclEntityDisablerPlugins(): array
    {
        return $this->getProvidedDependency(
            AclEntityDependencyProvider::PLUGINS_ACL_ENTITY_DISABLER,
        );
    }

    public function getAclEntityService(): AclEntityServiceInterface
    {
        return $this->getProvidedDependency(AclEntityDependencyProvider::SERVICE_ACL_ENTITY);
    }

    public function getIsAclEntityEnabled(): bool
    {
        return $this->getProvidedDependency(AclEntityDependencyProvider::PARAM_IS_ACL_ENTITY_ENABLED);
    }

    public function createAclEntityRuleValidator(): AclEntityRuleValidatorInterface
    {
        return new AclEntityRuleValidator(
            $this->getRepository(),
            $this->createAclEntityMetadataConfigReader(),
        );
    }

    public function createAclEntityMetadataConfigValidator(): AclEntityMetadataConfigValidatorInterface
    {
        return new AclEntityMetadataConfigValidator();
    }

    public function createAclEntityMetadataConfigFilter(): AclEntityMetadataConfigFilterInterface
    {
        return new AclEntityMetadataConfigFilter();
    }

    public function createAclEntitySegmentConnectorValidator(): AclEntitySegmentConnectorValidatorInterface
    {
        return new AclEntitySegmentConnectorValidator($this->getAclEntityService());
    }

    public function createAclEntityMetadataConfigConsoleValidator(): AclEntityMetadataConfigConsoleValidatorInterface
    {
        return new AclEntityMetadataConfigConsoleValidator(
            $this->createAclEntityMetadataConfigReader(),
            $this->createAclEntityMetadataConfigFilter(),
            $this->createAclEntityMetadataConfigValidator(),
        );
    }
}
