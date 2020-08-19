<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\User;
/**
* password controller
* php 7.3.8
*/
	class Password extends \Core\Controller
	{
		/**
		* show the forgotten password page
		* @return void
		*/
		public function forgotAction(){
			View::renderTemplate('Password/forgot.html');
		}
		/**
		* send the password reset link to the supplied email
		* @return void
		*/
		public function requestResetAction(){
			User::sendPasswordReset($_POST['email']);
			
			View::renderTemplate('Password/reset_requested.html');
		}
		/**
		* Show the reset password form
		* @return void
		*/
		public function resetAction(){
			$token = $this->route_params['token'];
			$user = $this->getUserOrExit($token);
			// we have exit statement in method above so the view isn't rendered if token is invalid 
				View::renderTemplate('Password/reset.html', [
					'token' => $token
				]);
			
		}
		/**
		* reset the user's password
		* @return void
		*/
		public function resetPasswordAction(){
			$token = $_POST['token'];
			$user = $this->getUserOrExit($token);			
			
			if($user->resetPassword($_POST['password'])){
				View::renderTemplate('Password/reset_success.html');
			} else {
				View::renderTemplate('Password/reset.html', [
					'token' => $token,
					'user' => $user
				]);
				
			}
			
		}
		/**
		* find the user model associated with the password reset token, or end the request with a message
		* @param string Password reset token sent to user
		* @return mixed User object if found and the token hasn't expired null otherwise
		*/
		protected function getUserOrExit($token){
			$user = User::findByPasswordReset($token);
			if($user){
				return $user;
			} else {
				View::renderTemplate('Password/token_expired.html');
				exit;
			}
		}
	}