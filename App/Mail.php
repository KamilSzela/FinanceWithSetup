<?php
namespace App;
use Mailgun\Mailgun;
//use Mailgun\HttpClient\HttpClientConfigurator;
use App\Config;
/**
* Mail
* php 7.3.8
*/
	class Mail 
	{
		/**
		* Send a message
		* @param striing $to recipient
		* @param string $subject subject
		* @param string text text only content of the message
		* @param string $html HTML content of the message
		*/
		public static function send($to, $subject, $text, $html)
		{
			# Instantiate the client.
			/*$mg = Mailgun::create(Config::MAILGUN_API_KEY);
			$domain = Config::MAILGUN_DOMAIN;
			$params = array(
			  'from'    => 'MVC LOGIN <mailgun@sandbox44fe922d025545d7ab093b7160d7f347.mailgun.org>',
			  'to'      => $to,
			  'subject' => $subject,
			  'text'    => $text,
			  'html' => $html
			);

			# Make the call to the client.
			$mg->messages()->send($domain, $params);*/
			
			mail($to,$subject,$text);			
				
		}
	}