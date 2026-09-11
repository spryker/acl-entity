<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\AclEntity\BackendApi;

use Codeception\Test\Unit;
use Spryker\Glue\AclEntity\Plugin\Application\AclEntityApplicationPlugin;
use Spryker\Service\Container\Container;
use Spryker\Zed\AclEntity\AclEntityDependencyProvider;

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
    public function testProvideEnablesAclEntityInTheApplicationContainer(): void
    {
        // Arrange
        $container = new Container();

        // Act
        $container = (new AclEntityApplicationPlugin())->provide($container);

        // Assert
        $this->assertTrue($container->has(AclEntityDependencyProvider::IS_ACL_ENTITY_ENABLED));
        $this->assertTrue($container->get(AclEntityDependencyProvider::IS_ACL_ENTITY_ENABLED));
    }
}
