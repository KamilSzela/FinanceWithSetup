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
	/**
	* add new expese tot he database
	* return string or generate view
	*/
	public function addExpense(){
		$data = $_POST['data'];
		if(Expense::validateInputs($data)){			
			$this->user = Auth::getUser();		
			$user_id = $this->user->id;	
			
			if(Expense::addNewExpense($data, $this->user)){
				echo ("<p class=\"text-success text-center light-input-bg\"><b>Dodano nowy wydatek do bazy danych</b></p>");
			} else {
				echo ("<p class=\"text-danger text-center light-input-bg\"><b>Wystąpił błąd podczas dodawania nowego wysatku</b></p>");
			}
			
		} else {						
			View::renderTemplate('Expenses/show.html', [
			'data' => $_POST,
			'expense_cathegories' => $this->cathegories,
			'expense_payment_ways' => $this->paymentWays
		 ]);
		}
	}
	/**
	* check if adding expense is over limit
	* return array
	*/
	public function checkLimitOfLastMonth(){	
		$catId = $_POST['categorie'];
		$newExpenseValue = floatval($_POST['expenseAmount']);
		$categoryData = Expense::getLimitOfCategorie($catId);
		$dataArray = $categoryData[0];
		$limit = floatval($dataArray['category_limit']);
		$expiryDate = $dataArray['limit_expiry'];
		//echo var_dump($data);
		$expenseOfCategoryFromThisMonth = Expense::getExpensesOfCategory($catId);
		//$expenseData = $expenseOfCategoryFromThisMonth[0];
		//echo var_dump($expensedata);
		$aggregatedData=[];
		$aggregatedData['new_expense'] = round(floatval($newExpenseValue),2);
		if($limit == NULL){
			$aggregatedData['limit'] = false;
			echo json_encode($aggregatedData);
		} else {
			$currentDate = date('YY-mm-dd');
			if($currentDate > $expiryDate && $expiryDate != NULL){ 
			// check if limit is greater than expense values
				$sumOfExpenses = floatval($newExpenseValue);
				foreach($expenseOfCategoryFromThisMonth as $expense){
					//add values of expenses from this month
					$sumOfExpenses += floatval($expense['amount']);					
				}
				if($sumOfExpenses > $limit){
					// expenses over limit
					$aggregatedData['limit'] = round($limit,2);
					$aggregatedData['expense_sum'] = round($sumOfExpenses-$newExpenseValue, 2);
					$aggregatedData['difference'] = round($sumOfExpenses - $limit,2);
					$aggregatedData['overLimit'] = true;
					echo json_encode($aggregatedData);
				} else {
					// expenses below limit
					$aggregatedData['limit'] = round($limit,2);
					$aggregatedData['expense_sum'] = round($sumOfExpenses-$newExpenseValue,2);
					$aggregatedData['difference'] = round($limit - $sumOfExpenses,2);
					$aggregatedData['overLimit'] = false;
					echo json_encode($aggregatedData);
				}
			} else { 
				// limit expired
				$aggregatedData['limit'] = false;
				echo json_encode($aggregatedData);
			}
		}
		
	}
	/**
	* remove expense data from the database
	*
	*/
	public function removeExpenseFromDatabase(){
		if(isset($_POST['deleteId'])){
			$expenseID = $_POST['deleteId'];
			if(Expense::removeExpense($expenseID)){
				echo true;
			} else {
				echo false;
			}	
		} else {
			echo false;
		} 
	}
}