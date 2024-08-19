<?php

namespace Module\IndexBoxes\Form;

use Module\IndexBoxes\Entity\IndexBox;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class IndexBoxType extends TranslatorAwareType
{
    private $icons = [
        'search' => '&#xe8b6;',
        'home' => '&#xe88a;',
        'menu' => '&#xe5d2;',
        'close' => '&#xe5cd;',
        'settings' => '&#xe8b8;',
        'check_circle' => '&#xe86c;',
        'favorite' => '&#xe87d;',
        'add' => '&#xe145;',
        'star' => '&#xe838;',
        'chevron_right' => '&#xe5cc;',
        'add_circle' => '&#xe147;',
        'cancel' => '&#xe5c9;',
        'arrow_forward' => '&#xe5c8;',
        'check' => '&#xe5ca;',
        'check_box' => '&#xe834;',
        'grade' => '&#xe885;',
        'refresh' => '&#xe5d5;',
    ];
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = \Context::getContext();

        $products = \Product::getSimpleProducts($context->language->id);
        $products_choices = [];
        foreach($products as $prod) {
            $products_choices[$prod['name'] . ' (ID: ' . $prod['id_product'] . ')'] = $prod['id_product'];
        }

        $cms = \CMS::getCMSPages($context->language->id, null, null, $context->shop->id);
        $cms_choices = [];
        foreach($cms as $c) {
            $cms_choices[$c['meta_title'] . ' (ID: ' . $c['id_cms'] . ')'] = $c['id_cms'];
        }

        $icons = array_flip(array_map('html_entity_decode',$this->icons));

        $builder
            ->add('bo_title', TextType::class, [
                'label' => $this->trans('BO Title', 'Modules.Indexboxes.Form'),
                'help' => $this->trans('Title that is only seen in the backoffice', 'Modules.Indexboxes.Form'),
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 255]
                        ),
                    ]),
                    new NotBlank(),
                ]
            ])
            ->add('title', TranslatableType::class, [
                'label' => $this->trans('Title', 'Admin.Global'),
                'help' => $this->trans('Title that will be seen in frontoffice', 'Modules.Indexboxes.Form'),
                'constraints' => [
                    new DefaultLanguage([
                        'message' => $this->trans(
                            'The field %field_name% is required at least in your default language.',
                            'Admin.Notifications.Error',
                            [
                                '%field_name%' => sprintf(
                                    '"%s"',
                                    $this->trans('Title', 'Modules.Indexboxes.Form')
                                ),
                            ]
                        ),
                    ]),
                ],
            ])
            ->add('icon', ChoiceType::class, [
                'label' => $this->trans('Icon', 'Modules.Indexboxes.Form'),
                'help' => $this->trans('Icon displayed after the title', 'Modules.Indexboxes.Form'),
                'attr' => ['class' => 'material-icons'],
                'choices' => $icons,
                'required' => false,
            ])
            ->add('image', ImagePreviewType::class, [
                'label' => false,
                'attr' => [
                    'type' => 'image'
                ],
            ])
            ->add('new_image', FileType::class, [
                'label' => $this->trans('Image', 'Admin.Global'),
                'help' => $this->trans('Image for box backgound', 'Modules.Indexboxes.Form'),
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => $this->trans('Please upload a valid image', 'Modules.Indexboxes.Form'),
                    ])
                ],
                'data_class' => null,
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'label' => $this->trans('Type', 'Modules.Indexboxes.Form'),
                'help' => $this->trans('Type of item to be linked', 'Modules.Indexboxes.Form'),
                'choices' => IndexBox::$TYPES,
            ])
            ->add('item_id', HiddenType::class)
            ->add('category_id', CategoryChoiceTreeType::class, [
                'label' => $this->trans('Category', 'Admin.Global'),
                'help' => $this->trans('Select category to be linked', 'Modules.Indexboxes.Form'),
                'required' => false,
                ])
            ->add('product_id', ChoiceType::class, [
                'label' => $this->trans('Product', 'Admin.Global'),
                'help' => $this->trans('Select product to be linked', 'Modules.Indexboxes.Form'),
                'choices' => $products_choices,
                'required' => false,
            ])
            ->add('cms_id', ChoiceType::class, [
                'label' => $this->trans('CMS', 'Admin.Global'),
                'help' => $this->trans('Select CMS to be linked', 'Modules.Indexboxes.Form'),
                'choices' => $cms_choices,
                'required' => false,
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', 'Admin.Global'),
                'required' => false,
            ])
        ;
    }
}
