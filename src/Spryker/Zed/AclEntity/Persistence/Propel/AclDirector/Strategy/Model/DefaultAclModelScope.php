<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Model;

use Generated\Shared\Transfer\AclEntityRuleCollectionTransfer;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Spryker\Shared\AclEntity\AclEntityConstants;
use Spryker\Zed\AclEntity\Persistence\Reader\AclEntityMetadataReaderInterface;

class DefaultAclModelScope implements AclModelScopeInterface
{
    /**
     * @var \Spryker\Zed\AclEntity\Persistence\Reader\AclEntityMetadataReaderInterface
     */
    protected $aclEntityMetadataReader;

    public function __construct(AclEntityMetadataReaderInterface $aclEntityMetadataReader)
    {
        $this->aclEntityMetadataReader = $aclEntityMetadataReader;
    }

    public function isSupported(string $scope): bool
    {
        return $scope === AclEntityConstants::SCOPE_DEFAULT;
    }

    public function isCreatable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool
    {
        $entityDefaultOperationMask = $this->aclEntityMetadataReader->getDefaultOperationMaskForEntityClass(
            get_class($entity),
        );

        return ($entityDefaultOperationMask & AclEntityConstants::OPERATION_MASK_CREATE) > 0;
    }

    public function isUpdatable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool
    {
        $entityDefaultOperationMask = $this->aclEntityMetadataReader->getDefaultOperationMaskForEntityClass(
            get_class($entity),
        );

        return ($entityDefaultOperationMask & AclEntityConstants::OPERATION_MASK_UPDATE) > 0;
    }

    public function isDeletable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool
    {
        $entityDefaultOperationMask = $this->aclEntityMetadataReader->getDefaultOperationMaskForEntityClass(
            get_class($entity),
        );

        return ($entityDefaultOperationMask & AclEntityConstants::OPERATION_MASK_DELETE) > 0;
    }

    public function isReadable(ActiveRecordInterface $entity, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): bool
    {
        $entityDefaultOperationMask = $this->aclEntityMetadataReader->getDefaultOperationMaskForEntityClass(
            get_class($entity),
        );

        return ($entityDefaultOperationMask & AclEntityConstants::OPERATION_MASK_READ) > 0;
    }
}
