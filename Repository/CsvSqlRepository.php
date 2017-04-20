<?php
/*
 * This file is part of the Related Product plugin
 *
 * Copyright (C) 2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CsvSql\Repository;

use Doctrine\ORM\EntityRepository;
use Eccube\Common\Constant;
use Plugin\CsvSql\Entity\CsvSql;

class CsvSqlRepository extends EntityRepository
{
    /**
     * 設定SQL一覧を取得する.
     *
     * @return \Plugin\CsvSql\Entity\CsvSql[] 設定SQLの配列
     */
    public function getList()
    {
        $qb = $this->createQueryBuilder('cs');
        $CsvSqls = $qb->getQuery()->getResult();

        return $CsvSqls;
    }

    /**
     * CSV出力用の設定SQL実行結果を取得する.
     *
     * @param $csv_sql SQL文
     * @return array 実行結果
     */
    public function getArrayList($csv_sql)
    {
        $em = $this->getEntityManager();
        $qb = $em->getConnection()->prepare('SELECT '.$csv_sql);
        $qb->execute();
        $result = $qb->fetchAll();

        return $result;
    }

    /**
     * 入力されたSQL文が正しいかどうか判定する
     *
     * @param $sql SQL文
     * @return bool SQLの実行結果
     */
    public function query($sql)
    {
        $em = $this->getEntityManager();
        $qb = $em->getConnection()->prepare($sql);

        $result = $qb->execute();

        return $result;
    }

    /**
     * 設定SQLを保存する.
     *
     * @param CsvSql $CsvSql 設定SQL
     * @return bool 成功した場合 true
     */
    public function save(CsvSql $CsvSql)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            if (!$CsvSql->getId()) {
                $CsvSql->setDelFlg(0);

                $em->createQueryBuilder('cs')
                    ->update('Plugin\CsvSql\Entity\CsvSql', 'cs')
                    ->getQuery();
            }

            $em->persist($CsvSql);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            return false;
        }

        return true;
    }

    /**
     * 設定SQLを削除する.
     *
     * @param CsvSql $CsvSql 削除対象の設定SQL
     * @return bool 成功した場合 true
     */
    public function delete(CsvSql $CsvSql)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            $CsvSql->setDelFlg(Constant::ENABLED);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            return false;
        }

        return true;
    }
}
