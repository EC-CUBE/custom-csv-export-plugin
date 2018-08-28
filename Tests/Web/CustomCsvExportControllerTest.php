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

namespace Plugin\CustomCsvExport\Tests\Web;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Plugin\CustomCsvExport\Entity\CustomCsvExport;
use Plugin\CustomCsvExport\Repository\CustomCsvExportRepository;

/**
 * Class CustomCsvExportControllerTest.
 */
class CustomCsvExportControllerTest extends AbstractAdminWebTestCase
{
    /** @var CustomCsvExportRepository */
    protected $customCsvExport;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->customCsvExport = $this->container->get(CustomCsvExportRepository::class);
    }

    /**
     * カテゴリ画面のルーティング.
     */
    public function testRoute()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('custom_csv_export_admin'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertContains('SQL一覧', $crawler->html());
    }

    public function testCustomSqlConfirm()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('custom_csv_admin_export_confirm'),
            [
                'custom_csv_export' => [
                    '_token' => 'dummy',
                    'sql_name' => 'SQL-scrip-001',
                    'custom_sql' => '* FROM dtb_plugin',
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = 'エラーはありません。';
        $this->actual = $crawler->filter('#list_box pre')->text();
        $this->verify();
    }

    public function testCustomSqlCreate()
    {
        $this->client->request(
            'POST',
            $this->generateUrl('custom_csv_export_admin'),
            [
                'custom_csv_export' => [
                    '_token' => 'dummy',
                    'sql_name' => 'SQL-scrip-001',
                    'custom_sql' => '* FROM dtb_plugin',
                ],
            ]
        );

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = 'SQLを保存しました。';
        $this->actual = $crawler->filter('.c-contentsArea .alert-success span')->text();
        $this->verify();
    }

    public function testCustomSqlUpdate()
    {
        $customCsv = $this->createCustomSql();
        $newName = 'SQL-scrip-002';
        $newScript = '* FROM dtb_base_info';
        $this->client->request(
            'POST',
            $this->generateUrl('custom_csv_export_admin_edit', ['id' => $customCsv->getId()]),
            [
                'custom_csv_export' => [
                    '_token' => 'dummy',
                    'sql_name' => $newName,
                    'custom_sql' => $newScript,
                ],
            ]
        );

        /** @var CustomCsvExport $customCsvExport */
        $customCsvExport = $this->customCsvExport->findOneBy(['id' => $customCsv->getId()]);
        $this->expected = $newName;
        $this->actual = $customCsvExport->getSqlName();
        $this->verify();
        $this->expected = $newScript;
        $this->actual = $customCsvExport->getCustomSql();
        $this->verify();
    }

    public function testCustomSqlDelete()
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->createCustomSql();
        }

        $customCsvExport = $this->customCsvExport->findAll();
        $count = count($customCsvExport);
        $this->client->request(
            'DELETE',
            $this->generateUrl('custom_csv_export_admin_delete', ['id' => $customCsvExport[0]->getId()])
        );

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = ' SQLを削除しました';
        $this->actual = $crawler->filter('.c-contentsArea .alert-success span')->text();

        $countResult = $this->customCsvExport->count([]);
        $this->expected = $count - 1;
        $this->actual = $countResult;
        $this->verify();
    }

    public function testExport()
    {
        $customCsv = $this->createCustomSql();
        $this->client->request(
            'GET',
            $this->generateUrl('custom_csv_export_admin_output', ['id' => $customCsv->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function createCustomSql()
    {
        $customCsv = new CustomCsvExport();
        $customCsv->setSqlName('SQL-scrip-001');
        $customCsv->setCustomSql('* FROM dtb_plugin');
        $this->entityManager->persist($customCsv);
        $this->entityManager->flush($customCsv);

        return $customCsv;
    }
}
