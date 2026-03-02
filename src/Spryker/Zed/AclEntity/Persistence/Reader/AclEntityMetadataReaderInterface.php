<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Reader;

use Generated\Shared\Transfer\AclEntityMetadataTransfer;

interface AclEntityMetadataReaderInterface
{
    public function findAclEntityMetadataTransferForEntityClass(string $entityClass): ?AclEntityMetadataTransfer;

    public function findRootAclEntityMetadataTransferForEntitySubClass(string $entitySubClass): ?AclEntityMetadataTransfer;

    public function findRootAclEntityMetadataTransferForEntityClass(string $entityClass): ?AclEntityMetadataTransfer;

    /**
     * @deprecated Will be removed without replacement.
     *
     * @param string $connectionClass
     *
     * @return \Generated\Shared\Transfer\AclEntityMetadataTransfer|null
     */
    public function findAclEntityMetadataTransferByConnectionClass(string $connectionClass): ?AclEntityMetadataTransfer;

    public function getRootAclEntityMetadataTransferForEntitySubClass(string $entitySubClass): AclEntityMetadataTransfer;

    public function getRootAclEntityMetadataTransferForEntityClass(string $entityClass): AclEntityMetadataTransfer;

    public function getAclEntityMetadataTransferForEntityClass(string $entityClass): AclEntityMetadataTransfer;

    public function getDefaultOperationMaskForEntityClass(string $entityClass): int;

    public function isAllowListItem(string $entityClass): bool;
}
