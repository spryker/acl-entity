<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\AclEntity;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface AclEntityConstants
{
    /**
     * @var int
     */
    public const OPERATION_MASK_READ = 0b1;

    /**
     * @var int
     */
    public const OPERATION_MASK_CREATE = 0b10;

    /**
     * @var int
     */
    public const OPERATION_MASK_UPDATE = 0b100;

    /**
     * @var int
     */
    public const OPERATION_MASK_DELETE = 0b1000;

    /**
     * @var int
     */
    public const OPERATION_MASK_CRUD = 0b1111;

    /**
     * @var string
     */
    public const OPERATION_CREATE = 'create';

    /**
     * @var string
     */
    public const OPERATION_UPDATE = 'update';

    /**
     * @var string
     */
    public const OPERATION_DELETE = 'delete';

    /**
     * @var string
     */
    public const SCOPE_GLOBAL = 'global';

    /**
     * @var string
     */
    public const SCOPE_SEGMENT = 'segment';

    /**
     * @var string
     */
    public const SCOPE_INHERITED = 'inherited';

    /**
     * @var string
     */
    public const SCOPE_DEFAULT = 'default';

    /**
     * @var string
     */
    public const WHILDCARD_ENTITY = '*';

    /**
     * Specification:
     * - Enables or disables ACL entity metadata config validation at runtime.
     * - When disabled, validation should be performed via console command in CI/deploy pipeline.
     * - Defaults to false. Set to true only in development environments. In production, disable this and run `acl-entity:metadata:validate` during deployment.
     *
     * @api
     *
     * @var string
     */
    public const ACL_ENTITY_RUNTIME_VALIDATION_ENABLED = 'ACL_ENTITY:ACL_ENTITY_RUNTIME_VALIDATION_ENABLED';
}
