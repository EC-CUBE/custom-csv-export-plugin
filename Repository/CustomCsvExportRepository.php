<?php
/*
 * This file is part of the Custom Csv Export Plugin
 *
 * Copyright (C) 2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomCsvExport\Repository;

use Doctrine\ORM\EntityRepository;
use Eccube\Common\Constant;
use Plugin\CustomCsvExport\Entity\CustomCsvExport;

class CustomCsvExportRepository extends EntityRepository
{
    /**
     * 設定SQL一覧を取得する.
     *
     * @return \Plugin\CustomCsvExport\Entity\CustomCsvExport[] 設定SQLの配列
     */
    public function getList()
    {
        $qb = $this->createQueryBuilder('cs');
        $CustomCsvExports = $qb->getQuery()->getResult();

        return $CustomCsvExports;
    }

    /**
     * CSV出力用の設定SQL実行結果を取得する.
     *
     * @param $custom_csv_export SQL文
     * @return array 実行結果
     */
    public function getArrayList($custom_csv_export)
    {
        $em = $this->getEntityManager();
        $qb = $em->getConnection()->prepare('SELECT '.$custom_csv_export);
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
     * @param CustomCsvExport $CustomCsvExport 設定SQL
     * @return bool 成功した場合 true
     */
    public function save(CustomCsvExport $CustomCsvExport)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            if (!$CustomCsvExport->getId()) {
                $CustomCsvExport->setDelFlg(0);

                $em->createQueryBuilder('cs')
                    ->update('Plugin\CustomCsvExport\Entity\CustomCsvExport', 'cs')
                    ->getQuery();
            }

            $em->persist($CustomCsvExport);
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
     * @param CustomCsvExport $CustomCsvExport 削除対象の設定SQL
     * @return bool 成功した場合 true
     */
    public function delete(CustomCsvExport $CustomCsvExport)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            $CustomCsvExport->setDelFlg(Constant::ENABLED);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            return false;
        }

        return true;
    }
}
