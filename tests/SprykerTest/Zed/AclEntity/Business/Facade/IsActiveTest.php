<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AclEntity\Business\Facade;

use Codeception\Test\Unit;
use ReflectionProperty;
use Spryker\Zed\AclEntity\AclEntityDependencyProvider;
use Spryker\Zed\AclEntity\Business\AclEntityFacade;
use SprykerTest\Zed\AclEntity\AclEntityBusinessTester;
use SprykerTest\Zed\AclEntity\Plugin\AclEntityDisablerPluginMock;
use SprykerTest\Zed\AclEntity\Plugin\QueryingAclEntityDisablerPluginMock;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AclEntity
 * @group Business
 * @group Facade
 * @group IsActiveTest
 * Add your own group annotations below this line
 */
class IsActiveTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\AclEntity\AclEntityBusinessTester
     */
    protected AclEntityBusinessTester $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetIsActiveCache();
        $this->tester->setDependency(AclEntityDependencyProvider::PARAM_IS_ACL_ENTITY_ENABLED, true);
    }

    protected function tearDown(): void
    {
        $this->resetIsActiveCache();

        parent::tearDown();
    }

    public function testIsActiveMemoizesTheAnswerForTheWholeProcess(): void
    {
        // Arrange
        $aclEntityDisablerPluginMock = new AclEntityDisablerPluginMock(true);
        $this->tester->setDependency(
            AclEntityDependencyProvider::PLUGINS_ACL_ENTITY_DISABLER,
            [$aclEntityDisablerPluginMock],
        );

        // Assert
        $this->assertFalse($this->tester->getFacade()->isActive());

        // Act
        $aclEntityDisablerPluginMock->setIsDisabled(false);

        // Assert
        $this->assertFalse($this->tester->getFacade()->isActive());
    }

    public function testResetIsActiveMakesTheNextCallAskThePluginsAgain(): void
    {
        // Arrange
        $aclEntityDisablerPluginMock = new AclEntityDisablerPluginMock(true);
        $this->tester->setDependency(
            AclEntityDependencyProvider::PLUGINS_ACL_ENTITY_DISABLER,
            [$aclEntityDisablerPluginMock],
        );
        $this->assertFalse($this->tester->getFacade()->isActive());
        $aclEntityDisablerPluginMock->setIsDisabled(false);

        // Act
        $this->tester->getFacade()->resetIsActive();

        // Assert
        $this->assertTrue($this->tester->getFacade()->isActive());
    }

    public function testResetIsActivePicksUpDeactivationAsWell(): void
    {
        // Arrange
        $aclEntityDisablerPluginMock = new AclEntityDisablerPluginMock(false);
        $this->tester->setDependency(
            AclEntityDependencyProvider::PLUGINS_ACL_ENTITY_DISABLER,
            [$aclEntityDisablerPluginMock],
        );
        $this->assertTrue($this->tester->getFacade()->isActive());
        $aclEntityDisablerPluginMock->setIsDisabled(true);

        // Act
        $this->tester->getFacade()->resetIsActive();

        // Assert
        $this->assertFalse($this->tester->getFacade()->isActive());
    }

    public function testIsActiveDoesNotRecurseWhenADisablerPluginQueriesTheDatabase(): void
    {
        // Arrange
        $queryingAclEntityDisablerPluginMock = new QueryingAclEntityDisablerPluginMock();
        $this->tester->setDependency(
            AclEntityDependencyProvider::PLUGINS_ACL_ENTITY_DISABLER,
            [$queryingAclEntityDisablerPluginMock],
        );

        // Act
        $isActive = $this->tester->getFacade()->isActive();

        // Assert
        $this->assertTrue($isActive);
        $this->assertSame(1, $queryingAclEntityDisablerPluginMock->queryCount);
    }

    protected function resetIsActiveCache(): void
    {
        (new ReflectionProperty(AclEntityFacade::class, 'isActive'))->setValue(null, null);
    }
}
