<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\User;
/** 
* Sign up controller
*
* php 7.3.8
*/
class Signup extends \Core\Controller
{
	/**
	* Show the signup page
	* @return void
	*/
	public function newAction(){
		View::renderTemplate('Signup/new.html');
	}	
	
	public function createAction(){
		$user = new User($_POST);
		
		if($user->save()){
			$user_id = User::getUsersIdFromADatabase($user->email);
			if($user_id == 0){
				View::renderTemplate('Signup/new.html', [
				'user' => $user
			]);
			}
			$user->fillTablesInDatabase($user_id);
			$user->sendActivationEmail();
			$this->redirect('/signup/success');
			exit();
		}else{
			View::renderTemplate('Signup/new.html', [
				'user' => $user
			]);
		}				
	}
	public function successAction(){
		View::renderTemplate('Signup/success.html');
	}
	/**
	* Activate a new account
	* @return void
	*/
	public function activateAction(){
		User::activate($this->route_params['token']);
		$this->redirect('/signup/activated');
	}
	/**
	* show the activation success page
	* @return void
	*/
	public function activatedAction(){
		View::renderTemplate('Signup/activated.html');
	}
}