<?php
/**
 * Created by PhpStorm.
 * User: chihiro_adachi
 * Date: 2018/02/26
 * Time: 16:14
 */

namespace Plugin\CustomCsvExport;


use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{
    public static function getNav()
    {
        return [
            'setting' => [
                'id' => 'admin_custom_csv_export',
                'name' => 'カスタムCSV出力',
                'url' => 'plugin_custom_csv_export',
            ],
        ];
    }
}
