var lightbox = new function(){
    var self    = this;
    self.holder = $("#lightbox");
    self.closeButton = self.holder.find(".close");
    self.contentHolder = self.holder.find(".content");

    self.open = function(content){
        self.contentHolder.html(content);
        self.holder.fadeIn();
    }
    self.close = function(){
        self.holder.fadeOut();
    }
    self.closeButton.click(function(){
        self.close();
    });
    self.close();
    
}