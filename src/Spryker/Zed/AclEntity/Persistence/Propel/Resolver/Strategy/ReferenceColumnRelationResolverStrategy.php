<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Propel\Resolver\Strategy;

use Generated\Shared\Transfer\AclEntityMetadataTransfer;
use InvalidArgumentException;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Propel;

class ReferenceColumnRelationResolverStrategy extends AbstractRelationResolverStrategy
{
    /**
     * @var string
     */
    protected const COLUMN_GETTER_TEMPLATE = 'get%s';

    /**
     * @var string
     */
    protected const JOIN_COLUMN_TEMPLATE = '%s.%s';

    /**
     * @phpstan-return \Propel\Runtime\Collection\ObjectCollection<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     *
     * @param \Propel\Runtime\ActiveRecord\ActiveRecordInterface $entity
     * @param \Generated\Shared\Transfer\AclEntityMetadataTransfer $aclEntityMetadataTransfer
     *
     * @throws \InvalidArgumentException
     *
     * @return \Propel\Runtime\Collection\ObjectCollection
     */
    public function getRelations(
        ActiveRecordInterface $entity,
        AclEntityMetadataTransfer $aclEntityMetadataTransfer
    ): ObjectCollection {
        $entityRelations = new ObjectCollection();
        $relationEntityClass = $aclEntityMetadataTransfer->getParentOrFail()->getEntityNameOrFail();
        if ($entity->isNew()) {
            $entityRelations->push(new $relationEntityClass());

            return $entityRelations;
        }

        $query = $this->getQueryByEntityClass($relationEntityClass);
        $referencedColumn = $this->getColumnPhpName(
            $relationEntityClass,
            $aclEntityMetadataTransfer->getParentOrFail()->getConnectionOrFail()->getReferencedColumnOrFail(),
        );
        $referenceColumn = $aclEntityMetadataTransfer->getParentOrFail()->getConnectionOrFail()->getReferenceOrFail();

        $referenceGetter = $this->getColumnGetter(get_class($entity), $referenceColumn);

        $callable = [$entity, $referenceGetter];

        if (!is_callable($callable)) {
            throw new InvalidArgumentException(sprintf('Expected a valid callable, %s given.', gettype($callable)));
        }

        $query->filterBy($referencedColumn, call_user_func($callable));

        foreach ($query->find() as $entityRelation) {
            $entityRelations->push($entityRelation);
        }

        return $entityRelations;
    }

    /**
     * @phpstan-param \Propel\Runtime\ActiveQuery\ModelCriteria<\Propel\Runtime\ActiveRecord\ActiveRecordInterface> $query
     *
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param \Generated\Shared\Transfer\AclEntityMetadataTransfer $aclEntityMetadataTransfer
     *
     * @return \Propel\Runtime\ActiveQuery\Join
     */
    protected function generateAclEntityJoin(ModelCriteria $query, AclEntityMetadataTransfer $aclEntityMetadataTransfer): Join
    {
        $tableMap = $this->getTableMapByEntityClass($aclEntityMetadataTransfer->getEntityNameOrFail());
        $referencedTableMap = $this->getTableMapByEntityClass(
            $aclEntityMetadataTransfer->getParentOrFail()->getEntityNameOrFail(),
        );
        $join = new ModelJoin(
            sprintf(
                static::JOIN_COLUMN_TEMPLATE,
                $tableMap->getName(),
                $aclEntityMetadataTransfer->getParentOrFail()->getConnectionOrFail()->getReferenceOrFail(),
            ),
            sprintf(
                static::JOIN_COLUMN_TEMPLATE,
                $referencedTableMap->getName(),
                $aclEntityMetadataTransfer->getParentOrFail()->getConnectionOrFail()->getReferencedColumnOrFail(),
            ),
        );
        $join->setTableMap($referencedTableMap);

        return $this->updateJoinAliases($query, $join);
    }

    /**
     * @param string $entityClass
     * @param string $columnName
     *
     * @return string
     */
    protected function getColumnGetter(string $entityClass, string $columnName): string
    {
        $columnName = $this->getColumnPhpName($entityClass, $columnName);

        return sprintf(static::COLUMN_GETTER_TEMPLATE, $columnName);
    }

    /**
     * @param string $entityClass
     * @param string $columnName
     *
     * @return string
     */
    protected function getColumnPhpName(string $entityClass, string $columnName): string
    {
        return Propel::getServiceContainer()
            ->getDatabaseMap()
            ->getTableByPhpName($entityClass)
            ->getColumn($columnName)
            ->getPhpName();
    }
}
