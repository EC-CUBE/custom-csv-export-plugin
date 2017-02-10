<?php

namespace Plugin\CsvSql\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class CsvSqlServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // Controller
        $app->match('/admin/setting/shop/csv_sql', 'Plugin\CsvSql\Controller\CsvSqlController::index')->bind('admin_shop_csv_sql');
        $app->match('/admin/setting/shop/csv_sql/{id}/edit', 'Plugin\CsvSql\Controller\CsvSqlController::index')->assert('id', '\d+')->bind('admin_shop_csv_sql_edit');
        $app->delete('/admin/setting/shop/csv_sql/{id}/delete', 'Plugin\CsvSql\Controller\CsvSqlController::delete')->assert('id', '\d+')->bind('admin_shop_csv_sql_delete');
        $app->match('/admin/setting/shop/csv_sql/{id}/output', 'Plugin\CsvSql\Controller\CsvSqlController::csvOutput')->assert('id', '\d+')->bind('admin_shop_csv_sql_output');
        $app->match('/admin/setting/shop/csv_sql/{id}/confirm', 'Plugin\CsvSql\Controller\CsvSqlController::sqlConfirm')->assert('id', '\d+')->bind('admin_shop_csv_sql_edit_confirm');
        $app->match('/admin/setting/shop/csv_sql/confirm', 'Plugin\CsvSql\Controller\CsvSqlController::sqlConfirm')->bind('admin_shop_csv_sql_confirm');

        // Form/Type
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\CsvSql\Form\Type\CsvSqlType($app);

            return $types;
        }));

        //Repository
        $app['csv_sql.repository.csv_sql'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CsvSql\Entity\CsvSql');
        });
/*
        // 管理メニュー表示
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $config['nav'][3]['child'][] = array(
                    'id' => 'admin_csv_sql',
                    'name' => 'CSV出力用高度な設定',
                    'url' => 'admin_shop_csv_sql',
            );

            return $config;
        }));
*/
    }

    public function boot(BaseApplication $app)
    {
    }
}
