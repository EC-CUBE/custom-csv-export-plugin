<?php

namespace Plugin\CsvSql\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CsvSqlController extends AbstractController
{
    public function index(Application $app, Request $request, $id = null)
    {
        if ($id) {
            $TargetCsvSql = $app['csv_sql.repository.csv_sql']->find($id);
            if (!$TargetCsvSql) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetCsvSql = new \Plugin\CsvSql\Entity\CsvSql();
        }

        //
        $builder = $app['form.factory']
        ->createBuilder('admin_csv_sql', $TargetCsvSql);

        $form = $builder->getForm();

        //
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            $errors = $form['csv_sql']->getErrors();

            if ($form->isValid()) {
                $sql = 'SELECT '.$form['csv_sql']->getData();
                $result = $app['csv_sql.repository.csv_sql']->query($sql);

                if ($result != 'true') {
                    $app->addError('SQLを保存できませんでした。SQL文を正しく入力してください。', 'admin');
                } else {
                    $status = $app['csv_sql.repository.csv_sql']->save($TargetCsvSql);

                    if ($status) {
                        $app->addSuccess('SQLを保存しました。', 'admin');

                        return $app->redirect($app->url('admin_shop_csv_sql'));
                    } else {
                        $app->addError('SQLを保存できませんでした。', 'admin');
                    }
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

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetCsvSql = $app['csv_sql.repository.csv_sql']->find($id);

        if (!$TargetCsvSql) {
            $app->deleteMessage();

            return $app->redirect($app->url('admin_shop_csv_sql'));
        }

        $status = $app['csv_sql.repository.csv_sql']->delete($TargetCsvSql);

        if ($status === true) {
            $app->addSuccess('SQLを削除しました。', 'admin');
        } else {
            $app->addError('SQLを削除できませんでした。', 'admin');
        }

        return $app->redirect($app->url('admin_shop_csv_sql'));
    }

    /**
     * CSV出力.
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

        $status = true;
        if ($status == true) {

            // ヘッダー行の抽出
            foreach ($csv_data as $csv_row) {
                foreach ($csv_row as $key => $value) {
                    $csv_header[] = $key;
                }
            }
            $csv_header = array_values(array_unique($csv_header));

            $filename = 'csv_'.date('ymd_His').'.csv';

            $response->setCallback(function () use ($app, $request, $csv_header, $csv_data) {

                $fp = fopen('php://output', 'w');
                // ヘッダー行の出力
                fputcsv($fp, $csv_header, $app['config']['csv_export_separator']);

                // データを出力
                foreach ($csv_data  as $csv_row) {
                    $row = array();
                    foreach ($csv_header as $header_name) {
                        mb_convert_variables('SJIS-win', 'UTF-8', $csv_row[$header_name]);
                        $row[] = $csv_row[$header_name];
                    }
                    fputcsv($fp, $row, $app['config']['csv_export_separator']);
                }

                fclose($fp);

            });

            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
            $response->send();

            return $response;
        }

        if ($status === false) {
            $app->addError('CSVを出力できませんでした。', 'admin');

            return $app->redirect($app->url('admin_shop_csv_sql'));
        }
    }

    /**
     * SQL確認.
     */
    public function sqlConfirm(Application $app, Request $request, $id = null)
    {
        if ($id) {
            $TargetCsvSql = $app['csv_sql.repository.csv_sql']->find($id);
            if (!$TargetCsvSql) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetCsvSql = new \Plugin\CsvSql\Entity\CsvSql();
        }

        //
        $builder = $app['form.factory']
        ->createBuilder('admin_csv_sql', $TargetCsvSql);

        $form = $builder->getForm();

        //
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if (!is_null($form['csv_sql']->getData())) {
                    $sql = 'SELECT '.$form['csv_sql']->getData();
                    $result = $app['csv_sql.repository.csv_sql']->query($sql);
                    if ($result == 'true') {
                        $message = 'エラーはありません。';
                    } else {
                        $message = $result;
                    }
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
