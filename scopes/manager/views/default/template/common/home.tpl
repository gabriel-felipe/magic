<?php echo $header ?>

<div class="scopes">
    <h2>Escopos que compõem o sistema: </h2>
    <div class="scopes-list">
        <?php foreach ($scopes as $scope): ?>
        <button class='scope'><?php echo $scope; ?></button>    
        <?php endforeach ?>
    </div>
    <input class='add-scope' type="text" placeholder='Adicionar Escopo'>
    <input class='submit-add-scope' onclick="addScope();" type="submit" value='Adicionar'>
</div>
<div class="sidebar">
    <div class="data-base">
        <?php if ($dbConfig['DB_ACTIVE'] == 0 or !$dbConfig['db_driver'] or !$dbConfig['db_host'] or !$dbConfig['db_user'] or !$dbConfig['db_name']): ?>
            
        
        <div class="error">
        Banco de dados não configurado!
        </div>
        <?php endif ?>
        <div class="content">
            <h2>Database:</h2>

            <div class="campos campos-db">
                <div class="campo">
                    <label for="">Db Ativo</label>
                    <select name='DB_ACTIVE'>
                        <option value='1'>Ativo</option>
                        <option value='0' <?php echo (!$dbConfig['DB_ACTIVE']) ? "selected='selected'" : ""?>>Inativo</option>
                    </select>
                </div>
                <div class="clear"></div>
                <div class="campo">
                    <label for="">Tipo</label>
                    <input type="text" name='db_driver' value="<?php echo $dbConfig['db_driver'] ?>">
                </div>
                <div class="campo">
                    <label for="">Host</label>
                    <input type="text" name='db_host' value="<?php echo $dbConfig['db_host'];?>">
                </div>
                <div class="campo">
                    <label for="">Usuário</label>
                    <input type="text" name='db_user' value="<?php echo $dbConfig['db_user'];?>">
                </div>
                <div class="campo">
                    <label for="">Senha</label>
                    <input type="text" name='db_password' value="<?php echo $dbConfig['db_password'];?>">
                </div>
                <div class="campo">
                    <label for="">Nome</label>
                    <input type="text" name='db_name' value="<?php echo $dbConfig['db_name'];?>">
                </div>
                <input type="submit" value='Salvar Configurações' onclick='updateDb(); return false;'class='salvar'>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="sidebar-shortcuts">
        <button href="#teste" onclick='openAddController();'>Adicionar Controller</button>
        <button href="#teste" onclick="openAddDbModel();">Adicionar DbModel</button>
    </div>
</div>
<div class="routes">
   <!--  <table>
        <thead>
        <tr class='highlight'>
            <td>Rota</td>
            <td>Regex Parâmetros</td>
            <td>Controller</td>
        </tr>
        </thead>
        <tbody>
        <tr class='highlight title'>
            <td colspan='4' class=''>Manager</td>
        </tr>

        <tr>
            <td>common/teste/{id}</td>
            <td><span><strong>id:</strong> ^[0-9]+$</span></td>
            <td>common_home_teste</td>
        </tr>
        <tr>
            <td>common/teste/{id}</td>
            <td><span><strong>id:</strong> ^[0-9]+$</span></td>
            <td>common_home_teste</td>
        </tr>
        <tr>
            <td>common/teste/{id}</td>
            <td><span><strong>id:</strong> ^[0-9]+$</span></td>
            <td>common_home_teste</td>
        </tr>
        <tr class='highlight title'>
            <td colspan='4' class=''>Admin</td>
        </tr>

        <tr>
            <td>common/teste/{id}</td>
            <td><span><strong>id:</strong> ^[0-9]+$</span></td>
            <td>common_home_teste</td>
        </tr>
        <tr>
            <td>common/teste/{id}</td>
            <td><span><strong>id:</strong> ^[0-9]+$</span></td>
            <td>common_home_teste</td>
        </tr>
        <tr>
            <td colspan='2'><button class='route-controller'>Route This</button></td>
            
            <td>common_home_teste</td>
        </tr>
        </tbody>
    </table>
    <button class='add-route'>Adicionar rota</button> -->
</div>
    <script>
    
        function openAddController(){
            var ajaxController = new majax;
            ajaxController.onSuccess = function(msg,data){
                lightbox.open(msg);
            }
            ajaxController.post("code/controller");
        }
        function openAddDbModel(){
            var ajaxDbModel = new majax;
            ajaxDbModel.onSuccess = function(msg,data){
                lightbox.open(msg);
            }
            ajaxDbModel.post("code/dbmodel");
        }
    
        function addScope(){
            var ajaxScope = new majax;
            var scope = $("input.add-scope").val();
            ajaxScope.onSuccess = function(){
                $(".scopes-list").append("<button class='scope'>"+scope+"</button>");
            }
            ajaxScope.post("common/scopes/novo",{"scopo":scope});
        }
        function updateDb(){
            var ajaxDb = new majax;
            ajaxDb.onSuccess = function(msg){
                lightbox.open("<div class='success'>"+msg+"</div>");
            }
            ajaxDb.onFail = function(msg){
                lightbox.open("<div class='fail'>"+msg+"</div>");
            }
            data = {"db": {}};
            $(".campos-db select,.campos-db input[type='text']").each(function(){
                data['db'][$(this).attr("name")] = $(this).val();
            })
            ajaxDb.post("common/db/update",data);
        }
    </script>
<?php echo $footer; ?>