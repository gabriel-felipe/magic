$(document).ready(function(){
	var ajaxCheckPage = new majax;
	ajaxCheckPage.on(200,function(msg,data){
		$("sidebar#page-list").replaceWith(data['sidebar']);
		$("sidebar#page-list .page[data-page='"+$("#page-holder").attr("data-page")+"']").addClass("ativa");
		window.setTimeout(function(){
			ajaxCheckPage.post(magic_route,{"CheckPages":true});
		},1000)
	})
	ajaxCheckPage.on(304,function(msg){
		console.log(msg);
		window.setTimeout(function(){
			ajaxCheckPage.post(magic_route,{"CheckPages":true});
		},1000)
	})
	ajaxCheckPage.onError = function(){
		console.log("error");
		window.setTimeout(function(){
			ajaxCheckPage.post(magic_route,{"CheckPages":true});
		},1000)
	}
	window.setTimeout(function(){
		ajaxCheckPage.post(magic_route,{"CheckPages":true});
	},2000)
});