function majax(){
    var self = this;
    self.statusKey = "status";
    self.msgKey    = "msg";
    self.success   = "success";
    self.dataKey   = "data";
    self.fail      = "fail";
    self.getUrl = function(route,scope){
        return path_base+"/index.php";
    }

    self.ajax = function(methodReq, route,scope,params,success,fail,async){
        if(typeof(scope) == 'undefined' || !scope){
            scope = magic_scope;
        }
        dataParams = {
            'route':route,'scope':scope
        }
        if(typeof(params) != 'undefined' ){
            $.extend(dataParams,params);
        }
        if(typeof(async) == 'undefined'){
            async = true;
        }
        $.ajax({
            url: path_base+"/index.php",
            method: methodReq,
            async: async,
            data: dataParams,
            dataType: 'JSON',
            success: function(d){
                
                if(d[self.statusKey] == self.success){
                    if(typeof(success) == 'function'){
                       return success(d[self.msgKey],d[self.dataKey]);
                    } else {
                        self.onSuccess(d[self.msgKey],d[self.dataKey]);
                        return true;
                    }
                } else {
                    if(typeof(fail) == 'function'){
                       return  fail(d[self.msgKey],d[self.dataKey]);
                    } else {
                        self.onFail(d[self.msgKey],d[self.dataKey]);
                        return false;
                    }
                }       
            },
            error: function(d,m,o){
                self.onError(d,m,o);
                
            },
            complete: function(){
                self.onComplete();
            }
        });
    }
    self.post = function(route,scope,params,success,fail,async){
        self.ajax("POST", route,scope,params,success,fail,async);
    }
    self.get = function(route,scope,params,success,fail,async){
        self.ajax("GET", route,scope,params,success,fail,async);
    }
    self.onSuccess   = function(msg,data){
        alert(msg);
    }
    self.onFail   = function(msg,data){
        alert(msg);
    }
    self.onError = function(d,m,o){
        console.log(o);
                console.log(m);
                console.log(d);
    }
    self.onComplete = function(){

    }

}

