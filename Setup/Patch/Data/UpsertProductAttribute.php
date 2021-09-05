<?php

namespace Bwilliamson\PatchExamples\Setup\Patch\Data;

use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpsertProductAttribute implements DataPatchInterface
{
//    Set your desired content in the class constants.
//    Product attribute docs here https://devdocs.magento.com/guides/v2.4/extension-dev-guide/attributes.html
    private const ATTRIBUTE_CODE = 'my_attribute_code';
    private const ENTITY_TYPE_ID = \Magento\Catalog\Model\Product::ENTITY;
    private const ATTRIBUTE_DATA = [
        'apply_to' => [Type::TYPE_BUNDLE, Type::TYPE_SIMPLE, Type::TYPE_VIRTUAL, Configurable::TYPE_CODE],
        'attribute_model' => '',
        'attribute_set' => '',
        'backend' => '',
        'comparable' => false,
        'default' => false,
        'filterable' => false,
        'filterable_in_search' => false,
        'frontend_class' => '',
        'frontend' => '',
        'global' => true,
        'group' => '',
        'input_renderer' => '',
        'input' => 'select',
//        for options use the following method.
//            the 0s are the scope of the value
//        'option' => ['value' =>
//            [
//                'Label1' => [0 => 'Value1'],
//                'Label2' => [0 => 'Value2'],
//                'Label3' => [0 => 'Value3'],
//            ]
//        ],
        'is_filterable_in_grid' => false,
        'is_used_in_grid' => false,
        'is_visible_in_grid' => false,
        'label' => 'My attribute label',
        'note' => 'This is an example product attribute data array',
        'option' => '',
        'position' => '0',
        'required' => false,
        'searchable' => false,
        'sort_order' => '0',
        'source' => '',
        'system' => true,
        //'table' => '',
        'type' => 'text',
        'unique' => false,
        'used_for_promo_rules' => false,
        'used_for_sort_by' => false,
        'used_in_product_listing' => false,
        'user_defined' => false, // set to true for users to have the ability to change values
        'visible_in_advanced_search' => false,
        'visible_on_front' => false,
        'visible' => false,
        'wysiwyg_enabled' => true
    ];

    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory          $eavSetupFactory, $_attributeData
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        if(!$eavSetup->getAttribute(self::ENTITY_TYPE_ID, self::ATTRIBUTE_CODE)) {
            $eavSetup->addAttribute(
                self::ENTITY_TYPE_ID,
                self::ATTRIBUTE_CODE,
                self::ATTRIBUTE_DATA
            );
        } else {
            $eavSetup->updateAttribute(
                self::ENTITY_TYPE_ID,
                self::ATTRIBUTE_CODE,
                self::ATTRIBUTE_DATA
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
