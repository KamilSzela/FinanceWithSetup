<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
	/**
	*Posts controller
	*PHP version 7.3.8
	*/
	
	class Home extends \Core\Controller{
		protected function before(){
			
		}
		protected function after(){

		}
		/**
		* show the index page
		* @return void
		*/
		public function indexAction(){
			View::renderTemplate('Home/index.html');
		}		
	}
?>