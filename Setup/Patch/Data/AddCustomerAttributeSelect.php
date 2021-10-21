<?php

namespace Bwilliamson\PatchExamples\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Zend_Validate_Exception;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddCustomerAttributeSelect implements DataPatchInterface
{

    private const ATTRIBUTE_CODE = 'customer_select';
    private const ENTITY_TYPE_ID = Customer::ENTITY;
    private const ATTRIBUTE_DATA = [
        'type' => 'text',
        'label' => 'Who goes there?',
        'input' => 'select',
        'required' => false,
        'sort_order' => 123,
        'position' => 123,
        'visible' => true,
        'system' => false,
        'user_defined' => true,
        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
        'option' => [ 'values' =>
            [
            'It is I! Sir bla bla!',
            'The knights of bla bla!',
            'Value three'
            ]
        ],
    ];

    private Config $eavConfig;
    private AttributeSetFactory $attributeSetFactory;
    private AttributeResource $attributeResource;
    private CustomerSetup $customerSetup;
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @param Config $eavConfig
     * @param AttributeSetFactory $attributeSetFactory
     * @param AttributeResource $attributeResource
     * @param CustomerSetupFactory $customerSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        Config               $eavConfig,
        AttributeSetFactory  $attributeSetFactory,
        AttributeResource    $attributeResource,
        CustomerSetupFactory $customerSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->eavConfig = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeResource = $attributeResource;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetup = $customerSetupFactory->create(['setup' => $moduleDataSetup]);
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $customerEntity = $this->eavConfig->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        //Sometimes we need to remove the attribute and recreate it. Easy peasy.
        //$this->customerSetup->removeAttribute(self::ENTITY_TYPE_ID, self::ATTRIBUTE_CODE);

        if (!$this->customerSetup->getAttribute(self::ENTITY_TYPE_ID, self::ATTRIBUTE_CODE)) {
            $this->customerSetup->addAttribute(
                self::ENTITY_TYPE_ID,
                self::ATTRIBUTE_CODE,
                self::ATTRIBUTE_DATA
            );
        } else {
            $this->customerSetup->updateAttribute(
                self::ENTITY_TYPE_ID,
                self::ATTRIBUTE_CODE,
                self::ATTRIBUTE_DATA
            );
        }

        //Some attribute properties don't/can't save on the first pass. Known bug.
        $attribute = $this->customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, self::ATTRIBUTE_CODE);
        if ($attribute) {
            $attribute->setData('used_in_forms', [
                'adminhtml_checkout',
                'adminhtml_customer',
                'customer_account_edit',
                'customer_account_create'
            ]);
            $attribute->setData('attribute_set_id', $attributeSetId);
            $attribute->setData('attribute_group_id', $attributeGroupId);
            $this->attributeResource->save($attribute);
        }
        $this->moduleDataSetup->getConnection()->endSetup();
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
        return [
            // \Bwilliamson\PatchExamples\Setup\Patch\Data\SomeClass::class
        ];
    }
}
