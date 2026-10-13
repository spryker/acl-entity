<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\Guard;

use Propel\Runtime\ActiveQuery\ModelCriteria;

interface AclEntityAmbiguousSelectColumnGuardInterface
{
    /**
     * @param array<\Propel\Runtime\ActiveQuery\Join> $preAclJoins
     *
     * @throws \Spryker\Zed\AclEntity\Persistence\Exception\AmbiguousSelectColumnException
     */
    public function guardAgainstAmbiguousSelectColumns(ModelCriteria $query, array $preAclJoins): void;
}
