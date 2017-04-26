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

        $prohibitedStr = str_replace(array('|', '/'), array('\|', '\/'), $denyList);
	$pattern = '/' . join('|', $prohibitedStr) . '/i';
	if (preg_match_all($pattern, $sql, $matches)) {
                $error = true;
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
            'CREATE\s',
            'INSERT\s',
            'UPDATE\s',
            'DELETE\s',
            'ALTER\s',
            'ABORT\s',
            'ANALYZE\s',
            'CLUSTER\s',
            'COMMENT\s',
            'COPY\s',
            'DECLARE\s',
            'DISCARD\s',
            'DO\s',
            'DROP\s',
            'EXECUTE\s',
            'EXPLAIN\s',
            'GRANT\s',
            'LISTEN\s',
            'LOAD\s',
            'LOCK\s',
            'NOTIFY\s',
            'PREPARE\s',
            'REASSIGN\s',
            'RELEASE\sSAVEPOINT',
            'RENAME\s',
            'REST\s',
            'REVOKE\s',
            'SAVEPOINT\s',
            '\sSET\s', // OFFSETを誤検知しないように先頭・末尾に\sを指定
            'SHOW\s',
            'START\sTRANSACTION',
            'TRUNCATE\s',
            'UNLISTEN\s',
            'VACCUM\s',
            'HANDLER\s',
            'LOAD\sDATA\s',
            'LOAD\sXML\s',
            'REPLACE\s',
            'OPTIMIZE\s',
            'REPAIR\s',
            'INSTALL\sPLUGIN\s',
            'UNINSTALL\sPLUGIN\s',
            'BINLOG\s',
            'KILL\s',
            'RESET\s',
            'PURGE\s',
            'CHANGE\sMASTER',
            'START\sSLAVE',
            'STOP\sSLAVE',
            'MASTER\sPOS\sWAIT',
            'SIGNAL\s',
            'RESIGNAL\s',
            'RETURN\s',
            'USE\s',
            'HELP\s',
        );

        return $arrList;
    }
}
