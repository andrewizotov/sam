<?php

namespace Sam\SpecialMeasure\Observer;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBeforeObserver implements ObserverInterface
{
    private $quote = null;
    private $order = null;
    private $quoteItems = [];

    /**
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $this->quote = $observer->getQuote();
        $this->order = $observer->getOrder();

        /* @var  \Magento\Sales\Model\Order\Item $orderItem */
        foreach ($this->order->getItems() as $orderItem) {
            if ($this->isSimpleProduct($orderItem)) {
                if ($quoteItem = $this->getQuoteItemById($orderItem->getQuoteItemId())) {
                    if ($optQuote = $quoteItem->getOptionByCode('additional_options')) {
                        if ($additionalOptionsOrder = $orderItem->getProductOptionByCode('additional_options')) {
                            $additionalOptions = array_merge($optQuote, $additionalOptionsOrder);
                        } else {
                            $additionalOptions = $optQuote;
                        }
                        if (count($additionalOptions) > 0) {
                            $options = $orderItem->getProductOptions();
                            $options['additional_options'] = unserialize($additionalOptions->getValue());
                            $orderItem->setProductOptions($options);
                        }

                    }
                }
            }
        }
    }


    /**
     * @param $orderItem
     * @return bool
     */
    private function isSimpleProduct($orderItem)
    {
        return (!$orderItem->getParentItemId() && $orderItem->getProductType() == Type::TYPE_SIMPLE);
    }


    /**
     * @param $id
     * @return mixed|null
     */
    private function getQuoteItemById($id)
    {
        if (empty($this->quoteItems)) {
            foreach ($this->quote->getItems() as $item) {
                if ($this->isSimpleProduct($item)) {
                    $this->quoteItems[$item->getId()] = $item;
                }
            }
        }
        if (array_key_exists($id, $this->quoteItems)) {
            return $this->quoteItems[$id];
        }
        return null;
    }
}