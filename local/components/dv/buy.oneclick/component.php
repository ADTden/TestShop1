<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

	$page = $APPLICATION->GetCurPage();
	//echo $page;
	if($_POST["PHONE"]){
		if($page != "/personal/cart/"){
			$this->makeArResult($arParams["ID_ELEMENT"],$arParams["NAME_ELEMENT"],$arParams["PRICE_ELEMENT"],$arParams["CURRENCY"],$arParams["QUANTITY"],$_POST["PHONE"],$_POST["COUNT"]);
		}else{
			$this->makeArResult1($_POST["PHONE"]);
		}
	}
	$this->IncludeComponentTemplate();

?>