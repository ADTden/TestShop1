$(".prew_but").click(function(){
	$(".one-click-button").show();
	$(this).hide();
	console.log($(".product-item-amount-field").val())
})

$(".btn-one-click").click(function(){
$.ajax({
	  type: "POST",
	  url: "",
	  data: { PHONE: $(".phone").val(), COUNT: $(".product-item-amount-field").val() }
	}).done(function(){
		$(".status").html("Заказ оформлен!");
		$(".one-click-button").hide();
		$(".prew_but").hide();
	})
})