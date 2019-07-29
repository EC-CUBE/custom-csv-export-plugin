<?php
/*
 * This file is part of the Custom Csv Export Plugin
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomCsvExport\Validator;

use Symfony\Component\Validator\Constraint;

class SqlCheck extends Constraint
{
    public $message = 'SQL文が不正です。SQL文を見直してください';
}
