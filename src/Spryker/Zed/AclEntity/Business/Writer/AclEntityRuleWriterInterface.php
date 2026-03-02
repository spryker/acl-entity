<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Business\Writer;

use ArrayObject;
use Generated\Shared\Transfer\AclEntityRuleRequestTransfer;
use Generated\Shared\Transfer\AclEntityRuleResponseTransfer;

interface AclEntityRuleWriterInterface
{
    public function create(AclEntityRuleRequestTransfer $aclEntityRuleRequestTransfer): AclEntityRuleResponseTransfer;

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\AclEntityRuleTransfer> $aclEntityRuleTransfers
     *
     * @return void
     */
    public function saveAclEntityRules(ArrayObject $aclEntityRuleTransfers): void;
}
