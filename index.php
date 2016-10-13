<?php  

// $vcode=new Vcode();

require './Vcode.class.php';
$config['height']=60;
$config['num']=6;
$config['session']=false;
$vcode=new Vcode($config);
$vcode->entry();
// echo $vcode->getCode();

?>