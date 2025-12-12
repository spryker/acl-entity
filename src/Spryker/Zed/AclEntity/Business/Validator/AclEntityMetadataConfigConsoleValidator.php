<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Business\Validator;

use Spryker\Zed\AclEntity\Business\Filter\AclEntityMetadataConfigFilterInterface;
use Spryker\Zed\AclEntity\Business\Reader\AclEntityMetadataConfigReaderInterface;

class AclEntityMetadataConfigConsoleValidator implements AclEntityMetadataConfigConsoleValidatorInterface
{
    /**
     * @param \Spryker\Zed\AclEntity\Business\Reader\AclEntityMetadataConfigReaderInterface $aclEntityMetadataConfigReader
     * @param \Spryker\Zed\AclEntity\Business\Filter\AclEntityMetadataConfigFilterInterface $aclEntityMetadataConfigFilter
     * @param \Spryker\Zed\AclEntity\Business\Validator\AclEntityMetadataConfigValidatorInterface $aclEntityMetadataConfigValidator
     */
    public function __construct(
        protected AclEntityMetadataConfigReaderInterface $aclEntityMetadataConfigReader,
        protected AclEntityMetadataConfigFilterInterface $aclEntityMetadataConfigFilter,
        protected AclEntityMetadataConfigValidatorInterface $aclEntityMetadataConfigValidator
    ) {
    }

    /**
     * @return void
     */
    public function validate(): void
    {
        $aclEntityMetadataConfigTransfer = $this->aclEntityMetadataConfigReader->getAclEntityMetadataConfig(false);
        $aclEntityMetadataConfigTransfer = $this->aclEntityMetadataConfigFilter->filter($aclEntityMetadataConfigTransfer);

        $this->aclEntityMetadataConfigValidator->validate($aclEntityMetadataConfigTransfer);
    }
}
