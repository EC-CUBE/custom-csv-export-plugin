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

namespace Plugin\CustomCsvExport\Form\Type;

use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Plugin\CustomCsvExport\Validator as Asserts;

class CustomCsvExportType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * ProductReviewType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sql_name', TextType::class, [
                'label' => 'custom_csv_export.admin.label.name',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('custom_sql', TextareaType::class, [
                'label' => 'custom_csv_export.admin.label.custom_sql',
                'constraints' => [
                    new Asserts\SqlCheck(),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'custom_csv_admin_export';
    }
}
