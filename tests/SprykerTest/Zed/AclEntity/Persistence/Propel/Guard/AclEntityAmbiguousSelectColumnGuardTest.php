<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AclEntity\Persistence\Propel\Guard;

use Codeception\Test\Unit;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Map\TableMap;
use Spryker\Zed\AclEntity\Persistence\Exception\AmbiguousSelectColumnException;
use Spryker\Zed\AclEntity\Persistence\Propel\Guard\AclEntityAmbiguousSelectColumnGuard;
use Spryker\Zed\AclEntity\Persistence\Propel\Guard\AclEntityAmbiguousSelectColumnGuardInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AclEntity
 * @group Persistence
 * @group Propel
 * @group Guard
 * @group AclEntityAmbiguousSelectColumnGuardTest
 * Add your own group annotations below this line
 */
class AclEntityAmbiguousSelectColumnGuardTest extends Unit
{
    protected AclEntityAmbiguousSelectColumnGuardInterface $guard;

    protected function _before(): void
    {
        parent::_before();

        $this->guard = new AclEntityAmbiguousSelectColumnGuard();
    }

    public function testGuardDoesNothingWhenAllSelectColumnsAreQualified(): void
    {
        // Arrange
        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['spy_category.id_category']);

        // Assert
        $query->expects($this->never())->method('getJoins');

