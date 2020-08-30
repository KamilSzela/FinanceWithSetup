<?php

 namespace App\Controllers;
 
 use \Core\View;
 use \App\Auth;
 use \App\Flash;
 use \App\Models\User;
 use \App\Models\Income;
 use \App\Models\Expense;
 /**
 * Summary controllers
 * php 7.3.8
 */
 
 class Setup extends Authenticated
 {
	/**
	 * Before filter - called each action method
	 * @return void
	 */
	 protected function before(){
		 parent::before();
		 $this->user = Auth::getUser();
	 }
	  /**
	 * show the incomes start site
	 * @return void
	 */
	 public function showAction(){		
		
		 View::renderTemplate('Setup/show.html', [
			'user' => $this->user			
		 ]);
	 }
	 /**
	 * get payment ways of logged user
	 * return json object
	 */
	 public function loadExpencePaymentWays(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		$paymentWays = Expense::getExpencePaymentWays($user_id);		
		echo json_encode($paymentWays);
	 }
	  /**
	 * get cathegories of logged user
	 * return json object
	 */
	 public function loadExpenceCathegories(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		$categories = Expense::getExpenceCathegories($user_id);		
		echo json_encode($categories);
	 }
	   /**
	 * get cathegories of logged user
	 * return json object
	 */
	 public function loadIncomeCathegories(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		$categories = Income::getIncomeCathegories($user_id);		
		echo json_encode($categories);
	 }
	 /**
	 * change login of logged user
	 * return string
	 */
	 public function changeLogin(){
		 $login = $_POST["newLogin"];
		 $this->user = Auth::getUser();	
		 $user_id = $this->user->id;		
		 $response = User::changeLogin($login, $user_id);
		 echo $response;
	 }
	 /**
	 * change email of logged user
	 * return string
	 */
	 public function changeEmail(){
		 $email = $_POST["newEmail"];
		 $this->user = Auth::getUser();	
		 $user_id = $this->user->id;		
		 $response = User::changeEmail($email, $user_id);
		 echo $response;
	 }
	 /**
	 * change password of logged user
	 * return string
	 */
	 public function changePassword(){
		 $password = $_POST["newPassword"];
		 $this->user = Auth::getUser();	
		 $user_id = $this->user->id;		
		 $response = User::changePassword($password, $user_id);
		 echo $response;
	 }
	  /**
	 * add new expence category to the server
	 * return string
	 */
	 public function addNewExpencePaymentWay(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		if(isset($_POST['newPaymentWay'])){
			$paymentWay = htmlspecialchars($_POST['newPaymentWay']);
			 if(Expense::addNewPaymentWay($user_id, $paymentWay)){		
				echo ("<p class=\"text-success light-input-bg\"><b>Dodano nową metodę płatności</b></p>");
			 } else {
				echo ("<p class=\"text-danger light-input-bg\"><b>Wystąpił błąd przy dodawaniu nowej metody do bazy danych lub wpisana metoda już istnieje w bazie</b></p>");
			 }
		} 
	 }
	  /**
	 * remove expense category
	 * return string
	 */
	 public function removeExpencePaymentWay(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		if(isset($_POST['toDelete'])){
			$paymentWayID = $_POST['toDelete'];
			if(Expense::removePaymentWay($user_id, $paymentWayID)){
				echo ("<p class=\"text-success light-input-bg\"><b>Usunięto metodę płatności</b></p>");
			} else {
				echo ("<p class=\"text-danger light-input-bg\"><b>Wystapił błąd przy usuwaniu metody płatności</b></p>");
			}
		} else {
			echo ("<p class=\"text-info light-input-bg\"><b>Wystapił błąd przy przekazywaniu wartości metody płatności</b></p>");
		}
	 }
 }