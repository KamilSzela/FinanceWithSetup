<?php
	namespace App\Controllers;
	
	use \App\Models\User;
	/**
	* Account controller
	* php 7.3.8
	*/
	class Account extends \Core\Controller
	{
		/** 
		* validate if email is avaiable AJAX
		* @return void
		*/
		public function validateEmailAction(){
			// przy definicji zmiennej zapis =! oznacza że zmienna jest odwrotna do booleana zwracanej wartości z funkcji
			// przy używaniu walidatora jquery AJAX może się zdarzyć że wartość igonre-Id wcale nie będzie przesyłana getem, więc w razie gdyby nie istniała przesyłamy null
			$is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);
			header('Content-type: application/json');
			echo json_encode($is_valid);
		}
	}