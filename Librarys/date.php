<?php 
	class date {
		static function timestamp($date=false,$if_null_then_time=true){
			if($date){
				if(self::is_timestamp($date) and strlen((string)$date) > 4){
					return $date;
				}
				if(is_string($date)){
					$date = trim($date);
					$len = strlen($date);
					$info = array(
						"dia"  => 1,
						"mes"  => 1,
						"ano"  => 1,
						"hora"  => 0,
						"min"  => 0,
						"seg"  => 0,
					);
					$temp = array();
					if($len <= 2) {
						$temp['dia'] = $date;
						$temp['mes'] = date('m');
						$temp['ano'] = date('Y');
					} else {

						$pieces = explode(' ',$date);
						$data = explode('/', $pieces[0]);
						if(count($data) == '2'){
							$temp ['ano'] = date('Y');
						}

						$horas = false;
						if(array_key_exists(1, $pieces)){
							$horas = explode(":",trim($pieces[1]));						
						} else {
							$horasTemp = explode(":",$pieces[0]);

							if(count($horasTemp) > 1){
								$horas = $horasTemp;
							}
						}
						if(count($data) > 1){

							$dataKey = array('dia','mes','ano');
							for($k=0;$k<=2;$k++)
							{
								if(array_key_exists($k, $data)){
									$temp[$dataKey[$k]] = $data[$k];						
								}
							}
						}
						if(count($data) == 1 and count(explode(":", $data[0])) == 1){
							trigger_error("Data Inválida");
							return false;

							
						}
						if($horas){
							$horasKey = array('hora','min','seg');
							for($k=0;$k<=2;$k++)
							{
								if(array_key_exists($k, $horas)){
									$temp[$horasKey[$k]] = $horas[$k];
								}
							}
						}
					}
					
					$limits = array('dia'=>31, 'mes'=>12, 'min'=>60,'hora'=>23,'seg'=>59);
					foreach($temp as $k=>$v){
						if(in_array($k, array('mes','dia','hora','min','seg'))){
							if(!validate::int($v,1,2)){
								unset($temp[$k]);
								trigger_error("Data Inválida");
								return false;
							}
						}
						if($k == 'ano' and !validate::int($v,1,4)){
							unset($temp[$k]);
							trigger_error("Data Inválida");
							return false;
						}
						if(in_array($k, array_keys($limits)) and $v > $limits[$k]){
							unset($temp[$k]);
							trigger_error("Data Inválida");
							return false;
						}
					}
					$info = array_merge($info,$temp);
					return mktime($info['hora'],$info['min'],$info['seg'],$info['mes'],$info['dia'],$info['ano']);
				} else {
					trigger_error("Formato Inválido, data esperada como string.");
					return false;
				}
			} else {
				return ($if_null_then_time) ? time() : 0;
			}
		}

		static function is_timestamp($timestamp){
			 return ((string) (int) $timestamp === (string)$timestamp) 
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);

		}

		static function tempo_entre($time1, $time2, $precision = 6) {
		    // If not numeric then convert texts to unix timestamps
		    if(!self::is_timestamp($time1) and strlen((string)$time1) > 4){
		    	
		   		$time1 = self::timestamp($time1); 	
		   		
		    }
		    if(!self::is_timestamp($time2) and strlen((string)$time2) > 4){

		   		$time2 = self::timestamp($time2); 	
		    } 
		    // If time1 is bigger than time2
		    // Then swap time1 and time2
		    if ($time1 > $time2) {
		      $ttime = $time1;
		      $time1 = $time2;
		      $time2 = $ttime;
		      unset($ttime);
		    }
		 
			 // Set default diff to 0
			$ndays = floor(abs($time1 - $time2)/60/60/24);
			$yeara = date('Y',$time1);
			$yearb = date('Y',$time2);
			// Create temp time from time1 and interval
			$ttime = $time1;
			// Loop until temp time is smaller than time2
			$notWorkDays = 0;
			$notWorkDaysÁrray = array();
			for ($x = $time1+86400; $x <= $time2; $x+=86400 ){
		        if (!self::is_workday($x)){
		        	$notWorkDaysÁrray[date("d/m/Y",$x)] = $x;
		        }
		    }
		   
		    
		    // Set up intervals and diffs arrays
		    $notWorkDays = count($notWorkDaysÁrray);
		    $intervals = array('year','month','day','hour','minute','second');
		    $weekends = 0;
		    $diffs = array();
		 	$times = array();
		    // Loop thru all intervals
		    foreach ($intervals as $interval) {
			    // Set default diff to 0
			    $diffs[$interval] = 0;
			    $times[$interval] = 0;
			    // Create temp time from time1 and interval
			    $ttime = strtotime("+1 " . $interval, $time1);
			    // Loop until temp time is smaller than time2
			    while ($time2 >= $ttime) {
					$time1 = $ttime;
					$diffs[$interval]++;
					// Create new temp time from time1 and interval
					$ttime = strtotime("+1 " . $interval, $time1);
					
			    }
			}
			
			$count = 0;
			
			// Loop thru all diffs
			foreach ($diffs as $interval => $value) {
			      // Break if we have needed precission
			    if ($count >= $precision) {
			    	break;
			    }
			    // Add value and interval 
			    // if value is bigger than 0
			    if ($value > 0) {
					// Add s if value is not 1
				
					// Add value and interval to times array
					$times[$interval] = $value ;
					$count++;
			    }
		    }
		    $times['ndays'] = $ndays;
		 	$times['workday'] = $ndays  - $notWorkDays;
		 	$times['week'] =  floor($ndays/7);
		    return $times;
		}
		static function get_feriados($ano = null,$anob = false)
		{
			if($ano and $anob){
				$response = array();
				for($a=$ano; $a<=$anob; $a++){
					$response = array_merge($response,self::get_feriados($a));
				}
				return $response;
			} else {
				if ($ano === null)
				{
				  $ano = intval(date('Y'));
				}

				$pascoa     = self::easter_date($ano); // Limite de 1970 ou após 2037 da easter_date PHP consulta http://www.php.net/manual/pt_BR/function.easter-date.php
				$dia_pascoa = date('j', $pascoa);
				$mes_pascoa = date('n', $pascoa);
				$ano_pascoa = date('Y', $pascoa);

				$feriados = array(
				  // Tatas Fixas dos feriados Nacionail Basileiras
				  mktime(0, 0, 0, 1,  1,   $ano), // Confraternização Universal - Lei nº 662, de 06/04/49
				  mktime(0, 0, 0, 4,  21,  $ano), // Tiradentes - Lei nº 662, de 06/04/49
				  mktime(0, 0, 0, 5,  1,   $ano), // Dia do Trabalhador - Lei nº 662, de 06/04/49
				  mktime(0, 0, 0, 9,  7,   $ano), // Dia da Independência - Lei nº 662, de 06/04/49
				  mktime(0, 0, 0, 10,  12, $ano), // N. S. Aparecida - Lei nº 6802, de 30/06/80
				  mktime(0, 0, 0, 11,  2,  $ano), // Todos os santos - Lei nº 662, de 06/04/49
				  mktime(0, 0, 0, 11, 15,  $ano), // Proclamação da republica - Lei nº 662, de 06/04/49
				  mktime(0, 0, 0, 12, 25,  $ano), // Natal - Lei nº 662, de 06/04/49

				  // These days have a date depending on easter
				  mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 48,  $ano_pascoa),//2ºferia Carnaval
				  mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 47,  $ano_pascoa),//3ºferia Carnaval	
				  mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 2 ,  $ano_pascoa),//6ºfeira Santa  
				  mktime(0, 0, 0, $mes_pascoa, $dia_pascoa     ,  $ano_pascoa),//Pascoa
				  mktime(0, 0, 0, $mes_pascoa, $dia_pascoa + 60,  $ano_pascoa),//Corpus Cirist
				);

				sort($feriados);
				
				return $feriados;
			}
		}
		static function is_workday($time){
			if(!self::is_timestamp($time)){
				$time = self::timestamp($time);
			}
			$ano = date("Y",$time);
			$timezerado = mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time));
			$feriados = self::get_feriados($ano);
			return !(date("N",$time) > 5 or in_array($timezerado, $feriados));
		}
		static function add_workdays($time,$nworkdays){
			if(!self::is_timestamp($time)){
				$time = self::timestamp($time);
			}
			$ttime = $time;
			$daysAdded = 0;
		    while ($daysAdded < $nworkdays) {
				$ttime = strtotime("+1 day", $ttime);
				
				if(self::is_workday($ttime)){
					$daysAdded++;
				}				
		    }
		    return $ttime;
		}
		
		static function rmktime($h = 0,$i = 0,$s = 0,$m = 0,$d = 0,$y = 0){
			$time = 0;
			$time += $h * 3600;
			$time += $i * 60;
			$time += $s;
			$time += $d * 86400;
			$time += $m * 2629743.83;
			$time += $y * 31556926;
			return $time;
		}
		static function unmktime($time,$get=array('y','m','d','h','i','s'),$date='d/m/y h:i:s'){

			$in_seconds = array('y'=>'31556926','m'=>'2629743.83','d'=>'86400', 'h'=>'3600','i'=>'60','s'=>'1');
			$response = (!$date) ? array() : $date;

			foreach($get as $interval)
			{
				$in_s = $in_seconds[$interval];
				if(is_array($response)){
					$response[$interval] = floor($time/$in_s);
				} else {
					$response = str_replace($interval, floor($time/$in_s), $response);
				}
				$time -= floor($time/$in_s) * $in_s;
			}
			
			return $response;
		}
		static function easter_date ($Year) { 
	  
	      /* 
	      G is the Golden Number-1 
	      H is 23-Epact (modulo 30) 
	      I is the number of days from 21 March to the Paschal full moon 
	      J is the weekday for the Paschal full moon (0=Sunday, 
	        1=Monday, etc.) 
	      L is the number of days from 21 March to the Sunday on or before 
	        the Paschal full moon (a number between -6 and 28) 
	      */ 
	      
	        $G = $Year % 19; 
	        $C = (int)($Year / 100); 
	        $H = (int)($C - (int)($C / 4) - (int)((8*$C+13) / 25) + 19*$G + 15) % 30; 
	        $I = (int)$H - (int)($H / 28)*(1 - (int)($H / 28)*(int)(29 / ($H + 1))*((int)(21 - $G) / 11)); 
	        $J = ($Year + (int)($Year/4) + $I + 2 - $C + (int)($C/4)) % 7; 
	        $L = $I - $J; 
	        $m = 3 + (int)(($L + 40) / 44); 
	        $d = $L + 28 - 31 * ((int)($m / 4)); 
	        $y = $Year; 
	        $E = mktime(0,0,0, $m, $d, $y); 
	        return $E; 
  		} 
	}
?>