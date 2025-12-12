<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AclEntity\Business\Validator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AclEntityMetadataCollectionTransfer;
use Generated\Shared\Transfer\AclEntityMetadataConfigTransfer;
use Generated\Shared\Transfer\AclEntityMetadataTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Spryker\Zed\AclEntity\Business\Exception\AclEntityMetadataConfigInvalidKeyException;
use Spryker\Zed\AclEntity\Business\Filter\AclEntityMetadataConfigFilter;
use Spryker\Zed\AclEntity\Business\Reader\AclEntityMetadataConfigReaderInterface;
use Spryker\Zed\AclEntity\Business\Validator\AclEntityMetadataConfigConsoleValidator;
use Spryker\Zed\AclEntity\Business\Validator\AclEntityMetadataConfigValidator;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AclEntity
 * @group Business
 * @group Validator
 * @group AclEntityMetadataConfigConsoleValidatorTest
 * Add your own group annotations below this line
 */
class AclEntityMetadataConfigConsoleValidatorTest extends Unit
{
    /**
     * @return void
     */
    public function testValidateSucceedsWithValidConfig(): void
    {
        // Arrange
        $aclEntityMetadataConfigTransfer = $this->createValidAclEntityMetadataConfigTransfer();

        $readerMock = $this->createReaderMock($aclEntityMetadataConfigTransfer);
        $filter = new AclEntityMetadataConfigFilter();
        $validator = new AclEntityMetadataConfigValidator();

        $consoleValidator = new AclEntityMetadataConfigConsoleValidator(
            $readerMock,
            $filter,
            $validator,
        );

        // Act & Assert - should not throw exception
        $consoleValidator->validate();
    }

    /**
     * @return void
     */
    public function testValidateThrowsExceptionWithInvalidKey(): void
    {
        // Arrange
        $aclEntityMetadataConfigTransfer = $this->createInvalidKeyAclEntityMetadataConfigTransfer();

        $readerMock = $this->createReaderMock($aclEntityMetadataConfigTransfer);
        $filter = new AclEntityMetadataConfigFilter();
        $validator = new AclEntityMetadataConfigValidator();

        $consoleValidator = new AclEntityMetadataConfigConsoleValidator(
            $readerMock,
            $filter,
            $validator,
        );

        // Assert
        $this->expectException(AclEntityMetadataConfigInvalidKeyException::class);

        // Act
        $consoleValidator->validate();
    }

    /**
     * @param \Generated\Shared\Transfer\AclEntityMetadataConfigTransfer $aclEntityMetadataConfigTransfer
     *
     * @return \Spryker\Zed\AclEntity\Business\Reader\AclEntityMetadataConfigReaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createReaderMock(
        AclEntityMetadataConfigTransfer $aclEntityMetadataConfigTransfer
    ): AclEntityMetadataConfigReaderInterface {
        $readerMock = $this->createMock(AclEntityMetadataConfigReaderInterface::class);
        $readerMock->method('getAclEntityMetadataConfig')
            ->willReturn($aclEntityMetadataConfigTransfer);

        return $readerMock;
    }

    /**
     * @return \Generated\Shared\Transfer\AclEntityMetadataConfigTransfer
     */
    protected function createValidAclEntityMetadataConfigTransfer(): AclEntityMetadataConfigTransfer
    {
        return (new AclEntityMetadataConfigTransfer())
            ->setAclEntityMetadataCollection(
                (new AclEntityMetadataCollectionTransfer())
                    ->addAclEntityMetadata(
                        SpyMerchant::class,
                        (new AclEntityMetadataTransfer())
                            ->setEntityName(SpyMerchant::class),
                    ),
            );
    }

    /**
     * @return \Generated\Shared\Transfer\AclEntityMetadataConfigTransfer
     */
    protected function createInvalidKeyAclEntityMetadataConfigTransfer(): AclEntityMetadataConfigTransfer
    {
        return (new AclEntityMetadataConfigTransfer())
            ->setAclEntityMetadataCollection(
                (new AclEntityMetadataCollectionTransfer())
                    ->addAclEntityMetadata(
                        'InvalidKey',
                        (new AclEntityMetadataTransfer())
                            ->setEntityName(SpyMerchant::class),
                    ),
            );
    }
}
