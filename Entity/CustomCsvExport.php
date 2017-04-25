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
    private $custom_csv_export;

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
     * Set custom_csv_export
     *
     * @param string $customCsvExport
     * @return CustomCsvExport
     */
    public function setCustomCsvExport($customCsvExport)
    {
        $this->custom_csv_export = $customCsvExport;

        return $this;
    }

    /**
     * Get custom_csv_export
     *
     * @return string 
     */
    public function getCustomCsvExport()
    {
        return $this->custom_csv_export;
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
