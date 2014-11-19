var ctrlDown = false;
var shiftDown = false;
$(window).keydown(function(evt){
	if (evt.which == '16') {
		shiftDown = true;
	}
	if (evt.which == '17'){
		
		ctrlDown = true;
	}
})
$(window).keyup(function(evt){
	if (evt.which == '16') {
		shiftDown = false;
		$(".tarefa").removeClass("selecionada");
	};
	if (evt.which == '17'){
		ctrlDown = false;
	}
})

function vaipara(obj){
	posicaoAtual =  $('html, body').offset().top;
	posicaoDestino = $(obj).offset().top;
	tempo = 1000;
	diferenca = 70;
	$('html, body').stop().animate({
	    scrollTop: $(obj).offset().top - diferenca
	}, tempo);
	event.preventDefault();
}
function ajaxReplaceWith(route,data,target,callback){
	wplightbox.show("loading");
	var ajaxReplace = new majax;
	ajaxReplace.post(route,data);
	ajaxReplace.onSuccess = function(msg,data){
		console.log(data);
		$(target).replaceWith(data['html']);
		if (typeof(callback) == "function") {
			callback();
		};
	}
	ajaxReplace.onComplete = function(){
		wplightbox.hide();
	}
}
function ajaxLoadInto(route,data,target){
	wplightbox.show("loading");
	var ajaxReplace = new majax;
	ajaxReplace.post(route,data);
	ajaxReplace.onSuccess = function(msg,data){
		$(target).html(data['html']);
	}
	ajaxReplace.onComplete = function(){
		wplightbox.hide('loading');
	}
}