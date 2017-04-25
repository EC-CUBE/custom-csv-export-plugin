<?php
/*
 * This file is part of the Related Product plugin
 *
 * Copyright (C) 2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomCsvExport\ServiceProvider;

use Eccube\Common\Constant;
use Plugin\CustomCsvExport\Form\Type\CustomCsvExportType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class CustomCsvExportServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // Controller
        // 管理画面定義
        $admin = $app['controllers_factory'];
        // 強制SSL
        if ($app['config']['force_ssl'] == Constant::ENABLED) {
            $admin->requireHttps();
        }

        $admin->match('/setting/shop/custom_csv_export', 'Plugin\CustomCsvExport\Controller\CustomCsvExportController::index')->bind('admin_shop_custom_csv_export');
        $admin->match('/setting/shop/custom_csv_export/{id}/edit', 'Plugin\CustomCsvExport\Controller\CustomCsvExportController::index')->assert('id', '\d+')->bind('admin_shop_custom_csv_export_edit');
        $admin->delete('/setting/shop/custom_csv_export/{id}/delete', 'Plugin\CustomCsvExport\Controller\CustomCsvExportController::delete')->assert('id', '\d+')->bind('admin_shop_custom_csv_export_delete');
        $admin->match('/setting/shop/custom_csv_export/{id}/output', 'Plugin\CustomCsvExport\Controller\CustomCsvExportController::csvOutput')->assert('id', '\d+')->bind('admin_shop_custom_csv_export_output');
        $admin->match('/setting/shop/custom_csv_export/{id}/confirm', 'Plugin\CustomCsvExport\Controller\CustomCsvExportController::sqlConfirm')->assert('id', '\d+')->bind('admin_shop_custom_csv_export_edit_confirm');
        $admin->match('/setting/shop/custom_csv_export/confirm', 'Plugin\CustomCsvExport\Controller\CustomCsvExportController::sqlConfirm')->bind('admin_shop_custom_csv_export_confirm');

        $app->mount('/'.trim($app['config']['admin_route'], '/').'/', $admin);

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new CustomCsvExportType($app);

            return $types;
        }));

        // Repository
        $app['custom_csv_export.repository.custom_csv_export'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CustomCsvExport\Entity\CustomCsvExport');
        });

        // 管理画面メニュー追加
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $config['nav'][4]['child'][0]['child'][] = array(
                'id' => 'admin_custom_csv_export',
                'name' => 'カスタムCSV出力',
                'url' => 'admin_shop_custom_csv_export',
            );

            return $config;
        }));

    }

    public function boot(BaseApplication $app)
    {
    }
}
