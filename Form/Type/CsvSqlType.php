<?php

namespace Plugin\CsvSql\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Plugin\CsvSql\Validator as Asserts;

class CsvSqlType extends AbstractType
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $app \Eccube\Application */
        $app = $this->app;

        $tabledata = array();

        $driverChain = $app['orm.em']->getConfiguration()->getMetadataDriverImpl();
        $drivers = $driverChain->getDrivers();

        foreach ($drivers as $namespace => $driver) {
            $classNames = $driver->getAllClassNames();
            foreach ($classNames as $className) {
                $meta = $app['orm.em']->getMetadataFactory()->getMetadataFor($className);
                $tabledata[$meta->getName()] = $meta->getTableName();
            }
        }

        $builder
            ->add('sql_name', 'text', array(
                'label' => '名称',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $this->app['config']['stext_len'],
                    )),
                ),
            ))
            ->add('csv_sql', 'textarea', array(
                    'label' => 'SQL文(最初のSELECTは記述しないでください。)',
                    'constraints' => array(
                            new Asserts\SqlCheck(),
                            ),
            ))
            ->add('tabledata', 'choice', array(
                    'choices' => $tabledata,
                    'label' => 'テーブル一覧',
                    'expanded' => false,
                    'multiple' => false,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_csv_sql';
    }
}
