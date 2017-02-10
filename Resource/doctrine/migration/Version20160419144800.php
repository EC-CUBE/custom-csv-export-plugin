<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Application;
use Plugin\CsvSql\Entity;
/**
 * Class Version20160419144800
 * @package DoctrineMigrations
 */
class Version20160419144800 extends AbstractMigration
{
    protected $tables = array();
    protected $entities = array();
    protected $sequences = array();

    public function __construct()
    {
        $this->tables = array(
                'plg_csv_sql',
        );
        $this->entities = array(
                'Plugin\CsvSql\Entity\CsvSql',
        );
        $this->sequences = array(
                'plg_csv_sql_csv_id_seq',
        );
    }
    /**
     * インストール時処理
     * @param Schema $schema
     * @return bool
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function up(Schema $schema)
    {
        $app = Application::getInstance();
        $em = $app['orm.em'];
        $classes = array();
        foreach ($this->entities as $entity) {
            $classes[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }
        $tool = new SchemaTool($em);
        $tool->createSchema($classes);
    }
    /**
     * アンインストール時処理
     * @param Schema $schema
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function down(Schema $schema)
    {
        foreach ($this->tables as $table) {
            if ($schema->hasTable($table)) {
                $schema->dropTable($table);
            }
        }
        foreach ($this->sequences as $sequence) {
            if ($schema->hasSequence($sequence)) {
                $schema->dropSequence($sequence);
            }
        }
    }
}
