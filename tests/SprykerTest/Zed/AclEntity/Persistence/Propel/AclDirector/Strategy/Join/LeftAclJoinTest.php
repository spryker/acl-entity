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
use Spryker\Zed\AclEntity\Persistence\Propel\AclDirector\Strategy\Join\LeftAclJoin;
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
 * @group LeftAclJoinTest
 * Add your own group annotations below this line
 */
class LeftAclJoinTest extends Unit
{
    protected LeftAclJoin $leftAclJoin;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Propel\Runtime\ServiceContainer\ServiceContainerInterface
     */
    protected $propelServiceContainerMock;

    protected function _before(): void
    {
        parent::_before();

        $this->propelServiceContainerMock = $this->createMock(ServiceContainerInterface::class);

        $this->leftAclJoin = new LeftAclJoin(
            $this->createMock(AclEntityMetadataReaderInterface::class),
            $this->createMock(AclQueryScopeResolverInterface::class),
            $this->createMock(AclQueryExpanderInterface::class),
            $this->createMock(AclEntityQueryMergerInterface::class),
            $this->propelServiceContainerMock,
        );
    }

    public function testExtendQueryWithSegmentConditionsUsesTheSegmentJoinOfTheOccurrenceBeingProcessed(): void
    {
        // Arrange
        $unrelatedFirstOccurrenceSegmentJoin = $this->createMock(Join::class);
        $unrelatedFirstOccurrenceSegmentJoin->method('getRightTableName')->willReturn('spy_acl_entity_segment_store');
        $unrelatedFirstOccurrenceSegmentJoin->method('getRightTableAliasOrName')->willReturn('spy_acl_entity_segment_store');

        $secondOccurrenceSegmentJoin = $this->createMock(Join::class);
        $secondOccurrenceSegmentJoin->method('getRightTableName')->willReturn('spy_acl_entity_segment_store');
        $secondOccurrenceSegmentJoin->method('getRightTableAliasOrName')->willReturn('acl_segment_store2');

        $secondOccurrenceStoreJoin = $this->createMock(Join::class);
        $secondOccurrenceStoreJoin->method('getRightTableName')->willReturn('spy_store');
        $secondOccurrenceStoreJoin->method('getRightTableAliasOrName')->willReturn('store2');

        $query = $this->createMock(ModelCriteria::class);
        $query->method('getJoins')->willReturn([
            'spy_acl_entity_segment_store' => $unrelatedFirstOccurrenceSegmentJoin,
            'store2' => $secondOccurrenceStoreJoin,
            'acl_segment_store2' => $secondOccurrenceSegmentJoin,
        ]);

        $this->configurePrimaryKeyColumns([
            'spy_acl_entity_segment_store' => 'id_acl_entity_segment_store',
            'spy_store' => 'id_store',
        ]);

        // Assert
        $query->expects($this->once())
            ->method('where')
            ->with($this->logicalAnd(
                $this->stringContains('acl_segment_store2.id_acl_entity_segment_store'),
                $this->logicalNot($this->stringContains('spy_acl_entity_segment_store.id_acl_entity_segment_store')),
            ));

        // Act
        $this->invokeExtendQueryWithSegmentConditions($query, $secondOccurrenceStoreJoin, $secondOccurrenceSegmentJoin);
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

        $query = $this->createMock(ModelCriteria::class);

        $this->configurePrimaryKeyColumns([
            'spy_acl_entity_segment_store' => 'id_acl_entity_segment_store',
            'spy_store' => 'id_store',
        ]);

        // Assert
        $query->expects($this->once())
            ->method('where')
            ->with('(spy_acl_entity_segment_store.id_acl_entity_segment_store IS NOT NULL OR spy_store.id_store IS NULL)');

        // Act
        $result = $this->invokeExtendQueryWithSegmentConditions($query, $storeJoin, $segmentJoin);

        $this->assertSame($query, $result);
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

    protected function invokeExtendQueryWithSegmentConditions(ModelCriteria $query, Join $join, Join $aclEntitySegmentJoin): ModelCriteria
    {
        $reflectionMethod = new ReflectionMethod(LeftAclJoin::class, 'extendQueryWithSegmentConditions');
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invoke($this->leftAclJoin, $query, $join, $aclEntitySegmentJoin);
    }
}
