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
 
 class Summary extends Authenticated
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
		
		 View::renderTemplate('Summary/show.html', [
			'user' => $this->user			
		 ]);
	 }
	 /**
	 * get user's expenses from ajax request
	 * @return json object if success
	 */
	public function expencesTables(){
		
		$this->user = Auth::getUser();
		
		$user_id = $this->user->id;
		
		$expenses = Expense::getLoggedUserExpenses($user_id, $_GET);
	
		echo json_encode($expenses);
		
	}
	/**
	*
	*
	*/
	public function expenceSummaryTable(){
		$this->user = Auth::getUser();
		
		$user_id = $this->user->id;
		
		$sumOfExpenses = Expense::getSummaryOfLoggedUserExpenses($user_id, $_GET);
		
		echo json_encode($sumOfExpenses); 
	}
	/**
	*
	*
	*/
	public function incomesTables(){
		$this->user = Auth::getUser();
		
		$user_id = $this->user->id;
		
		$incomes = Income::getLoggedUserIncomes($user_id, $_GET);
		
		echo json_encode($incomes);
	}
	/**
	*
	*
	*/
	public function incomeSummarytable(){
		$this->user = Auth::getUser();
		
		$user_id = $this->user->id;
		
		$incomes = Income::getSummaryOfLoggedUserIncomes($user_id, $_GET);
		
		echo json_encode($incomes);
	}
 }