<?php
/*
 * This file is part of the Related Product plugin
 *
 * Copyright (C) 2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomCsvExport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Plugin\CustomCsvExport\Validator as Asserts;

class CustomCsvExportType extends AbstractType
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
            ->add('custom_sql', 'textarea', array(
                'label' => 'SQL文(最初のSELECTは記述しないでください。最後の;(セミコロン)も不要です。)',
                'constraints' => array(
                    new Asserts\SqlCheck(),
                ),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_custom_csv_export';
    }
}
