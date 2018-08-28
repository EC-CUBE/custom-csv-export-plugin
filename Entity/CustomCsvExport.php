<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomCsvExport\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomCsvExport
 *
 * @ORM\Table(name="plg_custom_csv_export")
 * @ORM\Entity(repositoryClass="Plugin\CustomCsvExport\Repository\CustomCsvExportRepository")
 */
class CustomCsvExport extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="sql_name", type="string")
     */
    private $sql_name;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_sql", type="text")
     */
    private $custom_sql;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * @var integer
     *
     * @ORM\Column(name="del_flg", type="boolean", options={"default": 0})
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
     *
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
     * @param $custom_sql
     *
     * @return $this
     */
    public function setCustomSql($custom_sql)
    {
        $this->custom_sql = $custom_sql;

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
     *
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
     *
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
     *
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
