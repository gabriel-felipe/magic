<?php
/* RESPONSIVE CONFIG */
$gridColumns = 12; //Number of coluns in grids.
$gridMargin = '0.2%';
$breakpoints = array( //Defining breakpoints for definig custom grids. gridprefix => info.
	"xs-"=>array('max'=>"460",'alias'=>'mobile portrait'), 
	"s-"=>array('min'=>"460",'max'=>'860','alias'=>'mobile landscape'),
	"m-"=>array('min'=>"860",'max'=>'960','alias'=>'tablet'),
	""=> array('min'=>"960",'max'=>'1460','alias'=>'usual desktop'),
	"l-"=>array('min'=>"1460",'max'=>'2000','alias'=>'large desktop'),
	"xl-"=>array('max'=>"3000",'alias'=>'really large screen')
);
?>