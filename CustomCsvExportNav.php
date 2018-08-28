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

namespace Plugin\CustomCsvExport;

use Eccube\Common\EccubeNav;

class CustomCsvExportNav implements EccubeNav
{
    public static function getNav()
    {
        return [
            'setting' => [
                'children' => [
                    'CustomCsvExport' => [
                        'id' => 'custom_csv_export_admin',
                        'name' => 'custom_csv_export.admin.nav.menu',
                        'url' => 'custom_csv_export_admin',
                    ],
                ],
            ],
        ];
    }
}
