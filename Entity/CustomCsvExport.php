<?php

namespace Plugin\CustomCsvExport\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomCsvExport
 */
class CustomCsvExport extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $sql_name;

    /**
     * @var string
     */
    private $custom_sql;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var integer
     */
    private $del_flg = 0;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sql_name
     *
     * @param string $sqlName
     * @return CustomCsvExport
     */
    public function setSqlName($sqlName)
    {
        $this->sql_name = $sqlName;

        return $this;
    }

    /**
     * Get sql_name
     *
     * @return string 
     */
    public function getSqlName()
    {
        return $this->sql_name;
    }

    /**
     * Set custom_sql
     *
     * @param string $customCsvExport
     * @return CustomCsvExport
     */
    public function setCustomSql($customSql)
    {
        $this->custom_sql = $customSql;

        return $this;
    }

    /**
     * Get custom_sql
     *
     * @return string 
     */
    public function getCustomSql()
    {
        return $this->custom_sql;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return CustomCsvExport
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return CustomCsvExport
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return CustomCsvExport
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer 
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

}
