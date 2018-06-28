<?php
/*
 * This file is part of the Custom Csv Export Plugin
 *
 * Copyright (C) 2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomCsvExport\Validator;

use Symfony\Component\Validator\Constraint;

class SqlCheck extends Constraint
{
    public $message = 'plugin.CustomCsvExport.admin.message.validate.001';
}
