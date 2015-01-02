$(document).ready(function(){
	var ajaxReloadOnSave = new majax;
	ajaxReloadOnSave.on(200,function(msg,data){
		location.reload();
	})
	ajaxReloadOnSave.on(304,function(msg){
		console.log(msg);
		window.setTimeout(function(){
			ajaxReloadOnSave.post(magic_route,{"checkReload":true});
		},400)
	})
	ajaxReloadOnSave.onError = function(){
		console.log("error");
		window.setTimeout(function(){
			ajaxReloadOnSave.post(magic_route,{"checkReload":true});
		},400)
	}
	window.setTimeout(function(){
		ajaxReloadOnSave.post(magic_route,{"checkReload":true});
	},2000)
});