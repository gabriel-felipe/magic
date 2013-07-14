<div id="linha-topo">
	<div class="barra um"></div>
	<div class="barra dois"></div>
	<div class="barra tres"></div>
	<div class="barra quatro"></div>
	<div class="barra cinco"></div>
</div>
<section class="grid12 centro">
<header>
	<div class='grid3 centro box-back carregando'>
		<div class='grid2 centro talign-center'>Carregando</div>
	</div>
	<div class="grid12" id='topo'>
		<div class='clear'></div>
		<div class="logo fleft">
			<h1>Admin</h1>
		</div>
		<div class="grid4 fright avatar">
			<span class='img fleft'>
				<img src='<?php echo $avatar ?>' width='60' height='60' />
			</span>
			<div class='fleft info'>
				<p>Bem vindo, <strong><?php echo $logged ?></strong></p>
				<div class='clear'></div>
				<span class='italic'>
					<a href='<?php echo $site?>'>Ver site</a>
					<a href='<?php echo $logout?>'>Sair</a>
				</span>
			</div>
			<div class="clear"></div>
			
		</div>
	</div>
	
	<div class="clear"></div>
	
<!-- 	<nav id="menu">
		<ul>
			<a href="#"><li><div><span class="icon inicio"></span><span class="txt"> Início</span></div><span class="borda"></span></li></a><hr/>
			<a href="<?php echo $areas?>">
				<li>
					<div>
						<span class="txt">
							<span style='font-size:18px;' class='icon-suitcase'></span> Áreas de Atuação</span>
					</div>
					<span class="borda"></span>
					<ul>
						<a><li>Teste</li> </a>
						<a><li>Teste</li> </a>
						<a><li>Teste</li> </a>
						<a><li>Teste</li> </a>
					</ul>

				</li>
			</a><hr/>
			<a href="<?php echo $perguntas?>"><li><div><span class="txt"><span style='font-size:18px;'class='icon-question-sign'></span> Perguntas</span></div><span class="borda"></span></li></a><hr/>
	 		<a href="<?php echo $config?>"><li><div><span class="txt"><span class='icon-cogs'></span> Configurações</span></div><span class="borda"></span></li></a>
			
				
			
		</ul>
	</nav>
 -->
 <nav id="menu">
		<ul>
			<li>
				<a href="<?php echo $home ?>">
					<span class='icon-home'></span>	Inicio
				</a>
			</li>
			<li>
				<a href='<?php echo $linkpage1 ?>'>
					<span class='<?php echo $classpage1 ?>'></span>	<?php echo $page1 ?>
				</a>
			</li>
			
			<li class='submenu'>
				<a href="">
					<span class='icon-th'></span>	Kits
				</a>
				<ul>
					<li><a href='<?php echo $kitQuadro; ?>'>Quadros</a></li>
					<li><a href='<?php echo $kitBicicleta; ?>'>Bicicletas</a></li>
				</ul>

			</li>
			<li>
				<a href="<?php echo $suporte ?>">
					<span class='icon-bell-alt'></span>	Suporte
				</a>
			</li>
			<div class='clear'></div>
		</ul>
	</nav>

</header>

