<?php


namespace Core\IranianCities\Framework\Locale;


use Core\IranianCities\Setup\Patch\Data\IranianCities;
use Magento\Framework\Locale\ConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
class TranslatedLists extends \Magento\Framework\Locale\TranslatedLists
{
    protected $collectionFactory;
    protected $addDataForIran;

    public function __construct(
        ConfigInterface $config,
        ResolverInterface $localeResolver,
        IranianCities $addDataForIran,
        $locale = null)
    {
        parent::__construct($config, $localeResolver, $locale);
        $this->addDataForIran=$addDataForIran;
    }


    public function getCountryTranslation($value, $locale = null)
    {
        $translate=parent::getCountryTranslation($value, $locale);
        if (empty($translate)){
           $countries=$this->addDataForIran->getProvidersObj();
           if ($locale==null){
               $locale='en_US';
           }
            return !isset($countries[$value])?$translate:$countries[$value][$locale]??$countries[$value]['RegionName'];
        }
        return $translate;
    }
}
