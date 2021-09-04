<?php

namespace Bwilliamson\PatchExamples\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Setup\Module\Dependency\Report\WriterInterface;

class UpsertConfigurationSettings implements DataPatchInterface
{

    const CONFIG_PATHS_AND_VALUES = [
        'web/secure/base_url' => 'https://magento-2.test/',
        'web/secure/enable_hsts' => '1',
        'web/secure/use_in_adminhtml' => '1',
        'web/secure/use_in_frontend' => '1',
        'web/seo/use_rewrites' => '1',
        'web/unsecure/base_url' => 'http://magento-2.test/'
    ];
    const CONFIG_SCOPE_TYPE = 'default'; //can be default, stores, websites
    const CONFIG_SCOPE_ID = '0'; //this is polymorphous - can be the ID of websites/stores/storeviews

    private \Magento\Framework\App\Config\Storage\WriterInterface $configWriter;
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        WriterInterface $configWriter
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        foreach (self::CONFIG_PATHS_AND_VALUES AS $path => $value) {
            $this->configWriter->save(
                $path,
                $value,
                self::CONFIG_SCOPE_TYPE,
                self::CONFIG_SCOPE_ID
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
