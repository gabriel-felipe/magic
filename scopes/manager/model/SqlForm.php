<?php 

/**
* Classe responsável por otimizar o gerenciamento de forms, através da estrutura de banco de dados.
*/
class SqlForm
{
	protected $db;
	function __construct(){
		$this->db = new bffdbmanager;
	}
	function parseTable($table){
		$columns = $this->db->fetch_columns($table);
		$result = array();
		foreach ($columns as $column) {
			$result[$column['name']] = $this->parseColumn($column['name'],$column['type'],$column['key']);
		}
		return $result;
	}
	function nameToLabel($nome){
		return ucfirst(str_replace("_"," ",$nome));
	}
	function parseColumn($nome,$tipo,$key){	
		$field = array(
			"name" => $nome,
			"label" => $this->nameToLabel($nome),
			"sqlType"=> $tipo,
			"validadores" => array(),
			"limpadores" => array(),
		);
		if ($key == "PRI") {
			$field['type'] = "hidden";
			return $field;
		}
		if (in_array($nome,array("thumb","imagem","foto","banner")) and strpos($tipo, "varchar") !== false) {
			$field['type']	= "image";
			$field['validadores'][] = "StringValidator(2,255)";
			$field['validadores'][] = "FileExistsValidator()";
			$field['limpadores'][]  =  "CopyToSanitizer(path_root.\"/uploads/table\")";
			return $field;
		}
		if ($tipo == "date" or $tipo == "datetime") {
			$field['type'] = "date";
			return $field;
		}
		if (strpos($tipo, "int") !== false and $key == "MUL") {
			$field['type'] = "selectFK";
			$referencedTable = str_replace("_id","",$nome);
			$colunas = $this->db->fetch_columns($referencedTable);
			$colunas = array_map(function($v) use ($referencedTable) {return $referencedTable."/".$v['name'];},$colunas);
			$field['possibleLabels'] = $colunas;
			$field['validadores'][] = "RecordExistsValidator(\"$referencedTable\",\"$nome\")";
			$field['limpadores'][] = "SpecialCharsSanitizer()";
			return $field;
		}
		if (strpos($tipo, "enum") !== false) {
			$field['type'] = "select";
			preg_match_all("/^enum\((.+)\)\$$/",$tipo,$matches);
			$opcoes = $matches[1][0];
			$opcoes = explode("','",trim($opcoes,"'"));
			$options = array();
			foreach ($opcoes as $opt) {
				$options[] = array("value"=>$opt,"label"=>$opt);
			}
			$field['opcoes'] = $options;
			$field['limpadores'][] = "SpecialCharsSanitizer()";

			return $field;
		}
		if (strpos($tipo, "int") !== false) {
			$field['type'] = "number";
			return $field;
		}
		if (strpos($tipo, "varchar") !== false) {
			$field['type'] = "text";
			$field['validadores'][] = "StringValidator(2,255)";
			$field['limpadores'][] = "SpecialCharsSanitizer()";
			return $field;
		}
		if ($tipo == "text") {
			$field['type'] = "textarea";
			$field['validadores'][] = "StringValidator()";
			$field['limpadores'][] = "SpecialCharsSanitizer()";
			return $field;
		}
		return $field;
	}
	function getInputHtml($input){
		$html = "";
		if ($input['type'] == "hidden") {
			$html = "
			<input name='{$input['name']}' type='hidden'>
			";
		} elseif($input['type'] == "text") {
			$html = "
			<div class='campo {$input['name']}'>
				<label for='{$input['name']}'>{$input['label']}</label>
				<input name='{$input['name']}' type='text' />
			</div>
			";
		} elseif($input['type'] == "textarea") {
			$html = "
			<div class='campo {$input['name']}'>
				<label for='{$input['name']}'>{$input['label']}</label>
				<textarea name='{$input['name']}'></textarea>
			</div>
			";
		}  elseif($input['type'] == "date") {
			$html = "
			<div class='campo {$input['name']}'>
				<label for='{$input['name']}'>{$input['label']}</label>
				<input name='{$input['name']}' type='date' />
			</div>
			";
		} elseif($input['type'] == "number") {
			$html = "
			<div class='campo {$input['name']}'>
				<label for='{$input['name']}'>{$input['label']}</label>
				<input name='{$input['name']}' type='number' />
			</div>
			";
		} elseif($input['type'] == "boolean") {
			$html = "
			<div class='campo {$input['name']}'>
				<label for='{$input['name']}'>{$input['label']}</label>
				<select name='{$input['name']}'>
					<option value='0'>Não</option>
					<option value='1'>Sim</option>
				</select>
			</div>
			";
		} elseif ($input['type'] == "selectFK") {
			$label = explode("/",$input['labelfrom']);
			$table = $label[0];
			$value = $label[0]."_id";
			$label = $label[1];
			$html = "
			<?php 
			\$".$table."Opcoes = new dbmodelplural(\"".$table."\",array(\"".$table."_id\",\"titulo\"));
	   		\$".$table."Opcoes->all();
	   		\$".$table."Opcoes = \$".$table."Opcoes->info();
	   		\$".$table."Opcoes = array_map(function(\$info){
	   				\$data = array(\"label\"=>\$info['".$label."'],\"value\"=>\$info['".$table."_id']);
	   			return \$data;
	   		},\$".$table."Opcoes);
			?>
			<div class='campo {$input['name']}'>
				<label for='{$input['name']}'>{$input['label']}</label>
				<select name='{$input['name']}'>

					<?php foreach(\$".$table."Opcoes as \$opcao){ ?>
						<option value=\"<?php echo \$opcao"."['value']"." ?> \"><?php echo \$opcao"."['label']"." ?> </option>
					<?php } ?>
				</select>
			</div>
			";
		} elseif ($input['type'] == "select") {
			$html = "
			<div class='campo {$input['name']}'>
				<label for='{$input['name']}'>{$input['label']}</label>
				<select name='{$input['name']}'>
					";

					foreach($input['opcoes'] as $opcao){ 
						$html .= "
						<option value=\"".$opcao['value']."\">".$opcao['label']."</option>";
					}
			$html .= "
				</select>
			</div>
			";
		} elseif ($input['type'] == "image") {
			$html = "
			<div class='campo {$input['name']}'>
				<label for='{$input['name']}'>{$input['label']}</label>
				<img src='<?php echo path_base?>/uploads/no-image.jpg' id='img{$input['name']}'/>
				<input type='hidden' name='{$input['name']}' />
				<script>
					var jcrop{$input['name']} = new customJcrop(\"img{$input['name']}\",2000,537,function(data){
						$(\"#img{$input['name']}\").attr(\"src\",data+\"?\"+Math.random());
						$(\"input[name=".$input['name']."]\").val(data);
					})
				</script>
			</div>
			";
		}
		return $html;
	}
	function getFormInterface($inputs){
		$form = "
		function getForm(){

			\$form = new IForm;";
		foreach ($inputs as $input) {
			$form.= "

			\${$input['name']} = new TextElement('{$input['name']}');
			";
			if (isset($input['validadores'])) {
				foreach ($input['validadores'] as $value) {
					$form .= "\${$input['name']}->addValidator(new $value);
			";
				}
			}
			if (isset($input['limpadores'])) {
				foreach ($input['limpadores'] as $value) {
					$form .= "\${$input['name']}->addSanitizer(new $value);
			";
				}
			}
			
			$form.= "\$form->addElement(\${$input['name']});";
				
				
		}
		$form .=  "

			return \$form;

		}
		";
		return $form;
	}

}

?>
