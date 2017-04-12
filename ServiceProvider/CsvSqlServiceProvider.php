<?php
/*
 * This file is part of the Related Product plugin
 *
 * Copyright (C) 2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CsvSql\ServiceProvider;

use Eccube\Common\Constant;
use Plugin\CsvSql\Form\Type\CsvSqlType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class CsvSqlServiceProvider implements ServiceProviderInterface
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

        $admin->match('/setting/shop/csv_sql', 'Plugin\CsvSql\Controller\CsvSqlController::index')->bind('admin_shop_csv_sql');
        $admin->match('/setting/shop/csv_sql/{id}/edit', 'Plugin\CsvSql\Controller\CsvSqlController::index')->assert('id', '\d+')->bind('admin_shop_csv_sql_edit');
        $admin->delete('/setting/shop/csv_sql/{id}/delete', 'Plugin\CsvSql\Controller\CsvSqlController::delete')->assert('id', '\d+')->bind('admin_shop_csv_sql_delete');
        $admin->match('/setting/shop/csv_sql/{id}/output', 'Plugin\CsvSql\Controller\CsvSqlController::csvOutput')->assert('id', '\d+')->bind('admin_shop_csv_sql_output');
        $admin->match('/setting/shop/csv_sql/{id}/confirm', 'Plugin\CsvSql\Controller\CsvSqlController::sqlConfirm')->assert('id', '\d+')->bind('admin_shop_csv_sql_edit_confirm');
        $admin->match('/setting/shop/csv_sql/confirm', 'Plugin\CsvSql\Controller\CsvSqlController::sqlConfirm')->bind('admin_shop_csv_sql_confirm');

        $app->mount('/'.trim($app['config']['admin_route'], '/').'/', $admin);

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new CsvSqlType($app);

            return $types;
        }));

        // Repository
        $app['csv_sql.repository.csv_sql'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CsvSql\Entity\CsvSql');
        });

        // 管理画面メニュー追加
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $config['nav'][4]['child'][0]['child'][] = array(
                'id' => 'admin_csv_sql',
                'name' => 'CSV出力高度な設定',
                'url' => 'admin_shop_csv_sql',
            );

            return $config;
        }));

    }

    public function boot(BaseApplication $app)
    {
    }
}
