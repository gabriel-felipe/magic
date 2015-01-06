 (function( $ ) {	
  $.fn.equery = function(exp) { 
  	var helpers = {};
  	var self = {};
  	self.C_NumProperties = ["margin-top","width","height","margin-left"];
	self.C_Operators = {
		max: function(value,limit){
			return (value < limit)
		},
		min: function(value,limit){
			return (value > limit)
		},
		is: function(value,limit){
			return (value == limit)
		}
	}
  	helpers.add = function(obj,expression){
		var pieces = expression.split(" ");
		if(pieces.length == 3){
			var operator = pieces[0]
			var cssp = pieces[1]
			var limit = parseInt(pieces[2]);

			if(operator in self.C_Operators){
				if($.inArray(cssp, self.C_NumProperties) != -1){
					var value = parseInt(obj.css(pieces[1]));
					if(self.C_Operators[operator](value,limit)){
						obj.addClass(operator+"_"+cssp+"_"+limit)
					} else {
						obj.removeClass(operator+"_"+cssp+"_"+limit)
						console.log("Falha")
					}
				} else {
					console.log("Erro, propriedade css não encontrada");
				}
			} else {
				console.log("Erro, comparador não encontrado");
			}
		}		
	}
    this.each(function() {
    	helpers.add($(this),exp);
    });
    var objs = this;
    $(window).bind('resize',function(){
    	objs.each(function() {
    		helpers.add($(this),exp);
    	});
    });

  };
})( jQuery );
$(document).ready(function(){
<br />
<font size='1'><table class='xdebug-error xe-warning' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Warning: Invalid argument supplied for foreach() in /var/www/html/projetos/magic/Engine/Document/AbstractAsset.php(167) : eval()'d code on line <i>54</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0015</td><td bgcolor='#eeeeec' align='right'>286672</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='/var/www/html/projetos/magic/index.php' bgcolor='#eeeeec'>../index.php<b>:</b>0</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>0.1131</td><td bgcolor='#eeeeec' align='right'>1742416</td><td bgcolor='#eeeeec'>Magic\Engine\Mvc\Action->execute(  )</td><td title='/var/www/html/projetos/magic/index.php' bgcolor='#eeeeec'>../index.php<b>:</b>164</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>3</td><td bgcolor='#eeeeec' align='center'>0.1136</td><td bgcolor='#eeeeec' align='right'>1811744</td><td bgcolor='#eeeeec'><a href='http://www.php.net/function.call-user-func-array' target='_new'>call_user_func_array</a>
(  )</td><td title='/var/www/html/projetos/magic/Engine/Mvc/Action.php' bgcolor='#eeeeec'>../Action.php<b>:</b>137</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>4</td><td bgcolor='#eeeeec' align='center'>0.1136</td><td bgcolor='#eeeeec' align='right'>1812056</td><td bgcolor='#eeeeec'>ControllerAbout->index(  )</td><td title='/var/www/html/projetos/magic/Engine/Mvc/Action.php' bgcolor='#eeeeec'>../Action.php<b>:</b>137</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>5</td><td bgcolor='#eeeeec' align='center'>0.1140</td><td bgcolor='#eeeeec' align='right'>1838416</td><td bgcolor='#eeeeec'>publicController->render(  )</td><td title='/var/www/html/projetos/magic/scopes/public/controller/about.php' bgcolor='#eeeeec'>../about.php<b>:</b>8</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>6</td><td bgcolor='#eeeeec' align='center'>0.1146</td><td bgcolor='#eeeeec' align='right'>1859464</td><td bgcolor='#eeeeec'>Magic\Engine\Document\MagicDocument->render(  )</td><td title='/var/www/html/projetos/magic/scopes/public/publicController.php' bgcolor='#eeeeec'>../publicController.php<b>:</b>13</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>7</td><td bgcolor='#eeeeec' align='center'>0.1149</td><td bgcolor='#eeeeec' align='right'>1923256</td><td bgcolor='#eeeeec'>include( <font color='#00bb00'>'/var/www/html/projetos/magic/common/templates/layout.html'</font> )</td><td title='/var/www/html/projetos/magic/Engine/Document/MagicDocument.php' bgcolor='#eeeeec'>../MagicDocument.php<b>:</b>106</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>8</td><td bgcolor='#eeeeec' align='center'>0.1154</td><td bgcolor='#eeeeec' align='right'>1956672</td><td bgcolor='#eeeeec'>Magic\Engine\Document\MagicDocument->getScripts(  )</td><td title='/var/www/html/projetos/magic/common/templates/layout.html' bgcolor='#eeeeec'>../layout.html<b>:</b>49</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>9</td><td bgcolor='#eeeeec' align='center'>0.1154</td><td bgcolor='#eeeeec' align='right'>1956672</td><td bgcolor='#eeeeec'>Magic\Engine\Document\Script\ScriptManager->getScripts(  )</td><td title='/var/www/html/projetos/magic/Engine/Document/MagicDocument.php' bgcolor='#eeeeec'>../MagicDocument.php<b>:</b>93</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>10</td><td bgcolor='#eeeeec' align='center'>0.1154</td><td bgcolor='#eeeeec' align='right'>1956720</td><td bgcolor='#eeeeec'>Magic\Engine\Document\AbstractAssetManager->getAssets(  )</td><td title='/var/www/html/projetos/magic/Engine/Document/Script/ScriptManager.php' bgcolor='#eeeeec'>../ScriptManager.php<b>:</b>24</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>11</td><td bgcolor='#eeeeec' align='center'>0.1154</td><td bgcolor='#eeeeec' align='right'>1956856</td><td bgcolor='#eeeeec'>Magic\Engine\Document\AbstractAssetManager->compileLocalAssets(  )</td><td title='/var/www/html/projetos/magic/Engine/Document/AbstractAssetManager.php' bgcolor='#eeeeec'>../AbstractAssetManager.php<b>:</b>173</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>12</td><td bgcolor='#eeeeec' align='center'>0.1155</td><td bgcolor='#eeeeec' align='right'>1957480</td><td bgcolor='#eeeeec'>Magic\Engine\Document\AbstractAsset->compilar(  )</td><td title='/var/www/html/projetos/magic/Engine/Document/AbstractAssetManager.php' bgcolor='#eeeeec'>../AbstractAssetManager.php<b>:</b>116</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>13</td><td bgcolor='#eeeeec' align='center'>0.1156</td><td bgcolor='#eeeeec' align='right'>1981360</td><td bgcolor='#eeeeec'>eval( <font color='#00bb00'>'?> (function( $ ) {	
  $.fn.equery = function(exp) { 
  	var helpers = {};
  	var self = {};
  	self.C_NumProperties = ["margin-top","width","height","margin-left"];
	self.C_Operators = {
		max: function(value,limit){
			return (value < limit)
		},
		min: function(value,limit){
			return (value > limit)
		},
		is: function(value,limit){
			return (value == limit)
		}
	}
  	helpers.add = function(obj,expression){
		var pieces = expression.split(" ");
		if(pieces.length == 3){
			var operator = pieces[0]
			var cssp = pieces[1]
			var limit = parseInt(pieces[2]);

			if(operator in self.C_Operators){
				if($.inArray(cssp, self.C_NumProperties) != -1){
					var value = parseInt(obj.css(pieces[1]));
					if(self.C_Operators[operator](value,limit)){
						obj.addClass(operator+"_"+cssp+"_"+limit)
					} else {
						obj.removeClass(operator+"_"+cssp+"_"+limit)
						console.log("Falha")
					}
				} else {
					console.log("Erro, propriedade css não encontrada");
				}
			} else {
				console.log("Erro, comparador não encontrado");
			}
		}		
	}
    this.each(function() {
    	helpers.add($(this),exp);
    });
    var objs = this;
    $(window).bind('resize',function(){
    	objs.each(function() {
    		helpers.add($(this),exp);
    	});
    });

  };
})( jQuery );
$(document).ready(function(){
<?php foreach($this->equeries as $eq){ ?>
	$("<?=$eq['element']; ?>").equery("<?=$eq['rule']; ?>");
<?php } ?>
})<?php '</font> )</td><td title='/var/www/html/projetos/magic/Engine/Document/AbstractAsset.php' bgcolor='#eeeeec'>../AbstractAsset.php<b>:</b>167</td></tr>
</table></font>
})
 $(document).ready(function(){
	var ajax = new majax;
	ajax.on(
		200,
		function(msg,data){
			$("link").addClass("old");
			$("head").append(data["css"]);
			window.setTimeout(function(){
				$("link.old").remove();
				ajax.post(magic_route,{"css_only":true});
			},100);
		}
	);
	ajax.on(
		304,
		function(msg){
			console.log(msg);
			window.setTimeout(function(){
				ajax.post(magic_route,{"css_only":true});
			},100);
		}
	)
	ajax.onError = function(){
		window.setTimeout(function(){
			ajax.post(magic_route,{"css_only":true});
		},100);
	}
	window.setTimeout(function(){
		ajax.post(magic_route,{"css_only":true});
	},2000)
})
