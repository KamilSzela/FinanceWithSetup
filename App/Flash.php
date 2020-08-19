<?php
namespace App;

/** 
* flash notification messages: messages for one time display using session
* for storage between requests.
* php 7.3.8
*/
	class Flash
	{
		/**
		* Success message type
		* @var string
		*/
		const SUCCESS = 'success';
		/**
		* information message type
		* @var string 
		*/
		const INFO = 'info';
		/**
		* warning message type
		* @var string
		*/
		const WARNING = 'warning';
		/**
		* Add a message 
		* @param string $message The message
		* @pram string $type he optional message type, default to success
		* @return void
		*/
		public static function addMessage($message, $type = 'success')
		{
			//create array in the session if it doesn't already exist
			if(!isset($_SESSION['flash_notifications'])){
				$_SESSION['flash_notifications'] = [];				
			}
			// Append message to the array
			$_SESSION['flash_notifications'][] = [
			'body' => $message,
			'type' => $type
			];
		}
		/**
		* get all the messages
		* @return mixed an array with all the messages if set, or null if not
		*/
		public static function getMessages()
		{
			if(isset($_SESSION['flash_notifications'])){
				$message = $_SESSION['flash_notifications'];	
				unset($_SESSION['flash_notifications']);
				return $message;
			}
		}
	}