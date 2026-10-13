<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Query;

use Generated\Shared\Transfer\AclEntityMetadataTransfer;
use Generated\Shared\Transfer\AclEntityRuleCollectionTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\PropelQuery;
use Spryker\Shared\AclEntity\AclEntityConstants;
use Spryker\Zed\AclEntity\Persistence\Exception\InvalidScopeException;
use Spryker\Zed\AclEntity\Persistence\Filter\AclEntityRuleCollectionTransferFilterInterface;
use Spryker\Zed\AclEntity\Persistence\Propel\Expander\AclQueryExpanderInterface;
use Spryker\Zed\AclEntity\Persistence\Propel\QueryMerger\AclEntityQueryMergerInterface;
use Spryker\Zed\AclEntity\Persistence\Reader\AclEntityMetadataReaderInterface;
use Spryker\Zed\AclEntity\Persistence\Sorter\AclEntityRuleCollectionTransferSorterInterface;

class InheritedAclQueryScope implements AclQueryScopeInterface
{
    /**
     * @var \Spryker\Zed\AclEntity\Persistence\Reader\AclEntityMetadataReaderInterface
     */
    protected $aclEntityMetadataReader;

    /**
     * @var \Spryker\Zed\AclEntity\Persistence\Propel\Expander\AclQueryExpanderInterface
     */
    protected $aclQueryExpander;

    /**
     * @var \Spryker\Zed\AclEntity\Persistence\Filter\AclEntityRuleCollectionTransferFilterInterface
     */
    protected $aclEntityRuleCollectionTransferFilter;

    /**
     * @var \Spryker\Zed\AclEntity\Persistence\Sorter\AclEntityRuleCollectionTransferSorterInterface
     */
    protected $aclEntityRuleCollectionTransferSorter;

    /**
     * @var \Spryker\Zed\AclEntity\Persistence\Propel\QueryMerger\AclEntityQueryMergerInterface
     */
    protected $queryMerger;

    /**
     * @var array<\Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Query\AclQueryScopeInterface>
     */
    protected $queryScopes;

    /**
     * @param \Spryker\Zed\AclEntity\Persistence\Reader\AclEntityMetadataReaderInterface $aclEntityMetadataReader
     * @param \Spryker\Zed\AclEntity\Persistence\Propel\Expander\AclQueryExpanderInterface $aclQueryExpander
     * @param \Spryker\Zed\AclEntity\Persistence\Filter\AclEntityRuleCollectionTransferFilterInterface $aclEntityRuleCollectionTransferFilter
     * @param \Spryker\Zed\AclEntity\Persistence\Sorter\AclEntityRuleCollectionTransferSorterInterface $aclEntityRuleCollectionTransferSorter
     * @param \Spryker\Zed\AclEntity\Persistence\Propel\QueryMerger\AclEntityQueryMergerInterface $queryMerger
     * @param array<\Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Query\AclQueryScopeInterface> $queryScopes
     */
    public function __construct(
        AclEntityMetadataReaderInterface $aclEntityMetadataReader,
        AclQueryExpanderInterface $aclQueryExpander,
        AclEntityRuleCollectionTransferFilterInterface $aclEntityRuleCollectionTransferFilter,
        AclEntityRuleCollectionTransferSorterInterface $aclEntityRuleCollectionTransferSorter,
        AclEntityQueryMergerInterface $queryMerger,
        array $queryScopes
    ) {
        $this->aclEntityMetadataReader = $aclEntityMetadataReader;
        $this->aclQueryExpander = $aclQueryExpander;
        $this->aclEntityRuleCollectionTransferFilter = $aclEntityRuleCollectionTransferFilter;
        $this->aclEntityRuleCollectionTransferSorter = $aclEntityRuleCollectionTransferSorter;
        $this->queryMerger = $queryMerger;
        $this->queryScopes = $queryScopes;
    }

