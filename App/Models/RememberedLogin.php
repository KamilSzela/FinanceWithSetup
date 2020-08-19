<?php

namespace App\Models;
use \App\Models\User;
use \App\Token;
use PDO;
/**
* Remembered login model
* php 7.3.8
*/
class RememberedLogin extends \Core\Model
{
	/**
	* find a remembered login model by the token
	* @param string $token the remembered login token
	* @return mixed remembered login object if found null otherwise
	*/
	public static function findByToken($token)
	{
		$token = new Token($token);
		$token_hash = $token->getHash();
		
		$sql = "SELECT * FROM remembered_logins WHERE token_hash = :token_hash";
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$stmt->execute();
		return $stmt->fetch();
	}
	/**
	* get the user model assiciated with the remembered login
	* @return user the user model
	*/
	public function getUser()
	{
		// przekazujemy parametr user_id wczesniej zwrócony do obiektu klasy rememberedLogin zwracanym w klasie Auth
		return User::findByID($this->user_id);
	}
	/**
	* see if the remember token has expired or not, based on the current system time
	* @ return boolean true if the token has expired, flase otherwise
	*/
	public function hasExpired(){
		return strtotime($this->expires_at) < time();
	}
	/**
	* delete this model
	* @return void
	*/
	public function delete(){
		$sql = 'DELETE FROM remembered_logins WHERE token_hash = :token_hash';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);
		$stmt->execute();
	}
}