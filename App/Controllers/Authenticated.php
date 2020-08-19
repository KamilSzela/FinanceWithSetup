<?php
namespace App\Controllers;
/**
* Authenticated base controller
* php 7.3.8
*/
	abstract class Authenticated extends \Core\Controller
	{
		/** 
		* Require the user to be authenticated before giving access to all methods in the controller
		*
		* @return void
		*/
		protected function before(){
			$this->requireLogin();
		}
	}
