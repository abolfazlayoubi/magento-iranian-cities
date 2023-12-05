<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Core\IranianCities\Setup\Patch\Data;

use Magento\Directory\Setup\DataInstaller;
use Magento\Directory\Setup\Patch\Data\InitializeDirectoryData;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Add China States
 */
class IranianCities implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Directory\Setup\DataInstallerFactory
     */
    private $dataInstallerFactory;

    private $catch;

    const FILE_DIR=BP . "/app/code/Core/IranianCities/Setup/Patch/Data/BaseData";
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Directory\Setup\DataInstallerFactory $dataInstallerFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Directory\Setup\DataInstallerFactory $dataInstallerFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->dataInstallerFactory = $dataInstallerFactory;
        $this->catch=[];
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $columns = ['country_id', 'iso2_code', 'iso3_code'];
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('directory_country'),
            $columns,
            $this->getProviders()
        );

        /** @var DataInstaller $dataInstaller */
        $dataInstaller = $this->dataInstallerFactory->create();
        $dataInstaller->addCountryRegions(
            $this->moduleDataSetup->getConnection(),
            $this->getDataForIran()
        );
    }

    public function getProviders()
    {
        $fileCounties=$this->getProvidersArray();
        $counties=[];
        foreach ($fileCounties as $county) {
            if (strlen((string)$county['mageCode'])==1) {
                $county['mageCode']="0" . $county['mageCode'];
            }
            $counties[]=[
                (string)$county['mageCode'], (string)$county['mageCode'], $county['RegionCode']
            ];
            $this->catch[$county['ID']]=[
                'id'=> $county['mageCode'],
                'key'=>$county['RegionCode']
            ];
        }
        return $counties;
    }
    /**
     * China states data.
     *
     * @return array
     */
    private function getDataForIran()
    {
        $fileCities=file_get_contents(self::FILE_DIR . "/City.json");
        $cities=[];
        $counter=[];
        foreach (json_decode($fileCities, true) as $city) {
            if (isset($this->catch[$city['RegionID']]['key'])) {
                //  echo $this->catch[$city['RegionID']]['key']."-".++$counter;
                if (!isset($counter[$this->catch[$city['RegionID']]['key']])) {
                    $counter[$this->catch[$city['RegionID']]['key']]=1;
                }
                $cities[] = [
                    (string)$this->catch[$city['RegionID']]['id'], (string)$this->catch[$city['RegionID']]['key'] . "-" . $counter[$this->catch[$city['RegionID']]['key']]++, $city['CityName']
                ];
            }
        }
        return $cities;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            InitializeDirectoryData::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    public function getProvidersArray()
    {
        return json_decode(file_get_contents(self::FILE_DIR . "/Country.json"), true);
    }

    public function getProvidersObj()
    {
        return json_decode(file_get_contents(self::FILE_DIR . "/CountryObj.json"), true);
    }
}
