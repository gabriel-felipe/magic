function basename(path) {
    return path.replace(/\\/g,'/').replace( /.*\//, '' );
	}
$('input[type=file].custom').change(function(){
	if($(this).val()){
		$(this).parent().parent().find(".name").html(basename($(this).val()));
		$(this).parent().parent().find(".name").show();
	}
});
//--=-=-=-=-SELECT SINGLE //
function selectBox(obj){
//Construindo.
var select           = this;
select.all           = $(obj);
select.label         = $(obj).children("label");
select.seta          =  $(obj).children("#seta");
select.opcoes        = $(obj).children(".opcoes");
select.nomeCampo     = this.seta.attr("title");
select.input         = $(obj).children("input");
select.duracaoEfeito = 150;
select.efeitoIn      = "fade";
select.efeitoOut     = "fade";
select.buttonAtivo   = 0;

//Tratando div como input
select.all.attr("tabindex",'0');

//Definindo FunÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes

select.all.keydown(function(e){

        if(e.which ==40){
          e.preventDefault();
          select.mostraOpcoes();
          select.buttonAtivo = (select.buttonAtivo >= $(this).find('button').size() - 1)  ? 0 : select.buttonAtivo + 1;
          $(this).find('button').removeClass("on");
          $(this).find('button').eq(select.buttonAtivo).addClass("on");
        } else if(e.which == 38){
          e.preventDefault();
          select.mostraOpcoes();
          select.buttonAtivo = (select.buttonAtivo  <= 0)  ? $(this).find('button').size() - 1: select.buttonAtivo - 1;
          $(this).find('button').removeClass("on");
          $(this).find('button').eq(select.buttonAtivo).addClass("on");
        } else if(e.which == 13 || e.which == 32){
          select.mostraOpcoes();
          $(this).find('button').eq(select.buttonAtivo).click();
        } 
      });
select.all.focus(function(){
  // scroll = ($('body,html').scrollTop() > select.all.offset().top + select.all.height() + select.opcoes.height() - $('body').height() + 50 ) ? $('body,html').scrollTop() : select.all.offset().top + select.all.height() + select.opcoes.height() - $('body').height() + 50;
  // $('body,html').scrollTop(scroll);
});
select.all.blur(function(){
  select.buttonAtivo = 0;
  $(this).find('button').removeClass("on");
});

select.all.find('button').mouseenter(function(e){
  e.preventDefault();
  $(this).parent().find("button").removeClass("on");
  $(this).addClass("on");
  select.buttonAtivo = $(this).index();
  select.all.focus();
});

select.apagaOpcoes = function(){
 switch(select.efeitoOut){
 case "fade": select.opcoes.fadeOut(select.duracaoEfeito); break;
 case "slide": select.opcoes.slideUp(select.duracaoEfeito); break;
 case "none": select.opcoes.hide(select.duracaoEfeito); break;
 }
}

select.mostraOpcoes = function(){
 switch(select.efeitoIn){
 case "fade": select.opcoes.fadeIn(select.duracaoEfeito); break;
 case "slide": select.opcoes.slideDown(select.duracaoEfeito); break;
 case "none": select.opcoes.show(select.duracaoEfeito); break;
 }
}

select.confEfeito = function(obj) {
jQuery.extend(select, obj);
}

//PrÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© funÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes
select.opcoes.fadeOut(0);

//Configurando CSS's

select.opcoes.css(
 {
 "position": "absolute",
 "z-index" : "100"
 });
//Controladores de evento
 select.seta.click(function(e){
 e.preventDefault();
 select.mostraOpcoes();
 });
 select.all.click(function(e){
 e.preventDefault();
 select.mostraOpcoes();
 });
 select.all.focus(function(e){
  e.preventDefault();
  select.mostraOpcoes();
 });
 select.all.blur(function(e){
  e.preventDefault();
  select.apagaOpcoes();
 });
 select.opcoes.children("button").click(function(){
   valor=$(this).attr("value");
   html = $(this).html();
   select.label.html(html);
   select.input.val(valor);
   select.onchange(valor);
   select.apagaOpcoes();
   return false;
  });
 select.onchange = function(valor){

 }

 select.all.mouseleave(function(e){
  e.preventDefault();
  select.apagaOpcoes();
 });
}

function caixaformulario(){
  $('div.select').each(function(){
    var selecta = new selectBox(this);
  });
}

caixaformulario();

(function( $ ) {
  $.fn.numberBox = function() {
    var input = this.find("input");
    var numberHolder = this.find("p.number");
    var numberAtual = 0;
    var get_number = function(){
      number = parseInt(numberHolder.html());
      return number;
    }
    var soma = function(){
      numberAtual = get_number() + 1;
      set_number();
    }
    var menos = function(){
      if(get_number() > 1){
      numberAtual = get_number() - 1;
      } else{
        alert("VocÃƒÆ’Ã‚Âª nÃƒÆ’Ã‚Â£o pode adicionar menos que uma peÃƒÆ’Ã‚Â§a");
      }

      set_number();
    }
    var set_number = function(){
      numberHolder.html(numberAtual);
      if(input){
        input.val(numberAtual);
        input.change();

      }

    }

    var maisBt = this.find(".mais");
    var menosBt = this.find(".menos");
    var numberAtual = get_number();
    
    
    maisBt.click(function(){
      soma();
    });
    menosBt.click(function(){
      menos();
    });
  }
})( jQuery );


(function( $ ) {
  $.fn.sanfona = function() {
    var items = this.find("li");
    items.find(".content").hide();

    items.click(function(){
      if($(this).hasClass("down")){
        $(this).find(".content").slideUp();
        $(this).removeClass("down");
        $(this).find("span.mais").html("+");

      } else {
      items.find("span.mais").html("+");
      items.find(".content").slideUp();
      $(this).find("span.mais").html("-");
      items.removeClass("down");
      $(this).addClass("down");
      $(this).find(".content").stop(false,true).slideToggle();
      }

    });
    
  }
})( jQuery );