    public function isSupported(string $scope): bool
    {
        return $scope === AclEntityConstants::SCOPE_INHERITED;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface> $query
     * @param \Generated\Shared\Transfer\AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     */
    public function applyAclRuleOnSelectQuery(ModelCriteria $query, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): ModelCriteria
    {
        $aclEntityMetadataTransfer = $this->aclEntityMetadataReader->getAclEntityMetadataTransferForEntityClass(
            $query->getModelNameOrFail(),
        );
        $parentAclEntityRules = [];
        foreach ($this->getGroupedAclEntityRulesByAclGroupId($aclEntityRuleCollectionTransfer) as $aclEntityRuleCollectionTransfer) {
            if ($this->hasReadableGlobalScopeParent($aclEntityMetadataTransfer, $aclEntityRuleCollectionTransfer)) {
                return $query;
            }
            $parentAclEntityMetadataTransfer = $this->findReadableSegmentScopeParentMetadata(
                $aclEntityMetadataTransfer,
                $aclEntityRuleCollectionTransfer,
            );
            if (!$parentAclEntityMetadataTransfer) {
                continue;
            }
            $parentAclEntityRules[$parentAclEntityMetadataTransfer->getEntityNameOrFail()] = $this
                ->extendAclEntityRuleCollection(
                    $parentAclEntityRules[$parentAclEntityMetadataTransfer->getEntityNameOrFail()] ?? null,
                    $aclEntityRuleCollectionTransfer,
                    $parentAclEntityMetadataTransfer,
                );

            $query = $this->aclQueryExpander->joinEntityParent($query, $parentAclEntityMetadataTransfer);
        }

        if (!$parentAclEntityRules) {
            return $this->getDefaultAclQueryScope()->applyAclRuleOnSelectQuery($query, $aclEntityRuleCollectionTransfer);
        }

        return $this->applyParentAclEntityRulesOnSelectQuery($query, $parentAclEntityRules);
    }

    protected function hasReadableGlobalScopeParent(
        AclEntityMetadataTransfer $aclEntityMetadataTransfer,
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer
    ): bool {
        $readableGlobalScopeParentAclEntityMetadata = $this->findReadableParentMetadataByScope(
            $aclEntityMetadataTransfer,
            $aclEntityRuleCollectionTransfer,
            AclEntityConstants::SCOPE_GLOBAL,
        );

        return $readableGlobalScopeParentAclEntityMetadata !== null;
    }

    protected function findReadableSegmentScopeParentMetadata(
        AclEntityMetadataTransfer $aclEntityMetadataTransfer,
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer
    ): ?AclEntityMetadataTransfer {
        return $this->findReadableParentMetadataByScope(
            $aclEntityMetadataTransfer,
            $aclEntityRuleCollectionTransfer,
            AclEntityConstants::SCOPE_SEGMENT,
        );
    }

    protected function findReadableParentMetadataByScope(
        AclEntityMetadataTransfer $aclEntityMetadataTransfer,
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        string $scope
    ): ?AclEntityMetadataTransfer {
        if ($aclEntityMetadataTransfer->getIsSubEntity()) {
            return $this->findReadableParentMetadataByScope(
                $this->aclEntityMetadataReader->getAclEntityMetadataTransferForEntityClass(
                    $aclEntityMetadataTransfer->getParentOrFail()->getEntityNameOrFail(),
                ),
                $aclEntityRuleCollectionTransfer,
                $scope,
            );
        }
        $entityAclEntityRuleCollectionTransfer = $this->aclEntityRuleCollectionTransferSorter->sortByScopePriority(
            $this->aclEntityRuleCollectionTransferFilter->filterByEntityClassAndPermissionMask(
                $aclEntityRuleCollectionTransfer,
                $aclEntityMetadataTransfer->getEntityNameOrFail(),
                AclEntityConstants::OPERATION_MASK_READ,
            ),
        );
        foreach ($entityAclEntityRuleCollectionTransfer->getAclEntityRules() as $aclEntityRuleTransfer) {
            if ($aclEntityRuleTransfer->getScopeOrFail() === $scope) {
                return $aclEntityMetadataTransfer;
            }
            if ($aclEntityRuleTransfer->getScopeOrFail() !== AclEntityConstants::SCOPE_INHERITED) {
                continue;
            }
            $parentAclEntityMetadata = $this->aclEntityMetadataReader->getAclEntityMetadataTransferForEntityClass(
                $aclEntityMetadataTransfer->getParentOrFail()->getEntityNameOrFail(),
            );

            return $this->findReadableParentMetadataByScope(
                $parentAclEntityMetadata,
                $aclEntityRuleCollectionTransfer,
                $scope,
            );
        }

        return null;
    }

    protected function extendAclEntityRuleCollection(
        ?AclEntityRuleCollectionTransfer $currentAclEntityRuleCollectionTransfer,
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        AclEntityMetadataTransfer $parentAclEntityMetadataTransfer
    ): AclEntityRuleCollectionTransfer {
        if (!$currentAclEntityRuleCollectionTransfer) {
            $currentAclEntityRuleCollectionTransfer = new AclEntityRuleCollectionTransfer();
        }

        $parentAclEntityRuleCollectionTransfer = $this->aclEntityRuleCollectionTransferFilter
            ->filterByScopeEntityClassAndPermissionMask(
                $aclEntityRuleCollectionTransfer,
                AclEntityConstants::SCOPE_SEGMENT,
                $parentAclEntityMetadataTransfer->getEntityNameOrFail(),
                AclEntityConstants::OPERATION_MASK_READ,
            );

        foreach ($parentAclEntityRuleCollectionTransfer->getAclEntityRules() as $parentAclEntityRule) {
            $currentAclEntityRuleCollectionTransfer->addAclEntityRule($parentAclEntityRule);
        }

        return $currentAclEntityRuleCollectionTransfer;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface> $query
     * @param array<\Generated\Shared\Transfer\AclEntityRuleCollectionTransfer> $parentAclEntityRules
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     */
    protected function applyParentAclEntityRulesOnSelectQuery(
        ModelCriteria $query,
        array $parentAclEntityRules
    ): ModelCriteria {
        foreach ($parentAclEntityRules as $parentClass => $aclEntityRuleCollectionTransfer) {
            $query = $this->queryMerger->mergeQueries(
                $query,
                $this->getSegmentAclQueryScope()->applyAclRuleOnSelectQuery(
                    PropelQuery::from($parentClass),
                    $aclEntityRuleCollectionTransfer,
                ),
            );
        }

        return $query;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface> $query
     * @param \Generated\Shared\Transfer\AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     */
    public function applyAclRuleOnUpdateQuery(ModelCriteria $query, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): ModelCriteria
    {
        return $this->restrictQueryToAccessibleEntityIds($query, $aclEntityRuleCollectionTransfer, AclEntityConstants::OPERATION_MASK_UPDATE);
    }

    public function applyAclRuleOnDeleteQuery(ModelCriteria $query, AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer): ModelCriteria
    {
        return $this->restrictQueryToAccessibleEntityIds($query, $aclEntityRuleCollectionTransfer, AclEntityConstants::OPERATION_MASK_DELETE);
    }

    protected function restrictQueryToAccessibleEntityIds(
        ModelCriteria $query,
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        int $permissionMask
    ): ModelCriteria {
        $aclEntityMetadataTransfer = $this->aclEntityMetadataReader->getRootAclEntityMetadataTransferForEntitySubClass(
            $query->getModelNameOrFail(),
        );

        $accessibleEntityIds = $this->findAccessibleEntityIds($aclEntityMetadataTransfer, $aclEntityRuleCollectionTransfer, $permissionMask);

        /** @var \Propel\Runtime\Map\ColumnMap $primaryKey */
        $primaryKey = current($query->getTableMapOrFail()->getPrimaryKeys());
        $accessibleEntityIdsCriterion = $accessibleEntityIds
            ? $query->getNewCriterion($primaryKey->getFullyQualifiedName(), $accessibleEntityIds, Criteria::IN)
            : $query->getNewCriterion($primaryKey->getFullyQualifiedName(), null, Criteria::ISNULL);

        if ($query->containsKey($primaryKey->getFullyQualifiedName())) {
            $query->getCriterion($primaryKey->getFullyQualifiedName())->addAnd($accessibleEntityIdsCriterion);

            return $query;
        }

        return $query->add($accessibleEntityIdsCriterion);
    }

    /**
     * @return array<int>
     */
    protected function findAccessibleEntityIds(
        AclEntityMetadataTransfer $aclEntityMetadataTransfer,
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        int $permissionMask
    ): array {
        [$queryScope, $resolvedAclEntityMetadataTransfer] = $this->resolveAclQueryScope(
            $aclEntityRuleCollectionTransfer,
            $aclEntityMetadataTransfer,
            $permissionMask,
        );

        $accessibleEntityQuery = $this->aclQueryExpander->joinEntityParent(
            PropelQuery::from($aclEntityMetadataTransfer->getEntityNameOrFail()),
            $resolvedAclEntityMetadataTransfer,
        );
        $accessibleEntityQuery = $this->queryMerger->mergeQueries(
            $accessibleEntityQuery,
            $this->applyAclRuleOnPermissionMaskQuery(
                $queryScope,
                PropelQuery::from($resolvedAclEntityMetadataTransfer->getEntityNameOrFail()),
                $aclEntityRuleCollectionTransfer,
                $permissionMask,
            ),
        );

        /** @var \Propel\Runtime\Map\ColumnMap $primaryKey */
        $primaryKey = current($accessibleEntityQuery->getTableMapOrFail()->getPrimaryKeys());
        $primaryKeyGetter = sprintf('get%s', $primaryKey->getPhpName());

        return array_map(
            function ($entity) use ($primaryKeyGetter) {
                return $entity->{$primaryKeyGetter}();
            },
            $accessibleEntityQuery->find()->getArrayCopy(),
        );
    }

    protected function applyAclRuleOnPermissionMaskQuery(
        AclQueryScopeInterface $queryScope,
        ModelCriteria $query,
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        int $permissionMask
    ): ModelCriteria {
        if ($permissionMask === AclEntityConstants::OPERATION_MASK_UPDATE) {
            return $queryScope->applyAclRuleOnUpdateQuery($query, $aclEntityRuleCollectionTransfer);
        }

        return $queryScope->applyAclRuleOnDeleteQuery($query, $aclEntityRuleCollectionTransfer);
    }

    /**
     * @return array{0: \Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Query\AclQueryScopeInterface, 1: \Generated\Shared\Transfer\AclEntityMetadataTransfer}
     */
    protected function resolveAclQueryScope(
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer,
        AclEntityMetadataTransfer $aclEntityMetadataTransfer,
        int $permissionMask
    ): array {
        $filteredAclEntityRuleCollectionTransfer = $this->aclEntityRuleCollectionTransferSorter->sortByScopePriority(
            $this->aclEntityRuleCollectionTransferFilter->filterByEntityClassAndPermissionMask(
                $aclEntityRuleCollectionTransfer,
                $aclEntityMetadataTransfer->getEntityNameOrFail(),
                $permissionMask,
            ),
        );

        foreach ($filteredAclEntityRuleCollectionTransfer->getAclEntityRules() as $aclEntityRuleTransfer) {
            if ($aclEntityRuleTransfer->getScopeOrFail() === AclEntityConstants::SCOPE_INHERITED) {
                $parentAclEntityMetadataTransfer = $this->aclEntityMetadataReader
                    ->getAclEntityMetadataTransferForEntityClass(
                        $aclEntityMetadataTransfer->getParentOrFail()->getEntityNameOrFail(),
                    );

                return $this->resolveAclQueryScope(
                    $aclEntityRuleCollectionTransfer,
                    $parentAclEntityMetadataTransfer,
                    $permissionMask,
                );
            }

            return [$this->getAclQueryScopeByName($aclEntityRuleTransfer->getScopeOrFail()), $aclEntityMetadataTransfer];
        }

        return [$this->getDefaultAclQueryScope(), $aclEntityMetadataTransfer];
    }

    /**
     * @param string $scope
     *
     * @throws \Spryker\Zed\AclEntity\Persistence\Exception\InvalidScopeException
     *
     * @return \Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Query\AclQueryScopeInterface
     */
    protected function getAclQueryScopeByName(string $scope): AclQueryScopeInterface
    {
        foreach ($this->queryScopes as $queryScope) {
            if ($queryScope->isSupported($scope)) {
                return $queryScope;
            }
        }

        throw new InvalidScopeException($scope);
    }

    /**
     * @param \Generated\Shared\Transfer\AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer
     *
     * @return array<\Generated\Shared\Transfer\AclEntityRuleCollectionTransfer>
     */
    protected function getGroupedAclEntityRulesByAclGroupId(
        AclEntityRuleCollectionTransfer $aclEntityRuleCollectionTransfer
    ): array {
        $result = [];
        foreach ($aclEntityRuleCollectionTransfer->getAclEntityRules() as $aclEntityRuleTransfer) {
            if (!isset($result[$aclEntityRuleTransfer->getIdAclRoleOrFail()])) {
                $result[$aclEntityRuleTransfer->getIdAclRoleOrFail()] = new AclEntityRuleCollectionTransfer();
            }
            $result[$aclEntityRuleTransfer->getIdAclRoleOrFail()]->addAclEntityRule($aclEntityRuleTransfer);
        }

        return $result;
    }

    protected function getDefaultAclQueryScope(): AclQueryScopeInterface
    {
        return $this->getAclQueryScopeByName(AclEntityConstants::SCOPE_DEFAULT);
    }

    protected function getSegmentAclQueryScope(): AclQueryScopeInterface
    {
        return $this->getAclQueryScopeByName(AclEntityConstants::SCOPE_SEGMENT);
    }
}
