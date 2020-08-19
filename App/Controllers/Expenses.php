<?php

 namespace App\Controllers;
 
 use \Core\View;
 use \App\Auth;
 use \App\Flash;
 use \App\Models\Expense;
 /**
 * Expences controllers
 * php 7.3.8
 */
 
 class Expenses extends Authenticated
 {
	 /**
	 * Before filter - called each action method
	 * @return void
	 */
	 protected function before(){
		 parent::before();
		 $this->user = Auth::getUser();
		 $this->cathegories = Expense::getExpenceCathegories($this->user->id);
		 $this->paymentWays = Expense::getExpencePaymentWays($this->user->id);
	 }
	 /**
	 * show the incomes start site
	 * @return void
	 */
	 public function showAction(){		
		
		 View::renderTemplate('Expenses/show.html', [
			'user' => $this->user,
			'expense_cathegories' => $this->cathegories,
			'expense_payment_ways' => $this->paymentWays
		 ]);
	 }	 
	public function addAction(){				
		if(Expense::validateInputs($_POST)){			
				if(Expense::addNewExpense($_POST, $this->user)){
					Flash::addMessage('Dodano nowy wydatek do Twojej bazy danych!');
						View::renderTemplate('Expenses/show.html', [
						'expense_cathegories' => $this->cathegories,
						'expense_payment_ways' => $this->paymentWays
					 ]);		
				}
		} else {			
			View::renderTemplate('Expenses/show.html', [
			'data' => $_POST,
			'expense_cathegories' => $this->cathegories,
			'expense_payment_ways' => $this->paymentWays
		 ]);

		}
	}	
 }