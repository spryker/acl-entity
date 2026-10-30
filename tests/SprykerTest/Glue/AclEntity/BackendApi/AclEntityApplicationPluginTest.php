<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\AclEntity\BackendApi;

use Codeception\Test\Unit;
use Spryker\Glue\AclEntity\Plugin\Application\AclEntityApplicationPlugin;
use Spryker\Zed\AclEntity\AclEntityDependencyProvider;
use SprykerTest\Glue\AclEntity\AclEntityBackendApiTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group AclEntity
 * @group BackendApi
 * @group AclEntityApplicationPluginTest
 * Add your own group annotations below this line
 */
class AclEntityApplicationPluginTest extends Unit
{
    protected AclEntityBackendApiTester $tester;

    public function testProvideEnablesAclEntityInTheApplicationContainer(): void
    {
        // Arrange
        // The plugin makes the flag a global service, which outlives this container; the
        // helper's container resets it after the test, so later suites run with AclEntity off.
        $container = $this->tester->getContainer();

        // Act
        $container = (new AclEntityApplicationPlugin())->provide($container);

        // Assert
        $this->assertTrue($container->has(AclEntityDependencyProvider::IS_ACL_ENTITY_ENABLED));
        $this->assertTrue($container->get(AclEntityDependencyProvider::IS_ACL_ENTITY_ENABLED));
    }
}
