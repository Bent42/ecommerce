<?php 

use \Hcode\Model\User;

function formatPrice($vlprice){

       return number_format($vlprice, 2, ",", ".");   

 }

 function checkLogin($inadim = true){

 	return User::checkLogin($inadim);

 }

 function getUserName(){

 	$user = User::getFromSession();
 	
 	return $user->getdesperson();
 }

 ?>