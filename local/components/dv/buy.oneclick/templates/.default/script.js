$(function(){
	$(".prew_but").click(function(){
		$(".one-click-button").show();
		$(this).hide();
	////	console.log($(".product-item-amount-field").val())
	})

	$(".btn-one-click").click(function(){
		var path;
		if($(".btn-one-click").attr("data-cur-path") == "/personal/cart/")
		{
			path = "testBasket";
		}else{
			path = "test";
		}
		console.log(path);
			var request = BX.ajax.runComponentAction('dv:buy.oneclick', path, {
			mode:'ajax',
			data: {
				phone: $(".phone").val(),
				count: $(".product-item-amount-field").val(),
				id_element: $(".btn-one-click").attr("data-id-element"),
			}
		});
		 
		request.then(function(){
			$(".status").html("Заказ оформлен!");
			$(".one-click-button").hide();
			$(".prew_but").hide();
		});
	})
});
