    <?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
    use Bitrix\Sale,
		Bitrix\Sale\Basket;
    use Bitrix\Main\Engine\Controller;
	
	Bitrix\Main\Loader::includeModule("sale");
	Bitrix\Main\Loader::includeModule("catalog");
     
    class CustomAjaxController extends Controller
    {
    	/**
    	 * @return array
    	 */
    	public function configureActions()
    	{
    		return [
    			'test' => [
    				'prefilters' => []
    			]
    		];
    	}
     
    	/**
    	 * @param string $param2
    	 * @param string $param1
    	 * @return array
    	 */
    	public static function testAction($phone, $count, $id_element)
    	{
    	global $USER;
		$rsPrices = CPrice::GetList(array(), array('PRODUCT_ID' => $id_element));
		$arPrice = $rsPrices->Fetch();
		
		$obElement = CIBlockElement::GetByID($id_element);
		$arEl = $obElement->Fetch();
	
		$arResult["ITEM"]["PRODUCT_ID"] = $id_element;
		$arResult["ITEM"]["NAME"] = $arEl["NAME"];
		$arResult["ITEM"]["PRICE"] =  $arPrice["PRICE"];
		$arResult["ITEM"]["CURRENCY"] = $arPrice["CURRENCY"];
		$arResult["ITEM"]["QUANTITY"] = $count;
		
		$basket = Bitrix\Sale\Basket::create(SITE_ID);

		foreach ($arResult as $product)
			{
				$item = $basket->createItem("catalog", $product["PRODUCT_ID"]);
				unset($product["PRODUCT_ID"]);
				$item->setFields($product);
				$item->setFields(
                [
      
                    'CUSTOM_PRICE' => 'Y',
                ]
            );
			$item->save();
		
			}
			
		$order = Bitrix\Sale\Order::create(SITE_ID, 1);
		$order->setPersonTypeId($USER->GetID());
		$order->setBasket($basket);
		

		
		$basketSum = $order->getPrice();
		$shipmentCollection = $order->getShipmentCollection();
		$shipment = $shipmentCollection->createItem(
				Bitrix\Sale\Delivery\Services\Manager::getObjectById(1)
			);

		$shipmentItemCollection = $shipment->getShipmentItemCollection();

		/** @var Sale\BasketItem $basketItem */

		foreach ($basket as $basketItem)
			{
				$item = $shipmentItemCollection->createItem($basketItem);
				$item->setQuantity($basketItem->getQuantity());
			}

		$paymentCollection = $order->getPaymentCollection();
		$payment = $paymentCollection->createItem(
				Bitrix\Sale\PaySystem\Manager::getObjectById(1)
			);
		$payment->setField("SUM", $order->getPrice());
		$payment->setField("CURRENCY", $order->getCurrency());
		
		$propertyCollection = $order->getPropertyCollection();
		// телефон
		$phoneProp = $propertyCollection->getPhone();
		$phoneProp->setValue($phone);
			
		$result = $order->save();
			if (!$result->isSuccess())
				{
					$result->getErrors();
				}
		
		return $result;
    	}
		
		
		
		public static function testBasketAction($phone)
    	{
			$basket = Sale\Basket::loadItemsForFUser(        
			Sale\Fuser::getId(),
			"s1"
			);
			$order = Bitrix\Sale\Order::create(SITE_ID, 1);
			$order->setPersonTypeId(1);
			$order->setBasket($basket);
			$shipmentCollection = $order->getShipmentCollection();
			$shipment = $shipmentCollection->createItem(
			Bitrix\Sale\Delivery\Services\Manager::getObjectById(1)
			);
			$shipmentItemCollection = $shipment->getShipmentItemCollection();

			foreach ($basket as $basketItem)
				{
					$item = $shipmentItemCollection->createItem($basketItem);
					$item->setQuantity($basketItem->getQuantity());
				}
			$paymentCollection = $order->getPaymentCollection();
			$payment = $paymentCollection->createItem(
					Bitrix\Sale\PaySystem\Manager::getObjectById(1)
				);
			$payment->setField("SUM", $order->getPrice());
			$payment->setField("CURRENCY", $order->getCurrency());
			
			$propertyCollection = $order->getPropertyCollection();
			// телефон
			$phoneProp = $propertyCollection->getPhone();
			$phoneProp->setValue($phone);
			
			$result = $order->save();
			if (!$result->isSuccess())
				{
					$result->getErrors();
				}

			return $arResult;
        }
    }