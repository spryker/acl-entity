<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Communication\Console;

use Exception;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Spryker\Zed\AclEntity\Business\AclEntityFacadeInterface getFacade()
 * @method \Spryker\Zed\AclEntity\Communication\AclEntityCommunicationFactory getFactory()
 */
class AclEntityMetadataConfigValidateConsole extends Console
{
    /**
     * @var string
     */
    protected const COMMAND_NAME = 'acl-entity:metadata:validate';

    /**
     * @var string
     */
    protected const COMMAND_DESCRIPTION = 'Validates ACL entity metadata configuration.';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->info('Validating ACL entity metadata configuration...');

        try {
            $this->getFacade()->validateAclEntityMetadataConfig();
            $this->info('ACL entity metadata configuration is valid.');

            return static::CODE_SUCCESS;
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
            $this->warning('Validation stops on first error. Fix the issue and re-run to check for additional errors.');

            return static::CODE_ERROR;
        }
    }
}
