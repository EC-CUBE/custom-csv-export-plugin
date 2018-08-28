<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomCsvExport\Validator;

use Symfony\Component\Validator\Constraint;

class SqlCheck extends Constraint
{
    public $message = 'custom_csv_export.admin.message.validate.001';
}
