<?php

namespace Plugin\CsvSql\Repository;

use Doctrine\ORM\EntityRepository;

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
        $CsvSqls = $qb->getQuery()
        ->getResult();

        return $CsvSqls;
    }

    /**
     * CSV出力用の設定SQL実行結果を取得する.
     * @param $csv_sql SQL文
     *
     * @return result 実行結果
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
     * @param $sql SQL文
     *
     * @return result SQL の実行結果
     */
    public function query($sql)
    {
        $em = $this->getEntityManager();
        $qb = $em->getConnection()->prepare($sql);

        try {
            $result = $qb->execute();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $result;
    }

    /**
     * 設定SQLを保存する.
     *
     * @param \Plugin\CsvSql\Entity\CsvSql $CsvSql 設定SQL
     *
     * @return bool 成功した場合 true
     */
    public function save(\Plugin\CsvSql\Entity\CsvSql $CsvSql)
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
     * @param \Plugin\CsvSql\Entity\CsvSql $CsvSql 削除対象の設定SQL
     *
     * @return bool 成功した場合 true,
     */
    public function delete(\Plugin\CsvSql\Entity\CsvSql $CsvSql)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            $CsvSql->setDelFlg(1);
            $em->persist($CsvSql);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            return false;
        }

        return true;
    }
}
