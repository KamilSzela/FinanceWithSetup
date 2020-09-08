<?php

 namespace App\Controllers;
 
 use \Core\View;
 use \App\Auth;
 use \App\Flash;
 use \App\Models\Income;
 /**
 * Incomes controllers
 * php 7.3.8
 */
 
 class Incomes extends Authenticated
 {
	 /**
	 * Before filter - called each action method
	 * @return void
	 */
	 protected function before(){
		 parent::before();
		 $this->user = Auth::getUser();
		 $this->cathegories = Income::getIncomeCathegories($this->user->id);
	 }
	 /**
	 * show the incomes start site
	 * @return void
	 */
	 public function showAction(){
		
		 View::renderTemplate('Incomes/show.html', [
			'user' => $this->user,
			'income_cathegories' => $this->cathegories
		 ]);
	 }
	public function addAction(){		
		if(Income::validateInputs($_POST)){			
				if(Income::addNewIncome($_POST, $this->user)){
					Flash::addMessage('Dodano nowy dochód do Twojej bazy danych!');
					View::renderTemplate('Incomes/show.html', [
						'income_cathegories' => $this->cathegories				
					]);
				}			 
			
		} else {			
			View::renderTemplate('Incomes/show.html', [
			'data' => $_POST,
			'income_cathegories' => $this->cathegories
		 ]);
		}
	}
	/**
	* remove income data from the database
	*
	*/
	public function removeIncomeFromDatabase(){
		if(isset($_POST['deleteId'])){
			$expenseID = $_POST['deleteId'];
			if(Income::removeIncome($expenseID)){
				echo true;
			} else {
				echo false;
			}	
		} else {
			echo false;
		} 
	}
 }