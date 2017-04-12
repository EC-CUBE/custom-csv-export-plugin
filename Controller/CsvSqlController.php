<?php
/*
 * This file is part of the Related Product plugin
 *
 * Copyright (C) 2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CsvSql\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Plugin\CsvSql\Entity\CsvSql;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CsvSqlController extends AbstractController
{

    /**
     * CSV一覧画面
     *
     * @param Application $app
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request, $id = null)
    {
        if ($id) {
            $TargetCsvSql = $app['csv_sql.repository.csv_sql']->find($id);
            if (!$TargetCsvSql) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetCsvSql = new CsvSql();
        }

        $builder = $app['form.factory']->createBuilder('admin_csv_sql', $TargetCsvSql);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sql = 'SELECT '.$form['csv_sql']->getData();
            try {
                $result = $app['csv_sql.repository.csv_sql']->query($sql);

                if ($result) {
                    $status = $app['csv_sql.repository.csv_sql']->save($TargetCsvSql);

                    if ($status) {
                        $app->addSuccess('SQLを保存しました。', 'admin');

                        return $app->redirect($app->url('admin_shop_csv_sql'));
                    } else {
                        $app->addError('SQLを保存できませんでした。', 'admin');
                    }
                } else {
                    $app->addError('SQLを保存できませんでした。', 'admin');
                }
            } catch (\Exception $e) {
                $app->addError('SQLを保存できませんでした。SQL文を正しく入力してください。', 'admin');
            }
        }

        $CsvSqls = $app['csv_sql.repository.csv_sql']->getList();

        return $app->render('CsvSql/Resource/template/Admin/index.twig', array(
            'form' => $form->createView(),
            'CsvSqls' => $CsvSqls,
            'TargetCsvSql' => $TargetCsvSql,
        ));
    }

    /**
     * CSV削除
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetCsvSql = $app['csv_sql.repository.csv_sql']->find($id);

        if (!$TargetCsvSql) {
            $app->deleteMessage();

            return $app->redirect($app->url('admin_shop_csv_sql'));
        }

        $status = $app['csv_sql.repository.csv_sql']->delete($TargetCsvSql);

        if ($status) {
            $app->addSuccess('SQLを削除しました。', 'admin');
        } else {
            $app->addError('SQLを削除できませんでした。', 'admin');
        }

        return $app->redirect($app->url('admin_shop_csv_sql'));
    }

    /**
     * CSV出力.
     *
     * @param Application $app
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|StreamedResponse
     */
    public function csvOutput(Application $app, Request $request, $id = null)
    {
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $app['orm.em'];
        $em->getConfiguration()->setSQLLogger(null);

        $TargetCsvSql = $app['csv_sql.repository.csv_sql']->find($id);
        if (!$TargetCsvSql) {
            throw new NotFoundHttpException();
        }

        $response = new StreamedResponse();

        $csv_data = $app['csv_sql.repository.csv_sql']->getArrayList($TargetCsvSql->getCsvSql());

        if (count($csv_data) > 0) {

            // ヘッダー行の抽出
            $csv_header = array();
            foreach ($csv_data as $csv_row) {
                foreach ($csv_row as $key => $value) {
                    $csv_header[] = $key;
                }
            }
            $csv_header = array_values(array_unique($csv_header));

            $response->setCallback(function () use ($app, $request, $csv_header, $csv_data) {

                $fp = fopen('php://output', 'w');
                // ヘッダー行の出力
                fputcsv($fp, $csv_header, $app['config']['csv_export_separator']);

                // データを出力
                foreach ($csv_data as $csv_row) {
                    $row = array();
                    foreach ($csv_header as $header_name) {
                        mb_convert_variables($app['config']['csv_export_encoding'], 'UTF-8', $csv_row[$header_name]);
                        $row[] = $csv_row[$header_name];
                    }
                    fputcsv($fp, $row, $app['config']['csv_export_separator']);
                }

                fclose($fp);

            });

            $now = new \DateTime();
            $filename = 'csv_'.$now->format('YmdHis').'.csv';
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
            $response->send();

            return $response;
        }

        $app->addError('CSVを出力できませんでした。', 'admin');

        return $app->redirect($app->url('admin_shop_csv_sql'));
    }

    /**
     * SQL確認.
     *
     * @param Application $app
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sqlConfirm(Application $app, Request $request, $id = null)
    {
        if ($id) {
            $TargetCsvSql = $app['csv_sql.repository.csv_sql']->find($id);
            if (!$TargetCsvSql) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetCsvSql = new CsvSql();
        }

        $builder = $app['form.factory']->createBuilder('admin_csv_sql', $TargetCsvSql);

        $form = $builder->getForm();

        $form->handleRequest($request);

        $message = null;
        if ($form->isSubmitted() && $form->isValid()) {

            if (!is_null($form['csv_sql']->getData())) {
                $sql = 'SELECT '.$form['csv_sql']->getData();
                try {
                    $result = $app['csv_sql.repository.csv_sql']->query($sql);
                    if ($result) {
                        $message = 'エラーはありません。';
                    } else {
                        $message = 'エラーが発生しました。';
                    }
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                }
            }
        }

        $CsvSqls = $app['csv_sql.repository.csv_sql']->getList();

        return $app->render('CsvSql/Resource/template/Admin/index.twig', array(
            'form' => $form->createView(),
            'CsvSqls' => $CsvSqls,
            'message' => $message,
            'TargetCsvSql' => $TargetCsvSql,
        ));
    }
}
