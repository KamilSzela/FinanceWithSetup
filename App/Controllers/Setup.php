<?php

 namespace App\Controllers;
 
 use \Core\View;
 use \App\Auth;
 use \App\Flash;
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
	 * add new expence category to the server
	 * return boolean
	 */
	 public function loadIncomeCathegories(){
		$this->user = Auth::getUser();		
		$user_id = $this->user->id;	
		$categories = Expence::addNewPaymentWay($user_id, $_POST);		
		echo json_encode($categories);
	 }
 }