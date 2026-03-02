<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AclEntity\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\AclEntityRuleRequestBuilder;
use Generated\Shared\DataBuilder\AclEntitySegmentRequestBuilder;
use Generated\Shared\Transfer\AclEntityRuleCriteriaTransfer;
use Generated\Shared\Transfer\AclEntityRuleTransfer;
use Generated\Shared\Transfer\AclEntitySegmentCriteriaTransfer;
use Generated\Shared\Transfer\AclEntitySegmentTransfer;
use Orm\Zed\AclEntity\Persistence\SpyAclEntityRuleQuery;
use Orm\Zed\AclEntity\Persistence\SpyAclEntitySegmentQuery;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class AclEntityHelper extends Module
{
    use DataCleanupHelperTrait;
    use LocatorHelperTrait;

    /**
     * @param array<mixed> $seedData
     *
     * @return \Generated\Shared\Transfer\AclEntitySegmentTransfer
     */
    public function haveAclEntitySegment(array $seedData = []): AclEntitySegmentTransfer
    {
        /** @var \Generated\Shared\Transfer\AclEntitySegmentTransfer $aclEntitySegmentResponseTransfer */
        $aclEntitySegmentResponseTransfer = $this->getLocator()
            ->aclEntity()
            ->facade()
            ->createAclEntitySegment(
                (new AclEntitySegmentRequestBuilder($seedData))->build(),
            )
            ->getAclEntitySegmentOrFail();

        $this->getDataCleanupHelper()->_addCleanup(function () use ($aclEntitySegmentResponseTransfer): void {
            $this->cleanupAclEntitySegment($aclEntitySegmentResponseTransfer->getIdAclEntitySegment());
        });

        return $aclEntitySegmentResponseTransfer;
    }

    public function deleteAclEntitySegments(
        AclEntitySegmentCriteriaTransfer $aclEntitySegmentSearchCriteriaTransfer
    ): void {
        $query = SpyAclEntitySegmentQuery::create();
        if ($aclEntitySegmentSearchCriteriaTransfer->getReferences()) {
            $query->filterByReference_In($aclEntitySegmentSearchCriteriaTransfer->getReferences());
            $query->delete();

            return;
        }

        $query->deleteAll();
    }

    /**
     * @param array<mixed> $seedData
     *
     * @return \Generated\Shared\Transfer\AclEntityRuleTransfer
     */
    public function haveAclEntityRule(array $seedData = []): AclEntityRuleTransfer
    {
        /** @var \Generated\Shared\Transfer\AclEntityRuleTransfer $aclEntityRuleTransfer */
        $aclEntityRuleTransfer = $this->getLocator()
            ->aclEntity()
            ->facade()
            ->createAclEntityRule((new AclEntityRuleRequestBuilder($seedData))->build())
            ->getAclEntityRuleOrFail();

        $this->getDataCleanupHelper()->_addCleanup(function () use ($aclEntityRuleTransfer): void {
            $this->cleanAclEntityRule($aclEntityRuleTransfer->getIdAclEntityRuleOrFail());
        });

        return $aclEntityRuleTransfer;
    }

    public function deleteAclEntityRules(AclEntityRuleCriteriaTransfer $aclEntityRuleCriteriaTransfer): void
    {
        $query = SpyAclEntityRuleQuery::create();
        if ($aclEntityRuleCriteriaTransfer->getAclRoleIds()) {
            $query->filterByFkAclRole_In($aclEntityRuleCriteriaTransfer->getAclRoleIds());
            $query->delete();

            return;
        }

        $query->deleteAll();
    }

    private function cleanupAclEntitySegment(int $idAclEntitySegment): void
    {
        SpyAclEntitySegmentQuery::create()->findByIdAclEntitySegment($idAclEntitySegment)->delete();
    }

    private function cleanAclEntityRule(int $idAclEntityRule): void
    {
        SpyAclEntityRuleQuery::create()->findByIdAclEntityRule($idAclEntityRule)->delete();
    }
}
