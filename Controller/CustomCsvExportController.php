<?php
/*
 * This file is part of the Custom Csv Export Plugin
 *
 * Copyright (C) 2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomCsvExport\Controller;

use Eccube\Controller\AbstractController;
use Plugin\CustomCsvExport\Entity\CustomCsvExport;
use Plugin\CustomCsvExport\Form\Type\CustomCsvExportType;
use Plugin\CustomCsvExport\Repository\CustomCsvExportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomCsvExportController extends AbstractController
{
    /** @var  CustomCsvExportRepository */
    protected $customCsvExportRepository;

    /**
     * CustomCsvExportController constructor.
     * @param CustomCsvExportRepository $customCsvExportRepository
     */
    public function __construct(CustomCsvExportRepository $customCsvExportRepository)
    {
        $this->customCsvExportRepository = $customCsvExportRepository;
    }


    /**
     * @Route("%eccube_admin_route%/setting/shop/custom_csv_export", name="plugin_custom_csv_export")
     * @Route("%eccube_admin_route%/setting/shop/custom_csv_export/{id}/edit", requirements={"id" = "\d+"}, name="plugin_custom_csv_export_edit")
     *
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, $id = null)
    {
        if ($id) {
            $TargetCustomCsvExport = $this->customCsvExportRepository->find($id);
            if (!$TargetCustomCsvExport) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetCustomCsvExport = new CustomCsvExport();
        }

        $builder = $this->formFactory->createBuilder(CustomCsvExportType::class, $TargetCustomCsvExport);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sql = 'SELECT '.$form['custom_sql']->getData();
            try {
                $result = $this->customCsvExportRepository->query($sql);

                if ($result) {
                    $status = $this->customCsvExportRepository->save($TargetCustomCsvExport);

                    if ($status) {
                        $this->addSuccess('SQLを保存しました。', 'admin');

                        return $this->redirect($this->generateUrl('plugin_custom_csv_export'));
                    } else {
                        $this->addError('SQLを保存できませんでした。', 'admin');
                    }
                } else {
                    $this->addError('SQLを保存できませんでした。', 'admin');
                }
            } catch (\Exception $e) {
                $this->addError('SQLを保存できませんでした。SQL文を正しく入力してください。', 'admin');
            }
        }

        $CustomCsvExports = $this->customCsvExportRepository->getList();

        return $this->render('CustomCsvExport/Resource/template/Admin/index.twig', array(
            'form' => $form->createView(),
            'CustomCsvExports' => $CustomCsvExports,
            'TargetCustomCsvExport' => $TargetCustomCsvExport,
        ));
    }

    /**
     * @Route("%eccube_admin_route%/setting/shop/custom_csv_export/{id}/delete", requirements={"id" = "\d+"}, name="plugin_custom_csv_export_delete")
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, $id)
    {
        $this->isTokenValid();

        $TargetCustomCsvExport = $this->customCsvExportRepository->find($id);

        if (!$TargetCustomCsvExport) {
            $this->deleteMessage();

            return $this->redirectToRoute('plugin_custom_csv_export');
        }

        $status = $this->customCsvExportRepository->delete($TargetCustomCsvExport);

        if ($status) {
            $this->addSuccess('SQLを削除しました。', 'admin');
        } else {
            $this->addError('SQLを削除できませんでした。', 'admin');
        }

        return $this->redirectToRoute('plugin_custom_csv_export');
    }

    /**
     * @Route("%eccube_admin_route%/setting/shop/custom_csv_export/{id}/output", requirements={"id" = "\d+"}, name="plugin_custom_csv_export_output")
     *
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|StreamedResponse
     */
    public function csvOutput(Request $request, $id = null)
    {
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);

        $TargetCustomCsvExport = $this->customCsvExportRepository->find($id);
        if (!$TargetCustomCsvExport) {
            throw new NotFoundHttpException();
        }

        $response = new StreamedResponse();

        $csv_data = $this->customCsvExportRepository->getArrayList($TargetCustomCsvExport->getCustomSql());

        if (count($csv_data) > 0) {

            // ヘッダー行の抽出
            $csv_header = array();
            foreach ($csv_data as $csv_row) {
  
                foreach ($csv_row as $key => $value) {
                    $csv_header[$key] = mb_convert_encoding($key, $this->eccubeConfig['eccube_csv_export_encoding'], 'UTF-8');
                }
                break;
            }

            $response->setCallback(function () use ($request, $csv_header, $csv_data) {

                $fp = fopen('php://output', 'w');
                // ヘッダー行の出力
                fputcsv($fp, $csv_header, $this->eccubeConfig['eccube_csv_export_separator']);

                // データを出力
                foreach ($csv_data as $csv_row) {
                    $row = array();
                    foreach ($csv_header as $headerKey => $header_name) {
                        mb_convert_variables($this->eccubeConfig['eccube_csv_export_encoding'], 'UTF-8', $csv_row[$headerKey]);
                        $row[] = $csv_row[$headerKey];
                    }
                    fputcsv($fp, $row, $this->eccubeConfig['eccube_csv_export_separator']);
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

        $this->addError('CSVを出力できませんでした。', 'admin');

        return $this->redirectToRoute('plugin_custom_csv_export');
    }

    /**
     * @Route("%eccube_admin_route%/setting/shop/custom_csv_export/confirm", name="plugin_custom_csv_export_confirm")
     *
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sqlConfirm(Request $request, $id = null)
    {
        if ($id) {
            $TargetCustomCsvExport = $this->customCsvExportRepository->find($id);
            if (!$TargetCustomCsvExport) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetCustomCsvExport = new CustomCsvExport();
        }

        $builder = $this->formFactory->createBuilder(CustomCsvExportType::class, $TargetCustomCsvExport);

        $form = $builder->getForm();

        $form->handleRequest($request);

        $message = 'test message';
        if ($form->isSubmitted() && $form->isValid()) {

            if (!is_null($form['custom_sql']->getData())) {
                $sql = 'SELECT '.$form['custom_sql']->getData();
                try {
                    $result = $this->customCsvExportRepository->query($sql);
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

        $CustomCsvExports = $this->customCsvExportRepository->getList();

        return $this->render('CustomCsvExport/Resource/template/Admin/index.twig', array(
            'form' => $form->createView(),
            'CustomCsvExports' => $CustomCsvExports,
            'message' => $message,
            'TargetCustomCsvExport' => $TargetCustomCsvExport,
        ));
    }
}