        // Act
        $this->guard->guardAgainstAmbiguousSelectColumns($query, []);
    }

    public function testGuardDoesNothingWhenAclAddsNoNewJoin(): void
    {
        // Arrange
        $existingJoin = $this->createMock(ModelJoin::class);
        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['name']);
        $query->method('getJoins')->willReturn(['Attribute' => $existingJoin]);

        // Assert
        $existingJoin->expects($this->never())->method('getTableMap');

        // Act
        $this->guard->guardAgainstAmbiguousSelectColumns($query, ['Attribute' => $existingJoin]);
    }

    public function testGuardDoesNothingWhenTheNewJoinHasNoMatchingColumn(): void
    {
        // Arrange
        $unrelatedTableMap = $this->createMock(TableMap::class);
        $unrelatedTableMap->method('hasColumn')->with('name')->willReturn(false);

        $newJoin = $this->createMock(ModelJoin::class);
        $newJoin->method('getTableMap')->willReturn($unrelatedTableMap);

        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['name']);
        $query->method('getJoins')->willReturn(['Store' => $newJoin]);

        // Assert
        $query->expects($this->never())->method('removeSelectColumn');
        $query->expects($this->never())->method('getTableMapOrFail');

        // Act
        $this->guard->guardAgainstAmbiguousSelectColumns($query, []);
    }

    public function testGuardSkipsJoinsThatAreNotModelJoinsWithoutError(): void
    {
        // Arrange
        $plainJoin = $this->createMock(Join::class);

        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['name']);
        $query->method('getJoins')->willReturn(['Store' => $plainJoin]);

        // Assert
        $query->expects($this->never())->method('removeSelectColumn');

        // Act
        $this->guard->guardAgainstAmbiguousSelectColumns($query, []);
    }

    public function testGuardAutoQualifiesWhenOriginalOwnerIsTheRootTable(): void
    {
        // Arrange
        $rootTableMap = $this->createMock(TableMap::class);
        $rootTableMap->method('hasColumn')->with('name')->willReturn(true);
        $rootTableMap->method('getNameOrFail')->willReturn('spy_category');

        $storeTableMap = $this->createMock(TableMap::class);
        $storeTableMap->method('hasColumn')->with('name')->willReturn(true);

        $storeJoin = $this->createMock(ModelJoin::class);
        $storeJoin->method('getTableMap')->willReturn($storeTableMap);

        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['name']);
        $query->method('getJoins')->willReturn(['Store' => $storeJoin]);
        $query->method('getTableMapOrFail')->willReturn($rootTableMap);

        // Assert
        $query->expects($this->once())->method('removeSelectColumn')->with('name');
        $query->expects($this->once())->method('addSelectColumn')->with('spy_category.name');

        // Act
        $this->guard->guardAgainstAmbiguousSelectColumns($query, []);
    }

    public function testGuardThrowsWhenNoPreExistingTableOwnsTheColumnAtAll(): void
    {
        // Arrange
        $rootTableMap = $this->createMock(TableMap::class);
        $rootTableMap->method('hasColumn')->willReturn(false);

        $storeTableMap = $this->createMock(TableMap::class);
        $storeTableMap->method('hasColumn')->willReturn(true);
        $storeTableMap->method('getNameOrFail')->willReturn('spy_store');

        $storeJoin = $this->createMock(ModelJoin::class);
        $storeJoin->method('getTableMap')->willReturn($storeTableMap);

        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['name']);
        $query->method('getJoins')->willReturn(['Store' => $storeJoin]);
        $query->method('getTableMapOrFail')->willReturn($rootTableMap);
        $query->method('createSelectSql')->willReturn('SELECT name FROM spy_category ...');

        // Assert
        $query->expects($this->never())->method('removeSelectColumn');
        $this->expectException(AmbiguousSelectColumnException::class);

        // Act
        $this->guard->guardAgainstAmbiguousSelectColumns($query, []);
    }

    public function testGuardExceptionMessageNamesTheCollidingTableAndColumnAndExposesFullSqlSeparately(): void
    {
        // Arrange
        $rootTableMap = $this->createMock(TableMap::class);
        $rootTableMap->method('hasColumn')->willReturn(false);

        $attributeTableMap = $this->createMock(TableMap::class);
        $attributeTableMap->method('hasColumn')->willReturn(true);
        $attributeTableMap->method('getNameOrFail')->willReturn('spy_category_attribute');

        $storeTableMap = $this->createMock(TableMap::class);
        $storeTableMap->method('hasColumn')->willReturn(true);
        $storeTableMap->method('getNameOrFail')->willReturn('spy_store');

        $attributeJoin = $this->createMock(ModelJoin::class);
        $attributeJoin->method('getTableMap')->willReturn($attributeTableMap);

        $storeJoin = $this->createMock(ModelJoin::class);
        $storeJoin->method('getTableMap')->willReturn($storeTableMap);

        $sql = 'SELECT name FROM spy_category '
            . 'INNER JOIN spy_category_attribute ON (spy_category.id_category=spy_category_attribute.fk_category) '
            . 'INNER JOIN spy_category_store ON (spy_category.id_category=spy_category_store.fk_category) '
            . 'INNER JOIN spy_store ON (spy_category_store.fk_store=spy_store.id_store) '
            . 'WHERE spy_category.id_category=24';

        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['name', 'spy_category.id_category']);
        $query->method('getJoins')->willReturn(['Attribute' => $attributeJoin, 'Store' => $storeJoin]);
        $query->method('getTableMapOrFail')->willReturn($rootTableMap);
        $query->method('createSelectSql')->willReturn($sql);

        try {
            // Act
            $this->guard->guardAgainstAmbiguousSelectColumns($query, ['Attribute' => $attributeJoin]);
            $this->fail('Expected ' . AmbiguousSelectColumnException::class . ' was not thrown.');
        } catch (AmbiguousSelectColumnException $ambiguousSelectColumnException) {
            // Assert
            $this->assertSame(
                'AclEntity join made select column "name" ambiguous against table "spy_store". ' .
                'Qualify it in the originating repository (e.g. "<table>.name" or a TableMap::COL_* constant).',
                $ambiguousSelectColumnException->getMessage(),
            );
            $this->assertSame($sql, $ambiguousSelectColumnException->getSql());
        }
    }

    public function testGuardAutoQualifiesTheOnlySelectColumnWhenItsOriginalTableIsUnambiguous(): void
    {
        // Arrange
        $rootTableMap = $this->createMock(TableMap::class);
        $rootTableMap->method('hasColumn')->with('name')->willReturn(false);

        $attributeTableMap = $this->createMock(TableMap::class);
        $attributeTableMap->method('hasColumn')->with('name')->willReturn(true);
        $attributeTableMap->method('getNameOrFail')->willReturn('spy_category_attribute');

        $storeTableMap = $this->createMock(TableMap::class);
        $storeTableMap->method('hasColumn')->with('name')->willReturn(true);

        $attributeJoin = $this->createMock(ModelJoin::class);
        $attributeJoin->method('getTableMap')->willReturn($attributeTableMap);

        $storeJoin = $this->createMock(ModelJoin::class);
        $storeJoin->method('getTableMap')->willReturn($storeTableMap);

        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['name']);
        $query->method('getJoins')->willReturn(['Attribute' => $attributeJoin, 'Store' => $storeJoin]);
        $query->method('getTableMapOrFail')->willReturn($rootTableMap);

        // Assert
        $query->expects($this->once())->method('removeSelectColumn')->with('name');
        $query->expects($this->once())->method('addSelectColumn')->with('spy_category_attribute.name');

        // Act
        $this->guard->guardAgainstAmbiguousSelectColumns($query, ['Attribute' => $attributeJoin]);
    }

    public function testGuardThrowsWhenQuerySelectsMoreThanOneColumn(): void
    {
        // Arrange
        $rootTableMap = $this->createMock(TableMap::class);
        $rootTableMap->method('hasColumn')->willReturn(false);

        $attributeTableMap = $this->createMock(TableMap::class);
        $attributeTableMap->method('hasColumn')->willReturn(true);
        $attributeTableMap->method('getNameOrFail')->willReturn('spy_category_attribute');

        $storeTableMap = $this->createMock(TableMap::class);
        $storeTableMap->method('hasColumn')->willReturn(true);
        $storeTableMap->method('getNameOrFail')->willReturn('spy_store');

        $attributeJoin = $this->createMock(ModelJoin::class);
        $attributeJoin->method('getTableMap')->willReturn($attributeTableMap);

        $storeJoin = $this->createMock(ModelJoin::class);
        $storeJoin->method('getTableMap')->willReturn($storeTableMap);

        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['name', 'spy_category.id_category']);
        $query->method('getJoins')->willReturn(['Attribute' => $attributeJoin, 'Store' => $storeJoin]);
        $query->method('getTableMapOrFail')->willReturn($rootTableMap);
        $query->method('createSelectSql')->willReturn('SELECT name, spy_category.id_category FROM spy_category ...');

        // Assert
        $query->expects($this->never())->method('removeSelectColumn');
        $this->expectException(AmbiguousSelectColumnException::class);
        $this->expectExceptionMessage('spy_store');

        // Act
        $this->guard->guardAgainstAmbiguousSelectColumns($query, ['Attribute' => $attributeJoin]);
    }

    public function testGuardThrowsWhenColumnAlreadyExistedOnMultipleTablesBeforeAclJoined(): void
    {
        // Arrange
        $rootTableMap = $this->createMock(TableMap::class);
        $rootTableMap->method('hasColumn')->willReturn(true);
        $rootTableMap->method('getNameOrFail')->willReturn('spy_category');

        $attributeTableMap = $this->createMock(TableMap::class);
        $attributeTableMap->method('hasColumn')->willReturn(true);
        $attributeTableMap->method('getNameOrFail')->willReturn('spy_category_attribute');

        $storeTableMap = $this->createMock(TableMap::class);
        $storeTableMap->method('hasColumn')->willReturn(true);
        $storeTableMap->method('getNameOrFail')->willReturn('spy_store');

        $attributeJoin = $this->createMock(ModelJoin::class);
        $attributeJoin->method('getTableMap')->willReturn($attributeTableMap);

        $storeJoin = $this->createMock(ModelJoin::class);
        $storeJoin->method('getTableMap')->willReturn($storeTableMap);

        $query = $this->createMock(ModelCriteria::class);
        $query->method('getSelectColumns')->willReturn(['name']);
        $query->method('getJoins')->willReturn(['Attribute' => $attributeJoin, 'Store' => $storeJoin]);
        $query->method('getTableMapOrFail')->willReturn($rootTableMap);
        $query->method('createSelectSql')->willReturn('SELECT name FROM spy_category ...');

        // Assert
        $query->expects($this->never())->method('removeSelectColumn');
        $this->expectException(AmbiguousSelectColumnException::class);

        // Act
        $this->guard->guardAgainstAmbiguousSelectColumns($query, ['Attribute' => $attributeJoin]);
    }
}
