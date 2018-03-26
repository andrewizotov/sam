<?php

namespace Sam\SpecialMeasure\Block\Product;


/**
 * Class View
 * @package Sam\SpecialMeasure\Block\Product
 */
class View extends \Magento\Catalog\Block\Product\View
{

    /**
     * @return array
     */
    public function getMeasureValidators()
    {
        $validators = [];
        $validators['validate-no-empty'] = true;
        return $validators;
    }
}