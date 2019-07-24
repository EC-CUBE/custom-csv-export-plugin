<?php
/*
 * This file is part of the Custom Csv Export Plugin
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomCsvExport\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Plugin\CustomCsvExport\Entity\CustomCsvExport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomCsvExportController extends AbstractController
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
            $TargetCustomCsvExport = $app['custom_csv_export.repository.custom_csv_export']->find($id);
            if (!$TargetCustomCsvExport) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetCustomCsvExport = new CustomCsvExport();
        }

        $builder = $app['form.factory']->createBuilder('admin_custom_csv_export', $TargetCustomCsvExport);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sql = 'SELECT '.$form['custom_sql']->getData();
            try {
                $result = $app['custom_csv_export.repository.custom_csv_export']->query($sql);

                if ($result) {
                    $status = $app['custom_csv_export.repository.custom_csv_export']->save($TargetCustomCsvExport);

                    if ($status) {
                        $app->addSuccess('SQLを保存しました。', 'admin');

                        return $app->redirect($app->url('plugin_custom_csv_export'));
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

        $CustomCsvExports = $app['custom_csv_export.repository.custom_csv_export']->getList();

        return $app->render('CustomCsvExport/Resource/template/Admin/index.twig', array(
            'form' => $form->createView(),
            'CustomCsvExports' => $CustomCsvExports,
            'TargetCustomCsvExport' => $TargetCustomCsvExport,
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

        $TargetCustomCsvExport = $app['custom_csv_export.repository.custom_csv_export']->find($id);

        if (!$TargetCustomCsvExport) {
            $app->deleteMessage();

            return $app->redirect($app->url('plugin_custom_csv_export'));
        }

        $status = $app['custom_csv_export.repository.custom_csv_export']->delete($TargetCustomCsvExport);

        if ($status) {
            $app->addSuccess('SQLを削除しました。', 'admin');
        } else {
            $app->addError('SQLを削除できませんでした。', 'admin');
        }

        return $app->redirect($app->url('plugin_custom_csv_export'));
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

        $TargetCustomCsvExport = $app['custom_csv_export.repository.custom_csv_export']->find($id);
        if (!$TargetCustomCsvExport) {
            throw new NotFoundHttpException();
        }

        $response = new StreamedResponse();

        $csv_data = $app['custom_csv_export.repository.custom_csv_export']->getArrayList($TargetCustomCsvExport->getCustomSql());

        if (count($csv_data) > 0) {

            // ヘッダー行の抽出
            $csv_header = array();
            foreach ($csv_data as $csv_row) {
  
                foreach ($csv_row as $key => $value) {
                    $csv_header[$key] = mb_convert_encoding($key, $app['config']['csv_export_encoding'], 'UTF-8');
                }
                break;
            }

            $response->setCallback(function () use ($app, $request, $csv_header, $csv_data) {

                $fp = fopen('php://output', 'w');
                // ヘッダー行の出力
                fputcsv($fp, $csv_header, $app['config']['csv_export_separator']);

                // データを出力
                foreach ($csv_data as $csv_row) {
                    $row = array();
                    foreach ($csv_header as $headerKey => $header_name) {
                        mb_convert_variables($app['config']['csv_export_encoding'], 'UTF-8', $csv_row[$headerKey]);
                        $row[] = $csv_row[$headerKey];
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

        return $app->redirect($app->url('plugin_custom_csv_export'));
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
            $TargetCustomCsvExport = $app['custom_csv_export.repository.custom_csv_export']->find($id);
            if (!$TargetCustomCsvExport) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetCustomCsvExport = new CustomCsvExport();
        }

        $builder = $app['form.factory']->createBuilder('admin_custom_csv_export', $TargetCustomCsvExport);

        $form = $builder->getForm();

        $form->handleRequest($request);

        $message = null;
        if ($form->isSubmitted() && $form->isValid()) {

            if (!is_null($form['custom_sql']->getData())) {
                $sql = 'SELECT '.$form['custom_sql']->getData();
                try {
                    $result = $app['custom_csv_export.repository.custom_csv_export']->query($sql);
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

        $CustomCsvExports = $app['custom_csv_export.repository.custom_csv_export']->getList();

        return $app->render('CustomCsvExport/Resource/template/Admin/index.twig', array(
            'form' => $form->createView(),
            'CustomCsvExports' => $CustomCsvExports,
            'message' => $message,
            'TargetCustomCsvExport' => $TargetCustomCsvExport,
        ));
    }
}
