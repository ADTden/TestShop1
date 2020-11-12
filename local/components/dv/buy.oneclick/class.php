<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Sale;
class CDemoSqr extends CBitrixComponent
{
    //Родительский метод проходит по всем параметрам переданным в $APPLICATION->IncludeComponent
    //и применяет к ним функцию htmlspecialcharsex. В данном случае такая обработка избыточна.
    //Переопределяем.
    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
            "ID_ELEMENT" => intval($arParams["ID_ELEMENT"]),
			"NAME_ELEMENT" => $arParams["NAME_ELEMENT"],
			"PRICE_ELEMENT" => intval($arParams["PRICE_ELEMENT"]),
			"CURRENCY" => $arParams["CURRENCY"],
			"QUANTITY" =>$arParams["QUANTITY"],
			
			
        );
        return $result;
    }


    public function makeArResult($id_element,$name_element,$price_element,$currency_element,$quantity_element,$phone,$count)
    {	global $USER;
		$arResult["ITEM"]["PRODUCT_ID"] = $id_element;
		$arResult["ITEM"]["NAME"] = $name_element;
		$arResult["ITEM"]["PRICE"] = $price_element;
		$arResult["ITEM"]["CURRENCY"] = $currency_element;
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
					//$result->getErrors();
				}
		
		return $arResult;
		
	}
	
	
	 public function makeArResult1($phone)
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
				//
			}

		return $arResult;
		
	}
}	?>