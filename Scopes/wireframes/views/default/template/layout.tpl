<div id="page-holder">
	<?php echo $this->content ?>
</div>
<script>
	$(document).ready(function(){
		var ajax = new majax;
		ajax.on(200,function(msd,data){
			if ($("#page-holder").attr("data-page") == data['pageName']) {
				$("#page-holder").html(data['page']);
			};
			ajax.post("home/getPage",{"page":$("#page-holder").attr("data-page")});
		});
		ajax.on(500,function(msd,data){
			ajax.post("home/getPage",{"page":$("#page-holder").attr("data-page")});
		});
		ajax.post("home/getPage",{"page":$("#page-holder").attr("data-page")});
		$("body").on("click","#page-list .page",function(){
			$("#page-list .page").removeClass("ativa");
			page = $(this).attr("data-page");
			$("#page-holder").attr("data-page",page);
			$(this).addClass("ativa");
			ajax.post("home/getPage",{"page":page});
		});
	})
	
</script>
<?php echo $this->sidebar ?>