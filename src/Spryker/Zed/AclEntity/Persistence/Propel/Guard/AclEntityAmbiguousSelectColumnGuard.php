<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\Guard;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Map\TableMap;
use Spryker\Zed\AclEntity\Persistence\Exception\AmbiguousSelectColumnException;

class AclEntityAmbiguousSelectColumnGuard implements AclEntityAmbiguousSelectColumnGuardInterface
{
    /**
     * @param array<\Propel\Runtime\ActiveQuery\Join> $preAclJoins
     */
    public function guardAgainstAmbiguousSelectColumns(ModelCriteria $query, array $preAclJoins): void
    {
        $unqualifiedSelectColumns = $this->getUnqualifiedSelectColumns($query);
        if (!$unqualifiedSelectColumns) {
            return;
        }

        $aclAddedJoins = array_diff_key($query->getJoins(), $preAclJoins);
        if (!$aclAddedJoins) {
            return;
        }

        foreach ($unqualifiedSelectColumns as $unqualifiedSelectColumn) {
            $ambiguousWithTableMap = $this->findTableMapWithColumn($aclAddedJoins, $unqualifiedSelectColumn);
            if ($ambiguousWithTableMap === null) {
                continue;
            }

            $this->resolveAmbiguousSelectColumn($query, $preAclJoins, $unqualifiedSelectColumn, $ambiguousWithTableMap);
        }
    }

    /**
     * @return array<string>
     */
    protected function getUnqualifiedSelectColumns(ModelCriteria $query): array
    {
        return array_filter(
            $query->getSelectColumns(),
            function (string $selectColumn): bool {
                return !str_contains($selectColumn, '.');
            },
        );
    }

    /**
     * @param array<\Propel\Runtime\ActiveQuery\Join> $joins
     */
    protected function findTableMapWithColumn(array $joins, string $column): ?TableMap
    {
        foreach ($joins as $join) {
            if (!$join instanceof ModelJoin) {
                continue;
            }

            $tableMap = $join->getTableMap();
            if ($tableMap && $tableMap->hasColumn($column)) {
                return $tableMap;
            }
        }

        return null;
    }

    /**
     * @param array<\Propel\Runtime\ActiveQuery\Join> $preAclJoins
     *
     * @throws \Spryker\Zed\AclEntity\Persistence\Exception\AmbiguousSelectColumnException
     */
    protected function resolveAmbiguousSelectColumn(
        ModelCriteria $query,
        array $preAclJoins,
        string $column,
        TableMap $ambiguousWithTableMap
    ): void {
        $originalOwnerTableMap = $this->findOriginalOwnerTableMap($query, $preAclJoins, $column);

        if ($originalOwnerTableMap !== null && count($query->getSelectColumns()) === 1) {
            $query->removeSelectColumn($column);
            $query->addSelectColumn(sprintf('%s.%s', $originalOwnerTableMap->getNameOrFail(), $column));

            return;
        }

        throw new AmbiguousSelectColumnException($query, $column, $ambiguousWithTableMap->getNameOrFail());
    }

    /**
     * @param array<\Propel\Runtime\ActiveQuery\Join> $preAclJoins
     */
    protected function findOriginalOwnerTableMap(ModelCriteria $query, array $preAclJoins, string $column): ?TableMap
    {
        $candidateTableMaps = [];

        $rootTableMap = $query->getTableMapOrFail();
        if ($rootTableMap->hasColumn($column)) {
            $candidateTableMaps[$rootTableMap->getNameOrFail()] = $rootTableMap;
        }

        foreach ($preAclJoins as $preAclJoin) {
            if (!$preAclJoin instanceof ModelJoin) {
                continue;
            }

            $tableMap = $preAclJoin->getTableMap();
            if ($tableMap && $tableMap->hasColumn($column)) {
                $candidateTableMaps[$tableMap->getNameOrFail()] = $tableMap;
            }
        }

        return count($candidateTableMaps) === 1 ? current($candidateTableMaps) : null;
    }
}
