<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\AclEntity\SegmentConnectorGenerator;

interface SegmentConnectorGeneratorInterface
{
    public function generateConnectorTableName(string $baseTableName): string;

    public function generateConnectorTableIdColumnName(string $connectorTableName): string;

    public function generateConnectorClassName(string $baseClass): string;

    public function generateConnectorRelationName(string $baseClassName): string;

    public function generateConnectorGetter(string $baseClass): string;

    public function generateConnectorReferenceGetter(string $baseClass): string;

    public function generateConnectorReferenceSetter(string $baseClass): string;

    public function generateConnectorReferenceColumnName(string $referencedTableName): string;

    public function generateSegmentConnectorTableUniqueConstraintName(string $referencedTableName, string $referencedColumnName): string;
}
