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
				echo ("<p class=\"text-danger light-input-bg\"><b>Wpisana metoda już istnieje w bazie</b></p>");
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
	 /**
	 * add new expense catheogry to database
	 * return string
	 */
	 public function addNewExpenseCategory(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		if(isset($_POST['newExpenseCat'])){
			$category = htmlspecialchars($_POST['newExpenseCat']);
			if(Expense::addNewCategory($user_id, $category)){		
				echo ("<p class=\"text-success light-input-bg\"><b>Dodano nową kategorię płatności</b></p>");
			} else {
				echo ("<p class=\"text-danger light-input-bg\"><b>Wpisana kategoria już istnieje w bazie</b></p>");
			}
		} else {
			echo ("<p class=\"text-info light-input-bg\"><b>Wystapił błąd przy przekazywaniu wartości kategorii wydatku</b></p>");
		}
	 }
	 /**
	 * remove expense category from a database
	 * @return string
	 */
	 public function removeExpenceCategory(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		if(isset($_POST['toDelete'])){
			$paymentWayID = $_POST['toDelete'];
			if(Expense::removeCategory($user_id, $paymentWayID)){
			echo ("<p class=\"text-success light-input-bg\"><b>Usunięto kategorię wydatku</b></p>");
			} else {
				echo ("<p class=\"text-success light-input-bg\"><b>Wystąpił błąd podczas usuwania kategorii wydatku</b></p>");
			}	
		} else {
			echo ("<p class=\"text-info light-input-bg\"><b>Wystapił błąd przy przekazywaniu wartości kategorii wydatku</b></p>");
		}
	 }
	 /**
	 * add new income category to the database
	 * @return string
	 */
	 public function addNewIncomeCategory(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		if(isset($_POST['newIncomeCat'])){
			$category = htmlspecialchars($_POST['newIncomeCat']);	
			if(Income::addNewCategory($user_id, $category)){
				echo ("<p class=\"text-success light-input-bg\"><b>Dodano nową kategorię płatności</b></p>");
			} else {
				echo ("<p class=\"text-danger light-input-bg\"><b>Wpisana kategoria już istnieje w bazie</b></p>");
			}
		} else {
			echo ("<p class=\"text-info light-input-bg\"><b>Wystapił błąd przy przekazywaniu wartości kategorii dochodu</b></p>");
		}
	 }
	  /**
	 * remove income category from a database
	 * @return string
	 */
	 public function removeIncomeCategory(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		if(isset($_POST['toDelete'])){
			$paymentWayID = $_POST['toDelete'];
			if(Income::removeCategory($user_id, $paymentWayID)){
			echo ("<p class=\"text-success light-input-bg\"><b>Usunięto kategorię dochodu</b></p>");
			} else {
				echo ("<p class=\"text-success light-input-bg\"><b>Wystąpił błąd podczas usuwania kategorii dochodu</b></p>");
			}	
		} else {
			echo ("<p class=\"text-info light-input-bg\"><b>Wystapił błąd przy przekazywaniu wartości kategorii dochodu</b></p>");
		}
	 }
	 /**
	 * set limit for expense category
	 * @return string
	 */
	 public function setExpenseLimit(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		if(isset($_POST['limit'])){
			$limit = $_POST['limit'];
			$catID = $_POST['idCat'];
			if(Expense::setExpenseLimit($catID, $limit)){
			echo ("<p class=\"text-success light-input-bg\"><b>Wprowadzono miesięczny limit w kategorii</b></p>");
			} else {
				echo ("<p class=\"text-success light-input-bg\"><b>Wystąpił błąd podczas wprowadzania limitu</b></p>");
			}	
		} else {
			echo ("<p class=\"text-info light-input-bg\"><b>Wystapił błąd przy przekazywaniu wartości limitu dochodu</b></p>");
		}
	 }
 }