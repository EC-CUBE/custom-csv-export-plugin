<?php
/*
 * This file is part of the Related Product plugin
 *
 * Copyright (C) 2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CsvSql\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SqlCheckValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SqlCheck) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\SqlCheck');
        }

        $error = $this->sqlValidation($value);
        if ($error) {
            $this->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }

        if (false === $value || (empty($value) && '0' != $value)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->addViolation();
            } else {
                $constraint->message = 'SQL文を入力してください。';
                $this->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->addViolation();
            }
        }
    }

    /**
     * SQLの入力チェック.
     *
     * @param $sql SQL文
     * @return bool
     */
    private function sqlValidation($sql)
    {
        // 入力チェック
        $error = false;

        $denyList = $this->lfGetSqlDenyList();

        foreach ($denyList as $keyword) {
            if (preg_match('/'.$keyword.'/', $sql)) {
                $error = true;
            }
        }

        return $error;
    }

    /**
     * SQL文に含めることを許可しないSQLキーワード
     * 基本的にEC-CUBEのデータを取得するために必要なコマンドしか許可しない。複数クエリも不可.
     *
     * FIXME: キーワードの精査。危険な部分なのでプログラム埋め込みで実装しました。mtb化の有無判断必要。
     *
     * @return string[] 不許可ワード配列
     */
    private function lfGetSqlDenyList()
    {
        $arrList = array(
            ';',
            'SELECT',
            'CREATE',
            'INSERT',
            'UPDATE',
            'DELETE',
            'ALTER',
            'ABORT',
            'ANALYZE',
            'CLUSTER',
            'COMMENT',
            'COPY',
            'DECLARE',
            'DISCARD',
            'DO',
            'DROP',
            'EXECUTE',
            'EXPLAIN',
            'GRANT',
            'LISTEN',
            'LOAD',
            'LOCK',
            'NOTIFY',
            'PREPARE',
            'REASSIGN',
            // 'REINDEX\s', // REINDEXは許可で良いかなと
            'RELEASE\sSAVEPOINT',
            'RENAME',
            'REST',
            'REVOKE',
            'SAVEPOINT',
            '\sSET\s', // OFFSETを誤検知しないように先頭・末尾に\sを指定
            'SHOW',
            'START\sTRANSACTION',
            'TRUNCATE',
            'UNLISTEN',
            'VACCUM',
            'HANDLER',
            'LOAD\sDATA\s',
            'LOAD\sXML',
            'REPLACE',
            'OPTIMIZE',
            'REPAIR',
            'INSTALL\sPLUGIN',
            'UNINSTALL\sPLUGIN',
            'BINLOG',
            'KILL',
            'RESET',
            'PURGE',
            'CHANGE\sMASTER',
            'START\sSLAVE',
            'STOP\sSLAVE',
            'MASTER\sPOS\sWAIT',
            'SIGNAL',
            'RESIGNAL',
            'RETURN',
            'USE',
            'HELP',
        );

        return $arrList;
    }
}
