<?php

namespace Bwilliamson\PatchExamples\Setup\Patch\Data;

use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CreateCoursesCategory - Create a category that's applied to the root store (All stores)
 */
class AddCategory implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    const CATEGORY_NAME = 'CategoryName';

    private ModuleDataSetupInterface $moduleDataSetup;
    private CategoryCollectionFactory $categoryCollectionFactory;
    private CategoryInterfaceFactory $categoryInterfaceFactory;
    private CategoryRepositoryInterface $categoryRepositoryInterface;
    private StoreManagerInterface $storeManagerInterface;

    /**
     * SetupWceiStore constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CategoryInterfaceFactory $categoryInterfaceFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategoryCollectionFactory $categoryCollectionFactory,
        CategoryInterfaceFactory $categoryInterfaceFactory,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryInterfaceFactory = $categoryInterfaceFactory;
        $this->categoryRepositoryInterface = $categoryRepository;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return void
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $categoryCollection = $this->categoryCollectionFactory
            ->create()
            ->addAttributeToFilter('name', self::CATEGORY_NAME)
            ->setPageSize(1);
        if (!$categoryCollection->getSize()) {
            $rootCategoryId = $this->storeManagerInterface->getStore()->getRootCategoryId();
            $category = $this->categoryInterfaceFactory->create();
            $category->setName(self::CATEGORY_NAME);
            $category->setParentId($rootCategoryId);
            $category->setIsActive(1);
            $category->setData('stores', [0]);
            $category->setData('url_key', strtolower(self::CATEGORY_NAME));
            $this->categoryRepositoryInterface->save($category);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
