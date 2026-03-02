<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\Comparator;

use Propel\Runtime\ActiveQuery\Join;

interface JoinComparatorInterface
{
    public function areEqual(Join $join1, Join $join2): bool;
}
