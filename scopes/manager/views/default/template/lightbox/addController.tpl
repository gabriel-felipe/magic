<h2>Adicionar Controller</h2>

<br />
<input id='controller-name' type="text" placeholder='Nome do Controller' class='grid6'/>
<select id='controller-scope' name="" id="" class='grid6 margin-l'>
    <?php foreach ($scopes as $scope): ?>
    <option><?php echo $scope ?></option>
        
    <?php endforeach ?>
</select>
<div class="clear"></div>
<input id='controller-methods' type="text" placeholder='Métodos, separados por vírgula' class='grid12 margin-t'>
<div id='error-controller' class='error margin-v' style='display:none;'></div>
<div class="clear margin-b"></div>
<button class='bt-red grid4 fright margin-l' onclick='lightbox.close();'>Cancelar</button>
<button id='bt-save-controller' class='bt-green grid4 fright'>Cadastrar</button>

<div class="clear"></div>
<script>
    sendControllerAjax = new majax;
    sendControllerAjax.onSuccess = function(msg){
        $("#error-controller").hide();
        lightbox.open(msg);
    }
    sendControllerAjax.onFail    = function(msg){
        $("#error-controller").html(msg);
        $("#error-controller").show();
    }
    $("#bt-save-controller").click(function(){
        data = {
            "name": $("#controller-name").val(),
            "scope": $("#controller-scope").val(),
            "methods": $("#controller-methods").val(),
        }
        sendControllerAjax.post("code/controller/send",false,data);
    });
</script>