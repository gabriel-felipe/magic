//Requires common.functions.js
var crud = function(form,pk,item,holder,routes){
	var self = this;
	self.form = $(form);
	self.formSelector = form;
	self.pk = pk;
	self.item = $(item);
	self.itemSelector = item;
	self.itens = $(holder+" "+item);
	self.holder = $(holder);
	self.routes = routes;
	self.ajax = {};
	self.itemSelected = false;
	self.ajax.save = new majax;

	//Messages
	self.alert = {};
	self.alert.remove = "Tem certeza que deseja remover esse conteúdo?";
	self.ajax.save.onSuccess = function(msg,data){
		seletor = self.holder.find("[data-id='"+data[pk]+"']");
		if (seletor.size() > 0) {
			seletor.replaceWith(data['html']);
		} else {
			self.holder.append(data['html']);
		}
		self.resetaForm();
	}
	self.ajax.save.onComplete = function(){
	}

	self.ajax.remove = new majax;
	self.ajax.remove.onSuccess = function(msg,data){
		seletor = self.holder.find("[data-id='"+data[pk]+"']");
		seletor.remove();
	}
	self.ajax.remove.onComplete = function(){
	}

	self.resetaForm = function(){
		self.form.find("input,textarea").each(function(){
			value = $(this)[0].defaultValue;
			$(this).val(value);
		})
	}
	
	self.save = function(data){
		self.ajax.save.post(self.routes.save,data);
	}

	self.presubmit = function(data){
		return data;
	}

	self.getData = function(){
		data = {};
		var o = {};
	    var a = $(self.formSelector).serializeArray();
	    console.log(a);
	    $.each(a, function() {
	        if (o[this.name] !== undefined) {
	            if (!o[this.name].push) {
	                o[this.name] = [o[this.name]];
	            }
	            o[this.name].push(this.value || '');
	        } else {
	            o[this.name] = this.value || '';
	        }
	    });
	    return o;
	}
	self.remove = function(id){
		var data = {};
		data[self.pk] = id;
		self.ajax.remove.post(self.routes.remove,data);
	}
	
	//Events Handler
	self.alreadyclicked = false;
	self.alreadyclickedTimeout = false;
	self.dblclicktime = 200;
	self.events = {};
	self.events.item = {};
	self.events.form = {};

	//Default Events
	self.events.item.beforeclick = function(item,evt){}
	self.events.item.afterclick = function(item,evt){}
	self.events.item.click = function(item,evt){
		var id = $(item).attr("data-id");
		if (ctrlDown && confirm(self.alert.remove)) {				
			self.remove(id);
		} else {
			ctrlDown = false;
		}
	}

	self.events.item.beforedblclick = function(item,evt){}
	self.events.item.afterdblclick = function(item,evt){}
	self.events.item.dblclick = function(item,evt){
		return false;
	}

	self.events.form.submit = function(item,evt){
		evt.preventDefault();
		var data = self.getData();
		console.log(data);
		self.presubmit(data);
		self.save(data);
		return false;
	}
	

	//Binding Events
	self.resetEvents = function(){
		$(self.formSelector).unbind("submit");
		self.holder.off("click."+self.itemSelector);
		self.bindEvents();

	}
	self.bindEvents = function(){
		$(self.formSelector).submit(function(evt){
			var item = this;
			return self.events.form.submit(item,evt);
		});

		self.holder.on("click."+self.itemSelector,self.itemSelector,function(evt){
			$(this).addClass("noselect");
			var item = this;
			if (self.alreadyclicked) {
				$(this).removeClass("noselect");
				self.alreadyclicked=false; // reset
           		clearTimeout(self.alreadyclickedTimeout); // prevent this from happening
				self.events.item.beforedblclick(item,evt);
				self.events.item.dblclick(item,evt);
				self.events.item.afterdblclick(item,evt);
			} else {
				self.alreadyclicked=true;
        		self.alreadyclickedTimeout=setTimeout(function(){
        			$(this).removeClass("noselect");
        			self.alreadyclicked=false; // reset when it happens
        			self.events.item.beforeclick(item,evt);
					self.events.item.click(item,evt);
					self.events.item.afterclick(item,evt);
				},self.dblclicktime);
			}
		})	
	}
	self.bindEvents();
}