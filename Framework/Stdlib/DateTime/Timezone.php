<?php
namespace Core\IranianCities\Framework\Stdlib\DateTime;

use Magento\Framework\App\ScopeInterface;

class Timezone extends \Magento\Framework\Stdlib\DateTime\Timezone
{

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isScopeDateInInterval($scope, $dateFrom = null, $dateTo = null)
    {
        if (!$scope instanceof ScopeInterface) {
            $scope = $this->_scopeResolver->getScope($scope);
        }

        $scopeTimeStamp = strtotime($this->convertConfigTimeToUtc(date("Y-m-d H:i:s", $this->scopeTimeStamp($scope))));
        $fromTimeStamp = strtotime($dateFrom);
        $toTimeStamp = strtotime($dateTo);
        if ($dateTo) {
            // fix date YYYY-MM-DD 00:00:00 to YYYY-MM-DD 23:59:59
            $toTimeStamp += 86400;
        }

        return !(!$this->_dateTime->isEmptyDate($dateFrom) && $scopeTimeStamp < $fromTimeStamp ||
            !$this->_dateTime->isEmptyDate($dateTo) && $scopeTimeStamp > $toTimeStamp);
    }
}
