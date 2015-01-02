function majax(){
    var self = this;
    self.statusKey = "statusCode";
    self.msgKey    = "msg";
    self.dataKey   = "data";
    self.events    = {};
    self.on = function(code,callback){
        self.events[code] = callback;
    }
    self.getUrl = function(route,scope){
        return path_base+"/index.php";
    }
    self.ajax = function(methodReq, route,scope,params,async){
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
                var callback = self.events[d[self.statusKey]];
                if(typeof(callback) == "function") {
                    return callback(d[self.msgKey],d[self.dataKey]);
                } else {
                    alert(d[self.msgKey]);
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
    self.post = function(route,params,scope,async){
        self.ajax("POST", route,scope,params,async);
    }
    self.get = function(route,params,scope,async){
        self.ajax("GET", route,scope,params,async);
    }
    self.onError = function(d,m,o){
        console.log(o);
                console.log(m);
                console.log(d);
    }
    self.onComplete = function(){

    }

}

