<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Join;

use Codeception\Test\Unit;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\ColumnMap;
use Propel\Runtime\Map\DatabaseMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\ServiceContainer\ServiceContainerInterface;
use ReflectionMethod;
use Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Join\RightAclJoin;
use Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\StrategyResolver\AclQueryScopeResolverInterface;
use Spryker\Zed\AclEntity\Persistence\Propel\Expander\AclQueryExpanderInterface;
use Spryker\Zed\AclEntity\Persistence\Propel\QueryMerger\AclEntityQueryMergerInterface;
use Spryker\Zed\AclEntity\Persistence\Reader\AclEntityMetadataReaderInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AclEntity
 * @group Persistence
 * @group Propel
 * @group AclDirector
 * @group Strategy
 * @group Join
 * @group RightAclJoinTest
 * Add your own group annotations below this line
 */
class RightAclJoinTest extends Unit
{
    protected RightAclJoin $rightAclJoin;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Propel\Runtime\ServiceContainer\ServiceContainerInterface
     */
    protected $propelServiceContainerMock;

    protected function _before(): void
    {
        parent::_before();

        $this->propelServiceContainerMock = $this->createMock(ServiceContainerInterface::class);

        $this->rightAclJoin = new RightAclJoin(
            $this->createMock(AclEntityMetadataReaderInterface::class),
            $this->createMock(AclQueryScopeResolverInterface::class),
            $this->createMock(AclQueryExpanderInterface::class),
            $this->createMock(AclEntityQueryMergerInterface::class),
            $this->propelServiceContainerMock,
        );
    }

    public function testExtendQueryWithSegmentConditionsUsesTheOriginalJoinsRealAlias(): void
    {
        // Arrange
        $segmentJoin = $this->createMock(Join::class);
        $segmentJoin->method('getRightTableName')->willReturn('spy_acl_entity_segment_store');
        $segmentJoin->method('getRightTableAliasOrName')->willReturn('spy_acl_entity_segment_store');

        $originalJoin = $this->createMock(Join::class);
        $originalJoin->method('getRightTableName')->willReturn('spy_store');
        $originalJoin->method('getRightTableAliasOrName')->willReturn('store2');

        $relationQuery = $this->createMock(ModelCriteria::class);
        $relationQuery->method('getJoins')->willReturn(['spy_acl_entity_segment_store' => $segmentJoin]);

        $this->configurePrimaryKeyColumns([
            'spy_store' => 'id_store',
            'spy_acl_entity_segment_store' => 'id_acl_entity_segment_store',
        ]);

        // Assert
        $relationQuery->expects($this->once())
            ->method('where')
            ->with($this->stringContains('store2.id_store'));

        // Act
        $this->invokeExtendQueryWithSegmentConditions($relationQuery, $originalJoin);
    }

    public function testExtendQueryWithSegmentConditionsBuildsWhereConditionForASingleOccurrence(): void
    {
        // Arrange
        $segmentJoin = $this->createMock(Join::class);
        $segmentJoin->method('getRightTableName')->willReturn('spy_acl_entity_segment_store');
        $segmentJoin->method('getRightTableAliasOrName')->willReturn('spy_acl_entity_segment_store');

        $storeJoin = $this->createMock(Join::class);
        $storeJoin->method('getRightTableName')->willReturn('spy_store');
        $storeJoin->method('getRightTableAliasOrName')->willReturn('spy_store');

        $relationQuery = $this->createMock(ModelCriteria::class);
        $relationQuery->method('getJoins')->willReturn(['spy_acl_entity_segment_store' => $segmentJoin]);

        $this->configurePrimaryKeyColumns([
            'spy_store' => 'id_store',
            'spy_acl_entity_segment_store' => 'id_acl_entity_segment_store',
        ]);

        // Assert
        $relationQuery->expects($this->once())
            ->method('where')
            ->with('spy_store.id_store IS NULL OR spy_acl_entity_segment_store.id_acl_entity_segment_store IS NOT NULL');

        // Act
        $result = $this->invokeExtendQueryWithSegmentConditions($relationQuery, $storeJoin);

        $this->assertSame($relationQuery, $result);
    }

    /**
     * @param array<string, string> $primaryKeyColumnNamesByTableName
     */
    protected function configurePrimaryKeyColumns(array $primaryKeyColumnNamesByTableName): void
    {
        $tableMapsByName = [];
        foreach ($primaryKeyColumnNamesByTableName as $tableName => $primaryKeyColumnName) {
            $primaryKeyColumnMock = $this->createMock(ColumnMap::class);
            $primaryKeyColumnMock->method('getName')->willReturn($primaryKeyColumnName);

            $tableMapMock = $this->createMock(TableMap::class);
            $tableMapMock->method('getPrimaryKeys')->willReturn([$primaryKeyColumnMock]);

            $tableMapsByName[$tableName] = $tableMapMock;
        }

        $databaseMapMock = $this->createMock(DatabaseMap::class);
        $databaseMapMock->method('getTable')->willReturnCallback(
            function (string $tableName) use ($tableMapsByName): TableMap {
                return $tableMapsByName[$tableName];
            },
        );

        $this->propelServiceContainerMock->method('getDatabaseMap')->willReturn($databaseMapMock);
    }

    protected function invokeExtendQueryWithSegmentConditions(ModelCriteria $query, Join $join): ModelCriteria
    {
        $reflectionMethod = new ReflectionMethod(RightAclJoin::class, 'extendQueryWithSegmentConditions');
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invoke($this->rightAclJoin, $query, $join);
    }
}
