<?php

namespace Plugin\CsvSql\Entity;

class CsvSql extends \Eccube\Entity\AbstractEntity
{
    private $id;

    private $sql_name;

    private $csv_sql;

    private $create_date;

    private $update_date;

    private $del_flg;

    private $tabledata;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getSqlName()
    {
        return $this->sql_name;
    }

    public function setSqlName($sql_name)
    {
        $this->sql_name = $sql_name;

        return $this;
    }

    public function getCsvSql()
    {
        return $this->csv_sql;
    }

    public function setCsvSql($csv_sql)
    {
        $this->csv_sql = $csv_sql;

        return $this;
    }

    public function getCreateDate()
    {
        return $this->create_date;
    }

    public function setCreateDate($create_date)
    {
        $this->create_date = $create_date;

        return $this;
    }

    public function getUpdateDate()
    {
        return $this->update_date;
    }

    public function setUpdateDate($update_date)
    {
        $this->update_date = $update_date;

        return $this;
    }

    public function getDelFlg()
    {
        return $this->del_flg;
    }

    public function setDelFlg($del_flg)
    {
        $this->del_flg = $del_flg;

        return $this;
    }

    public function getTabledata()
    {
        return $this->tabledata;
    }

    public function setTabledata($tabledata)
    {
        $this->tabledata = $tabledata;

        return $this;
    }
}
