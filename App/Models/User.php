<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{
  //protected $params = [];
  public $errors = [];
  /**
   * Class constructor
   *
   * @param array $data  Initial property values, argument optional
   * by adding '= []'
   * @return void
   */
  public function __construct($data = [])
  {
    foreach ($data as $key => $value) {
      $this->$key = $value;
    };
  }

  /**
   * Save the user model with the current property values
   *
   * @return void
   */
  public function save()
  {
	$this->validate();
	
	if(empty($this->errors)){
		$password_hash = password_hash($this->password, PASSWORD_DEFAULT);

		$token = new Token();
		$hashed_token = $token->getHash();
		$this->activation_token = $token->getValue();
		
		$sql = 'INSERT INTO users (name, email, password_hash, activation_hash)
				VALUES (:name, :email, :password_hash, :activation_hash)';

		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
		$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
		$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
		$stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

		return $stmt->execute();
		//PDO::execute returns true on success
	}
	return false;
  }
  public function validate()
  {
	  //Name
	  if($this->name == ''){
		  $this->errors[] = 'Imię jest wymagane';
	  }
	  // email adress
	  if(filter_var($this->email, FILTER_VALIDATE_EMAIL) === false){
		  $this->errors[] = 'Niepoprawny adres email';
	  }
	  if(static::emailExists($this->email, $this->id ?? null)){
		  $this->errors[] = 'Adres email już jest używany';
	  }
	  //password
	  if(isset($this->password)){
		  if(strlen($this->password) < 6){
			  $this->errors[] = 'Hasło musi skladać się z co najmniej 6 znaków';
		  }
		  
		  if (preg_match('/.*[a-z]+.*/i', $this->password) == 0){
			  $this->errors[] = 'Hasło musi zawierać co najmniej jedną literę';
		  }
		  
		  if(preg_match('/.*\d+.*/i', $this->password) == 0){
			  $this->errors[] = 'Hasło musi zawierać co najmniej jedną cyfrę';
		  }
	  }
  }
  /** 
  * See if a user record already exists with the specified email
	* @param string $email email adress
	* @param string ignore_id return false anyway if the record found has this id
	* @return boolean True if a record already exists and false otherwise
	*/
	public static function emailExists($email, $ignore_id = null){
		//return static::findByEmail($email) != false;
		$user  = static::findByEmail($email);
			if($user){
				if($user->id != $ignore_id){
					return true;
				}
			}
		return false;
	}
	/** find a user model by email
	*@param string $email 
	*return mixed, user object if found, false otherwise
	*/
	public static function findByEmail($email){
		$sql = 'SELECT * FROM users WHERE email = :email';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		//$stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\User');
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		$stmt->execute();
		//PDO::fetch() returns false if no record is found
		return $stmt->fetch();
	}
	/**
	*	fill tables: *_assigned_to_users wuth default values
	* @param user_id The user id
	* @return void
	*/
	public function fillTablesInDatabase($user_id){
		
		$db = static::getDB();
		
		$insert_Query2 = $db->exec("INSERT INTO expenses_category_assigned_to_users VALUES (null, '$user_id','Jedzenie'),(null, '$user_id', 'Mieszkanie'),(null, '$user_id','Transport'),(null, '$user_id','Telekomunikacja'),(null, '$user_id','Opieka zdrowotna'),(null, '$user_id','Ubranie'),(null, '$user_id','Higiena'),(null, '$user_id','Dzieci'),(null, '$user_id','Rozrywka'),(null, '$user_id','Wycieczka'),(null, '$user_id','Szkolenia'),(null, '$user_id','Książki'),(null, '$user_id','Oszczędności'),(null, '$user_id','Darowizna'),(null, '$user_id','Spłata długów'),(null, '$user_id','Na złotą jesien, czyli emeryturę'),(null, '$user_id','Inne wydatki')");
					
		$insert_Query3 = $db->exec("INSERT INTO incomes_category_assigned_to_users VALUES (null, '$user_id','Wynagrodzenie'),(null, '$user_id','Odsetki bankowe'),(null, '$user_id','Sprzedaż na Allegro'),(null, '$user_id','Inne źródło')");
					
		$insert_Query4 = $db->exec("INSERT INTO payment_methods_assigned_to_users VALUES (null, '$user_id','Gotówka'),(null, '$user_id','Karta Debetowa'),(null, '$user_id','Karta Kredytowa');");
	}
	/**
	* get the user id from the database
	* return id if found, 0 otherwise
	*/
	public static function getUsersIdFromADatabase($user_email){
		$user = static::findByEmail($user_email);
		if($user){
			return $user->id;
		} else {
			return 0;
		}
	}
	/**
	* Authenticate the user by email and password
	* @param string $email email address
	* @param string $passwrod password
	* @return mixed The user object or false if authentication fails
	*/
	public static function authenticate($email, $password){
		$user = static::findByEmail($email);
		if($user && $user->is_active){
			if(password_verify($password, $user->password_hash)){
				return $user;
			}
		}
		return false;
	}
	/**
	* find a user model by id
	* @ param strin $id the user id
	* @return mixed the user object if found null otherwise
	*/
	public static function findByID($id){
		$sql = 'SELECT * FROM users WHERE id = :id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		//setfetchMode - zwracamy wynik jako obiekt a nie tablicę
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		$stmt->execute();
		//PDO::fetch() returns false if no record is found
		return $stmt->fetch();
	}
	/**
	* Remember the login by inserting a new unique token into the remembered_logins table
	* for this user record
	* @return boolean true if login was remembered correctely, flase otherwise
	*/
	public function rememberLogin()
	{
		$token = new Token();
		$hashed_token = $token->getHash();
		// seting hashed token value to paramter for access from outside the class
		$this->remember_token = $token->getValue();
		$this->expiry_time = time() + 60*60*24*30;//30 days from now
		
		$sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at) VALUES (:token_hash, :user_id, :expires_at)';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
		$stmt->bindValue(':expires_at', date('Y-m-d H-i-s', $this->expiry_time), PDO::PARAM_STR);
		
		return $stmt->execute();
	}
	/**
	* send password reset instructions to the user specified
	* @param string $email the email adress
	* @return void
	*/
	public static function sendPasswordReset($email){
		$user = static::findByEmail($email);
		
		if($user){
			if($user->startPasswordReset()){
				$user->sendPasswordResetEmail();
			}
		}
	}
	/** 
	* start the password reset process by generating a new token and expiry
	* @return void
	*/
	protected function startPasswordReset()
	{
		$token = new Token();
		$hashed_token = $token->getHash();
		$this->password_reset_token = $token->getValue();
		
		$expiry_timestamp = time() + 60 * 60 * 2; // two hours from now
		
		$sql = 'UPDATE users SET password_reset_hash = :token_hash, password_reset_expires_at = :expires_at WHERE id = :id';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
		$stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp) ,PDO::PARAM_STR );
		$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
		
		return $stmt->execute();
	}
	/**
	* send password reset instructions in an email to the user
	* @return void
	*/
	protected function sendPasswordResetEmail(){
		// generate the url
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/password/reset/'.$this->password_reset_token;
		$text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
		$html = View::getTemplate('Password/reset_email.html', ['url' => $url]);
		
		Mail::send($this->email, 'Password_reset', $text, $html);
	}
	/**
	* find a user model by password reset token and expiry
	* @param string $token passwrod reset token send to user
	* @return mixed User object if found and token hasn,t expired, null otherwise
	*/
	public static function findByPasswordReset($token){
		$token = new Token($token);
		//get hashed token
		$hashed_token = $token->getHash();
		//check if token hash exists in the database
		$sql = 'SELECT * FROM users WHERE password_reset_hash = :token_hash';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$stmt->execute();
		$user = $stmt->fetch();
		
		if($user){
			//check password reset token hasn't expired
			if(strtotime($user->password_reset_expires_at)>time()){
				return $user;
			}
		}
	}
	/**
	* reset the password
	* @param string $password the new password
	* @return boolean true if the password was updated successfully, false otherwise
	*/
	public function resetPassword($password){
		$this->password = $password;
		$this->validate();
		if (empty($this->errors)){
			$password_hash = password_hash($this->password, PASSWORD_DEFAULT);
			
			$sql = 'UPDATE users 
			SET password_hash = :password_hash, password_reset_hash = NULL, password_reset_expires_at = NULL 
			WHERE id = :id';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
			
			return $stmt->execute();
		}
		
		return false;
	}
	/**
	* send an email to the user containing the activation link
	* @return void
	*/
	public function sendActivationEmail(){
		// generate the url
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/signup/activate/'.$this->activation_token;
		$text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
		$html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);
		
		Mail::send($this->email, 'Account_activation', $text, $html);
	}
	/**
	* Activate the user account with the specified activation token
	* @param striing $value activation token from the url
	* @return void
	*/
	public static function activate($value){
		$token = new Token($value);
		$hashed_token = $token->getHash();
		
		$sql = 'UPDATE users 
				SET is_active = 1,
					activation_hash = NULL 
				WHERE activation_hash = :hashed_token';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);
		$stmt->execute();
	}
	/**
	* change login of logged user
	* param @login - user login @user_id - user id
	* @return string
	*/
	public static function changeLogin($login, $user_id){
		
		if(static::findByID($user_id) == true){
			
			$sql = 'UPDATE users 
				SET name = :name
				WHERE id = :user_id';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':name', $login, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			return '<p class="text-success light-input-bg"><b>Zmieniono nazwe użytkownika</b></p>';
		} else {
			return '<p class="text-danger light-input-bg"><b>Wystąpił bład przy wprowadzaniu do bazy</b></p>';
		}
	}
	/**
	* change email of logged user
	* param @email - user email @user_id - user id
	* @return string
	*/
	public static function changeEmail($email, $user_id){
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
		  return '<p class="text-danger light-input-bg"><b>Niepoprawny adres email</b></p>';
		}		
		if(static::findByEmail($email) == false){
			
			$sql = 'UPDATE users 
				SET email = :email 
				WHERE id = :user_id';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':email', $email, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			return '<p class="text-success light-input-bg"><b>Zmieniono adres email</b></p>';
		} else {
			return '<p class="text-danger light-input-bg"><b>Wystąpił bład przy wprowadzaniu do bazy</b></p>';
		}
	}
	/**
	* change password of logged user
	* param @password - user's new password @user_id - user id
	* @return string
	*/
	public static function changePassword($password, $user_id){
		
		if(static::findByID($user_id) == true){
			
			$sql = 'UPDATE users 
				SET password_hash = :password_hash
				WHERE id = :user_id';
			
			$password_hash = password_hash($password, PASSWORD_DEFAULT);
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			return '<p class="text-success light-input-bg"><b>Ustawiono nowe hasło</b></p>';
		} else {
			return '<p class="text-danger light-input-bg"><b>Wystąpił bład przy wprowadzaniu do bazy</b></p>';
		}
	}
}
