<?php

namespace Bwilliamson\PatchExamples\Setup\Patch\Data;

use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class AddCmsData - dynamically insert cms pages and blocks
 *
 * This uses store view codes, to add CMS data to our DBs programmatically
 * Currently only handles one store view code per data block array
 */
class AddCmsData implements DataPatchInterface
{

    /**
     * CMS Blocks Data Array
     * Parent key for each array is store view code
     */
    protected array $cmsBlockData = [
        'my_default_store_view_code' => [
            [
                'title' => 'CMS block title',
                'identifier' => 'example-block-1',
                'content' => 'This would be content directly from the cms_block tables content field',
                'is_active' => 1,
                'stores' => [], //will be populated in logic later
                'sort_order' => 0
            ],
            [
                'title' => 'CMS block title 2',
                'identifier' => 'example-block-2',
                'content' => 'This would be content directly from the cms_block tables content field',
                'is_active' => 1,
                'stores' => [],
                'sort_order' => 0
            ]
        ],
        'admin' => [
            [
                'title' => 'Example block title 3',
                'identifier' => 'example-block-for-admin-scope',
                'content' => 'This would be content directly from the cms_block tables content field',
                'is_active' => 1,
                'stores' => [],
                'sort_order' => 0
            ]
        ]
    ];

    /**
     * CMS Pages Data Array
     * Parent key for each array is store view code
     */
    protected array $cmsPageData = [
        'my_default_store_view_code' => [
            [
                'title' => 'Example cms page 1',
                'page_layout' => 'cms-full-width',
                'meta_keywords' => '',
                'meta_description' => 'lorem',
                'identifier' => 'example-page-1-slug',
                'content_heading' => '',
                'content' => 'This would be content directly from the cms_page tables content field',
                'layout_update_xml' => '',
                'url_key' => 'example-page-1',
                'is_active' => 1,
                'stores' => [],
                'sort_order' => 0,
                'meta_title' => 'lorem'
            ],
            [
                'title' => 'Example cms page 2',
                'page_layout' => 'cms-full-width',
                'meta_keywords' => '',
                'meta_description' => 'lorem',
                'identifier' => 'example-page-2-slug',
                'content_heading' => '',
                'content' => 'This would be content directly from the cms_page tables content field',
                'layout_update_xml' => '',
                'url_key' => 'example-page-2',
                'is_active' => 1,
                'stores' => [],
                'sort_order' => 0,
                'meta_title' => 'lorem'
            ]
        ],
    ];

    private ModuleDataSetupInterface $moduleDataSetup;
    private PageFactory $pageFactory;
    private BlockFactory $blockFactory;
    private StoreRepositoryInterface $storeRepository;

    /**
     * AddCmsData constructor.
     * @param PageFactory $pageFactory
     * @param BlockFactory $blockFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        PageFactory $pageFactory,
        BlockFactory $blockFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $blockFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        //pages
        foreach ($this->cmsPageData as $storeViewCode => $pageDataArray) {
            $storeViewId = $this->getStoreViewId($storeViewCode);
            foreach ($pageDataArray as $pageData) {
                $pageData['stores'] = ($storeViewId) ? [$storeViewId] : ['0'];
                $this->pageFactory->create()->setData($pageData)->save();
            }
        }
        foreach ($this->cmsBlockData as $storeViewCode => $blockDataArray) {
            $storeViewId = $this->getStoreViewId($storeViewCode);
            foreach ($blockDataArray as $blockData) {
                $blockData['stores'] = ($storeViewId) ? [$storeViewId] : ['0'];
                $this->blockFactory->create()->setData($blockData)->save();
            }
        }
        $this->moduleDataSetup->endSetup();
    }

    /**
     * @param string $code
     * @return int|null
     * @throws NoSuchEntityException
     */
    public function getStoreViewId(string $code): ?int
    {
        $storeView = $this->storeRepository->get($code);
        return $storeView->getId();
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}

