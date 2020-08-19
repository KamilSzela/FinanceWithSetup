<?php
 namespace App;
 use \App\Models\User;
 use \App\Models\RememberedLogin;
 /**
 * Authentication
 * php v 7.3.8
 */
 class Auth
 {
	 /**
	 * Login the user
	 * @param User $user the user model
	 * @return void
	 */
	 public static function login($user, $remember_me){
		//generujemy na nowo id sesji w celu uniknięcia ataków hakerskich
		session_regenerate_id(true);
		// sending userid to the session - id is stored on the server so its safe to do this
		$_SESSION['user_id'] = $user->id; 
		
		if($remember_me){
			if($user->rememberLogin()){
				setcookie('remember_me', $user->remember_token, $user->expiry_time, '/');
			}
		}
	 }
	 public static function logout(){
		 //unset all of the session variables
			$_SESSION = [];
			// delete the session cookie
			if(ini_get('session.use_cookies')){
				$params = session_get_cookie_params();
				
				setcookie(
					session_name(),
					'',
					time() - 42000,
					$params['path'],
					$params['domain'],
					$params['secure'],
					$params['httponly'],
					
				);
			}
			// finally destroy the session
			session_destroy();
			//destroing cookie
			static::forgetLogin();
	 }
	 /**
	 * return the indicator of whether a user is logged in or not
	 * @return boolean
	 
	 public static function isLoggedIn(){
		 return isset($_SESSION['user_id']);
	 }
	 */
	 /** 
	 * remember the originally requested page
	 * @return void
	 */
	 public static function rememberRequestedPage(){
		 $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
	 }
	 /**
	 * get the originally requested page to return to after requiring login or default to the homepage
	 * @return void
	 */
	 public static function getReturnToPage()
	 {
		 // if the return_to doesn't exist in the session we return url to the home page i.e. empty url
		 return $_SESSION['return_to'] ?? '/';
	 }
	 /**
	 * get the current logged in user, from the session or remember hte cookie 
	 * @retrun mixed the user model or null if i=not logged in
	 */
	 public static function getUser(){
		 if(isset($_SESSION['user_id'])){
			 return User::findByID($_SESSION['user_id']);
		 } else {
			 return static::loginFromRememberedCookie();
		 }
	 }
	 /**
	 * login the user from a remembered login cookie
	 * @return mixed the user model if login cookie is found,null otherwise
	 */
	 public static function loginFromRememberedCookie(){
		 $cookie = $_COOKIE['remember_me'] ?? false;
		 
		 if($cookie){
			 $remembered_login = RememberedLogin::findByToken($cookie);
			 if($remembered_login && !$remembered_login->hasExpired()){
				 //metoda getuser wywołuje metodę User::findByID, zwracającą obiekt klasy user
				 $user = $remembered_login->getUser();
				 static::login($user, false);
				 
				 return $user;
			 }
		 }
	 }
	 /**
	 * forget the remembered login, if present
	 * @return void
	 */
	 protected static function forgetLogin()
	 {
		 $cookie = $_COOKIE['remember_me'] ?? false;
		 if($cookie){
			 $remembered_login = RememberedLogin::findByToken($cookie);
			 if($remembered_login){
				 $remembered_login->delete();
			 }
			 setcookie('remember_me','',time()-3600);//set to expire in the past
		 }
	 }
 }