<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\Expander;

use Generated\Shared\Transfer\AclEntityMetadataTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;

interface AclQueryExpanderInterface
{
    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface> $query
     * @param \Generated\Shared\Transfer\AclEntityMetadataTransfer $aclEntityMetadataTransfer
     * @param string $joinType
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     */
    public function joinRelation(
        ModelCriteria $query,
        AclEntityMetadataTransfer $aclEntityMetadataTransfer,
        string $joinType = Criteria::INNER_JOIN
    ): ModelCriteria;

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface> $query
     * @param \Generated\Shared\Transfer\AclEntityMetadataTransfer $parentAclEntityMetadataTransfer
     * @param string $joinType
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     */
    public function joinEntityParent(
        ModelCriteria $query,
        AclEntityMetadataTransfer $parentAclEntityMetadataTransfer,
        string $joinType = Criteria::INNER_JOIN
    ): ModelCriteria;

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface> $query
     * @param \Generated\Shared\Transfer\AclEntityMetadataTransfer $aclEntityMetadataTransfer
     * @param string $joinType
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     */
    public function joinSubEntityRootRelation(
        ModelCriteria $query,
        AclEntityMetadataTransfer $aclEntityMetadataTransfer,
        string $joinType = Criteria::INNER_JOIN
    ): ModelCriteria;

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface> $query
     * @param \Generated\Shared\Transfer\AclEntityMetadataTransfer $aclEntityMetadataTransfer
     * @param string $joinType
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     */
    public function joinEntityRootRelation(
        ModelCriteria $query,
        AclEntityMetadataTransfer $aclEntityMetadataTransfer,
        string $joinType = Criteria::INNER_JOIN
    ): ModelCriteria;
}
