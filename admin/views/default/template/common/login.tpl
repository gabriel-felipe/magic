<!-- <div id='login' class="grid5 centro">
	
	<div class="box-back">

		<form action='<?php echo $action ?>' method='post'>
			<?php
				if($erro){
				?>
				<p class='alert' style='margin-left:10px;'><strong>Erro: </span><?php echo $erro; ?> </p>
				<?php
				}
			?>
			<div class="campo">
				<label class='grid4 ' for="usename">Username:</label>
				<input class="grid4 " type="text" name='username'>
			</div>
			<div class="campo">
				<label class='grid4' for="password">Senha</label>
				<input class="grid4 " type="password" name='password'>
			</div>
			<input type="submit" class='grid4 '>
			
		</form>
		<div class="clear"></div>
	</div>
</div>
 -->
 	<div id='login' class="grid5 centro">
		<form action='<?php echo $action ?>' method='post' class='grid5'>
			<div class="box-back" style=''>
				<div id="linha-topo">
					<div class="barra um"></div>
					<div class="barra dois"></div>
					<div class="barra tres"></div>
					<div class="barra quatro"></div>
					<div class="barra cinco"></div>
				</div>
				<img src='<?php echo base_theme ?>/image/logotipo-webingpro.png' />
				<p class='italic grid4 centro talign-center color-dark' style='margin-bottom:20px;'>Bem vindo ao sistema de <strong>controle interno</strong>. Entre abaixo com suas credenciais.</p>
				
				<?php
					if($erro){
					?>
					<div class='clear'></div>
					<p class='alert grid4 centro talign-center' style='margin-left:10px;'><strong>Erro: </strong><?php echo $erro; ?> </p>
					<div class='clear'></div>
					<?php
					}
				?>

				
				<div class='clear'></div>
				<div class="campo txt icon-user grid4 centro">
					<input class="grid4" type="text" name='username'>
				</div>
				<div class='clear'></div>
				<div class="campo txt icon-lock">
					<input class="grid4 " type="password" name='password'>
				</div>
				<div class='clear'></div>

			</div>
			<div class='cinza grid5 box-back'>
				<input type="submit" class='grid4 ' value='Acessar o Sistema' />
			</div>
			<!--
			<div class='senha box-back'>
				<a href='#'><strong>Esqueceu</strong> sua senha?</a>
				<div class="clear"></div>

			</div>
			-->
			
			<div class="clear"></div>
		</form>
	</div>
