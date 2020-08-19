<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use App\Auth;
use App\Flash;

/** 
* Login controller
*
*php v 7.3.8
*/
	class Login extends \Core\Controller
	{
		/** Show the login page
		*@return void
		*/
		public function newAction(){
			View::renderTemplate('Login/new.html');
		}
		public function createAction(){
			$user = User::authenticate($_POST['email'], $_POST['password']);
			//checking if checkbox is on
			$remember_me = isset($_POST['remember_me']);
			if($user){
				//ustawia user_id w sesji i generuje nowe id sesyjne
				Auth::login($user, $remember_me);
				//remember the login ...
				Flash::addMessage('Logowanie zakończone sukcesem!');				
				
				$this->redirect(Auth::getReturnToPage());
				
			}else {
				Flash::addMessage('Logowanie nie powiodło się. Proszę spróbować ponownie.', Flash::WARNING);
				View::renderTemplate('Home/index.html', [
				'email' => $_POST['email'],
				'remember_me' => $remember_me
				]);
				
			}
		}
		/** 
		* Log out a user
		* @retrunn void
		*/
		public function destroyAction(){
			Auth::logout();
			$this->redirect('/login/show-logout-message');
		}
		/** 
		* show a logegd out flash message and redirect to the homepage. Necessary 
		* to use flash messages as they use the session and at the end of the logout method (destroyAction) the session is destroyed so a new method needs to be called in order to use that session
		* @return void
		*/
		public function showLogoutMessageAction(){
			//addMessage method adds a data to the session
			Flash::addMessage('Wylogowałeś się');
			$this->redirect('/');
		}
	}
	