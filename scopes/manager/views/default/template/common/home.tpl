<?php echo $header ?>

<div class="scopes">
    <h2>Escopos que compõem o sistema: </h2>
    <div class="scopes-list">
        <button class='scope'>Manager</button>
        <button class='scope'>Public</button>
        <button class='scope'>Admin</button>
    </div>

    <input class='add-scope' type="text" placeholder='Adicionar Escopo'>
    <input class='submit-add-scope' type="submit" value='Adicionar'>
</div>
<div class="sidebar">
    <div class="data-base">
        <div class="error">
        Banco de dados não configurado!
        </div>
        <div class="content">
            <h2>Database:</h2>
            <div class="campos">
                <div class="campo">
                    <label for="">Tipo</label>
                    <input type="text">
                </div>
                <div class="campo">
                    <label for="">Usuário</label>
                    <input type="text">
                </div>
                <div class="campo">
                    <label for="">Senha</label>
                    <input type="text">
                </div>
                <div class="campo">
                    <label for="">Nome</label>
                    <input type="text">
                </div>
                <input type="submit" value='Salvar Configurações' class='salvar'>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="sidebar-shortcuts">
        <a href="#teste">Adicionar Controller</a>
        <a href="#teste">Adicionar DbModel</a>
    </div>
</div>
<div class="routes">
    <table>
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
    <button class='add-route'>Adicionar rota</button>
</div>
