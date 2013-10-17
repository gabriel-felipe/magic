<h2>Adicionar DbModel</h2>

<br />
<input id='DbModel-table' type="text" placeholder='Tabela' class='grid4'/>
<input id='DbModel-single' type="text" placeholder='Single' class='grid4 margin-l'/>
<input id='DbModel-plural' type="text" placeholder='Plural' class='grid4 margin-l'/>
<div class="clear"></div>
<div class="clear margin-t">
    <select id='DbModel-scope' name="" id="" class='grid6 margin-t'>
        <?php foreach ($scopes as $scope): ?>
        <option><?php echo $scope ?></option>
            
        <?php endforeach ?>
    </select>

    <button class='bt-red grid3 fright margin-l' onclick='lightbox.close();'>Cancelar</button>
    <button id='bt-save-DbModel' class='bt-green grid3 fright'>Cadastrar</button>
</div>
<div class="clear margin-v"></div>
<div id='error-DbModel' class='error margin-v' style='display:none;'></div>
<div id='success-DbModel' class='success margin-v' style='display:none;'></div>
<div class="clear margin-b"></div>


<div class="clear"></div>
<script>
    sendDbModelAjax = new majax;
    sendDbModelAjax.onSuccess = function(msg){
        $("#error-DbModel").hide();
        $("#success-DbModel").html(msg);
        $("#success-DbModel").show();
    }
    sendDbModelAjax.onFail    = function(msg){
        $("#success-DbModel").hide();
        $("#error-DbModel").html(msg);
        $("#error-DbModel").show();
    }
    $("#bt-save-DbModel").click(function(){
        data = {
            "single": $("#DbModel-single").val(),
            "plural": $("#DbModel-plural").val(),
            "table": $("#DbModel-table").val(),
            "scopo": $("#DbModel-scope").val(),
        }
        sendDbModelAjax.post("code/dbmodel/send",data);
    });
</script>