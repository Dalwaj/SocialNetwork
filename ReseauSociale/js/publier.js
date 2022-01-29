$(document).ready(function(){
	$(".publier").click(function(){
		$("div.publierWindow").css("visibility", "visible");
	});

	$(".cancelPublier").click(function(){
		$("div.publierWindow").css("visibility", "hidden");
	});
});