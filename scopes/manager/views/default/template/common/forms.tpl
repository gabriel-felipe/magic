<?php echo $header ?>
<h1 class='fl margin-v'>Gerador de Formulários</h1>
<div class="clear"></div>
<div class="campo margin-v">
	<label for="">Selecione a tabela</label>
	<form action="" method="post">
		<select name="table" id="">
			<?php foreach ($tables as $table): ?>
				<option><?php echo $table ?></option>
			<?php endforeach ?>
		</select>
		<input type="submit" value="Gerar">
	</form>
</div>

<?php if ($fieldStructure): ?>
<div class="estrutura-dos-campos">
	<form action="<?php echo $codeGenerator ?>" method="post">

		<?php $i=0; foreach ($fieldStructure as $field): $i++; ?>
		<div class="campo grid12 padding margin-t" style="background:#fff;">
			<h3 class='fl'><?php echo $field['label'] ?></h3>
			<button class="bt-red remover fr">Remover</button>
			<div class="clear"></div>
			<label for="">Label:</label>
			<input type="text" name="input[<?php echo $field['name'] ?>][label]" value="<?php echo $field['label'] ?>">
			<br><br>
			<label for="">Name:</label>
			<input type="text" name="input[<?php echo $field['name'] ?>][name]" value="<?php echo $field['name'] ?>">
			<br><br>
			<label for="">Tipo:</label>
			<select name="input[<?php echo $field['name'] ?>][type]" id="">
				<option <?php echo ($field['type'] == "text") ? "selected='selected'" : "" ?>value="text">Text</option>
				<option <?php echo ($field['type'] == "textarea") ? "selected='selected'" : "" ?>value="textarea">Textarea</option>
				<option <?php echo ($field['type'] == "hidden") ? "selected='selected'" : "" ?>value="hidden">Hidden</option>
				<option <?php echo ($field['type'] == "image") ? "selected='selected'" : "" ?>value="image">Image</option>
				<option <?php echo ($field['type'] == "date") ? "selected='selected'" : "" ?>value="date">Date</option>
				<option <?php echo ($field['type'] == "number") ? "selected='selected'" : "" ?>value="number">Number</option>
				<option <?php echo ($field['type'] == "boolean") ? "selected='selected'" : "" ?>value="boolean">Boolean</option>
				<option <?php echo ($field['type'] == "select") ? "selected='selected'" : "" ?>value="select">Select</option>
				<option <?php echo ($field['type'] == "selectFK") ? "selected='selected'" : "" ?>value="selectFK">Select FK</option>
			</select>
			<br><br>
			<?php if (isset($field['possibleLabels'])): ?>
				<label for="">Coluna Label</label>
				<select name="input[<?php echo $field['name'] ?>][labelfrom]" id="">
					<?php foreach ($field['possibleLabels'] as $label): ?>
						<option><?php echo $label ?></option>
					<?php endforeach ?>
				</select>
			<?php endif ?>
			<?php if (isset($field['opcoes'])): ?>
				<br />
				<label for="">Opções: </label>
				<div class="clear padding-b"></div>
				<?php foreach ($field['opcoes'] as $key => $opcao): ?>
					<input type="text" name='input[<?php echo $field['name'] ?>][opcoes][<?php echo $key ?>][value]' value='<?php echo $opcao['value'] ?>' class='grid6 margin-t'>
					<input type="text" name='input[<?php echo $field['name'] ?>][opcoes][<?php echo $key ?>][label]' value='<?php echo $opcao['label'] ?>' class='grid6 margin-t margin-l'>
				<?php endforeach ?>
			<?php endif ?>

			<strong style="color:#003; font-size:1.2em; display:inline-block;float:left">Validadores: </strong>
			<button class='fright padding addValidador'>Adicionar Validador</button>
			<div class="clear"></div>
			<br />
			<div class="validadores">
				<?php if (isset($field['validadores'])): ?>
					<?php foreach ($field['validadores'] as $validador): ?>
						<select name="input[<?php echo $field['name'] ?>][validadores][]" id="" class="margin-v">
							<option><?php echo $validador ?></option>
							<option>StringValidator(3,500)</option>
							<option>FileExistsValidator()</option>
							<option>RecordExistsValidator("table","column")</option>
						</select>
					<?php endforeach ?>
				<?php endif ?>
			</div>
			<div class="clear"></div>
			<strong style="color:#003; font-size:1.2em; display:inline-block;float:left">Limpadores: </strong>
			<button class='fright padding addLimpador'>Adicionar Limpador</button>
			<div class="clear"></div>
			<br />

			<div class="limpadores">
				<?php if (isset($field['limpadores'])): ?>
					<?php foreach ($field['limpadores'] as $limpador): ?>
						<select name="input[<?php echo $field['name'] ?>][limpadores][]" id="" class="margin-v">
							<option><?php echo $limpador ?></option>
							<option>StringValidator(3,500)</option>
							<option>FileExistsValidator()</option>
							<option>RecordExistsValidator("table","column")</option>
						</select>
					<?php endforeach ?>
				<?php endif ?>
			</div>


		</div>	
		<?php endforeach ?>
		<div class="clear"></div>
		<input type="submit" value="Gerar Código">
	</form>
</div>
<?php endif ?>
<script>
	$(".addValidador").click(function(){
		$(this).parent().find(".validadores").append('\
			<select name="input[<?php echo $field['name'] ?>][validadores][]" id="" class="margin-v">\
				<option>StringValidator(3,500)</option>\
				<option>FileExistsValidator()</option>\
				<option>RecordExistsValidator("table","column")</option>\
			</select>');
		return false;
	})
	$(".addLimpador").click(function(){
		$(this).parent().find(".limpadores").append('\
			<select name="input[<?php echo $field['name'] ?>][limpadores][]" id="" class="margin-v">\
				<option>SpecialCharsSanitizer()</option>\
				<option>CopyToSanitizer(path_root."/uploads/pasta")</option>\
				<option>SlugSanitizer()</option>\
			</select>');
		return false;
	})
	var ctrlDown = false;
	var shiftDown = false;
	$(window).keydown(function(evt){
		if (evt.which == '16') {
			shiftDown = true;
		}
		if (evt.which == '17'){
			
			ctrlDown = true;
		}
	})
	$(".limpadores,.validadores").on("focus","select",function(evt){
		if (ctrlDown) {
			$(this).remove();	
		};
	})
	$("button.remover").click(function(){
		$(this).parent().remove();
	})
</script>
<?php echo $footer ?>