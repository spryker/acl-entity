<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AclEntity\Persistence\Exception;

use Propel\Runtime\ActiveQuery\ModelCriteria;

class AmbiguousSelectColumnException extends AclEntityException
{
    protected const string MESSAGE_TEMPLATE = 'AclEntity join made select column "%s" ambiguous against table "%s". ' .
        'Qualify it in the originating repository (e.g. "<table>.%s" or a TableMap::COL_* constant).';

    protected string $sql;

    public function __construct(ModelCriteria $query, string $column, string $ambiguousWithTableName)
    {
        $params = [];
        $this->sql = $query->createSelectSql($params);

        parent::__construct(sprintf(static::MESSAGE_TEMPLATE, $column, $ambiguousWithTableName, $column));
    }

    public function getSql(): string
    {
        return $this->sql;
    }
}
