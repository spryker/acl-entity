<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Join;

use Generated\Shared\Transfer\AclEntityRuleCollectionTransfer;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\ActiveQuery\ModelCriteria;

interface AclJoinInterface
{
    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface> $query
     * @param \Propel\Runtime\ActiveQuery\Join $join
     * @param \Generated\Shared\Transfer\AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     */
    public function applyAclRuleOnSelectQueryRelation(
        ModelCriteria $query,
        Join $join,
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer
    ): ModelCriteria;

    /**
     * @param \Propel\Runtime\ActiveQuery\Join $join
     *
     * @return bool
     */
    public function isSupported(Join $join): bool;
}
