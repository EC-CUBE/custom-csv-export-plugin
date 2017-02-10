<?php

namespace Plugin\CsvSql\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class SqlCheck extends Constraint
{
    public $message = 'SQL文が不正です。SQL文を見直してください';
}
