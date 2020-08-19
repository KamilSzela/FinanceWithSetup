<?php

namespace App;

/**
* unique random token
* php 7.3.8
*/
	class Token
	{
		/**
		* The token value
		* @var string
		*/
		protected $token;
		
		/**
		* class constructor. create a new random token
		* @return void
		*/
		public function __construct($token_value = null)
		{
			if($token_value){
				$this->token = $token_value;
			} else {
				$this->token = bin2hex(random_bytes(16)); //16 bytes = 128 bits = 32 hex characters
			}
		}
		/**
		* get the token value
		* @return string the value
		*/
		public function getValue()
		{
			return $this->token;
		}
		/**
		* get the hashed token value 
		* @return string the hashed value 
		*/
		public function getHash()
		{
			return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY); //sha256 = 64 chars
		}
	}