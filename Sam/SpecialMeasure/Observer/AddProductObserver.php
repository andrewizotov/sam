<?php

namespace Sam\SpecialMeasure\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class AddProductObserver implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;


    /**
     * AddProductObserver constructor.
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->_request = $request;
    }


    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $item = $observer->getQuoteItem();
        $options = array();
        if ($option = $item->getOptionByCode('additional_options')) {
            $options = (array)unserialize($option->getValue());
        }
        $post = $this->_request->getParam('measures');
        if (is_array($post)) {
            foreach ($post as $key => $value) {
                if ($key == '' || $value == '') {
                    continue;
                }
                $options[] = [
                    'label' => $key,
                    'value' => $value
                ];
            }
        }
        if (count($options) > 0) {
            $item->addOption(array(
                'code' => 'additional_options',
                'value' => serialize($options)
            ));
        }

    }
}
