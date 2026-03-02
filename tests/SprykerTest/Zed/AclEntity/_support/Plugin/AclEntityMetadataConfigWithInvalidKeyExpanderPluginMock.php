<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AclEntity\Plugin;

use Generated\Shared\Transfer\AclEntityMetadataCollectionTransfer;
use Generated\Shared\Transfer\AclEntityMetadataConfigTransfer;
use Generated\Shared\Transfer\AclEntityMetadataTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Orm\Zed\User\Persistence\SpyUser;
use Spryker\Zed\AclEntityExtension\Dependency\Plugin\AclEntityMetadataConfigExpanderPluginInterface;

class AclEntityMetadataConfigWithInvalidKeyExpanderPluginMock implements AclEntityMetadataConfigExpanderPluginInterface
{
    public function expand(
        AclEntityMetadataConfigTransfer $aclEntityMetadataConfigTransfer
    ): AclEntityMetadataConfigTransfer {
        return $aclEntityMetadataConfigTransfer->setAclEntityMetadataCollection(
            (new AclEntityMetadataCollectionTransfer())
                ->addAclEntityMetadata(
                    SpyUser::class,
                    (new AclEntityMetadataTransfer())
                        ->setEntityName(SpyMerchant::class),
                ),
        );
    }
}
