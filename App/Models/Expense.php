<?php

namespace App\Models;
use \App\Models\User;
use \App\Flash;
use PDO;
/**
* Expense model
* php 7.3.8
*/
class Expense extends \Core\Model
{
	/**
	* find income cathegories assigned to called user_id
	* @return mixed - cathegories array if found null otherwise
*/	
	public static function getExpenceCathegories($user_id){
		$sql = "SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :user_id";
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		
		$stmt->execute();
		$categoriesData = $stmt->fetchAll();
		//check if limit is expired - if is, remove it from database
		$currentDate = date('Y-m-d');
		foreach($categoriesData as $row){
			if($row['limit_expiry'] !== NULL){
				if($row['limit_expiry'] < $currentDate){
					static::removeCategoryLimit($row['id']);
					$row['category_limit'] = NULL;
					$row['limit_expiry'] = NULL;
				}
			}
		}		
		return $categoriesData;
	}
	/**
	* find payment ways from database assigned to called user id
	* @return mixed - payment array if found null otherwise
	*/
	public static function getExpencePaymentWays($user_id){
		$sql = "SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :user_id";
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		
		$stmt->execute();
		return $stmt->fetchAll();
	}
	/**
	* validate inputs from form
	* return boolean
	*/
	public static function validateInputs($data){
		$validData = true;

		if(isset($data['expenceAmount'])){
			$expenceAmount = preg_replace("/[^0-9.,]/", "", $data['expenceAmount']);
			
			if($expenceAmount != $data['expenceAmount']||$expenceAmount==""){
				Flash::addMessage('Kwota dochodu powinna zawierać jedynie cyfry i znak "." lub ","', Flash::WARNING);	
				$validData = false;
			}
						
			if($validData==true){
				$dateValue = preg_replace("/[^0-9\-]/","",$data['dateExpence']);
				if($dateValue!=$data['dateExpence']||$dateValue==""){
					$validData = false;
					Flash::addMessage('Proszę wpisać datę w formacie rrrr-mm-dd', Flash::WARNING);
				}
			}
			
			if(!isset($data['payment'])){
				$validData = false;
				Flash::addMessage('Proszę podać sposób płatności', Flash::WARNING);
			}
		
			if(!isset($data['expenceCat'])){
				$validData = false;
				Flash::addMessage('Proszę wybrać kategorię dodawanego wydatku', Flash::WARNING);
			}
		} else {
			$validData = false;
		}
		return $validData;
	}
	/**
	* add new expense to the database
	* @return void
	*/
	public static function addNewExpense($data, $user){
		$expenceAmountCommaReplacement = str_replace(',','.',$data['expenceAmount']);
		$expenceFloatFormat = floatval($expenceAmountCommaReplacement);
		$userId = $user->id;
		$cathegory_assigned_to_user = $data['expenceCat'];
		$paymentWay = $data['payment'];
		$dateValue = $data['dateExpence'];
		
		$db = static::getDB();
		
		$comment = htmlspecialchars($data['commentExpence']);

		$insert_expence_query = $db->exec("INSERT INTO expenses VALUES(NULL, '$userId', '$cathegory_assigned_to_user', '$paymentWay', '$expenceFloatFormat','$dateValue','$comment')");
		
		if($insert_expence_query > 0) return true;
		else return false;
	}
	/**
	*	get expenses of logged user
	* @return mixed associative array if found null otherwise 
	*/
	public static function getLoggedUserExpenses($user_id, $data){
		
	$db = static::getDB();
	$users_Expenses = [];
	if(isset($data['timePeriod'])){
		$timePeriod = $data['timePeriod'];
		
		if($timePeriod=='lastMonth'){
			$dayOfMonth = date("d");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			
			$get_expences_query = $db->query("SELECT e.id, e.amount, e.date_of_expence, ec.name, pm.name, e.expence_comment FROM `expenses` AS e, `expenses_category_assigned_to_users` AS ec, `payment_methods_assigned_to_users` AS pm WHERE e.date_of_expence > '$beginningOfMonth' AND e.user_id='$user_id' AND e.expence_category_assigned_to_user_id = ec.id AND e.payment_method_assigned_to_user_id = pm.id ORDER BY e.expence_category_assigned_to_user_id");
			 
			$users_Expenses = $get_expences_query->fetchAll();
			
		}
		else if($timePeriod=='previousMonth'){
			$dayOfMonth = date("d");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			$d2 = strtotime($beginningOfMonth."-1 Months");
			$previousMonth = date("Y-m-d",$d2);
						
			$get_expences_query = $db->query("SELECT e.id, e.amount, e.date_of_expence, ec.name, pm.name, e.expence_comment FROM `expenses` AS e, `expenses_category_assigned_to_users` AS ec, `payment_methods_assigned_to_users` AS pm WHERE e.date_of_expence >= '$previousMonth' AND e.date_of_expence <= '$beginningOfMonth' AND e.user_id='$user_id' AND e.expence_category_assigned_to_user_id = ec.id AND e.payment_method_assigned_to_user_id = pm.id ORDER BY e.expence_category_assigned_to_user_id");
			
			$users_Expenses = $get_expences_query->fetchAll();
		}
		else if($timePeriod=='lastYear'){
			$dayOfMonth = date("d");
			$month = date("m");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			$d2 = strtotime("- ".$month."Months");
			$beginningOfYear = date("Y-m-d",$d2);
			
			$get_expences_query = $db->query("SELECT e.id, e.amount, e.date_of_expence, ec.name, pm.name, e.expence_comment FROM `expenses` AS e, `expenses_category_assigned_to_users` AS ec, `payment_methods_assigned_to_users` AS pm WHERE e.date_of_expence >= '$beginningOfYear' AND e.user_id='$user_id' AND e.expence_category_assigned_to_user_id = ec.id AND e.payment_method_assigned_to_user_id = pm.id ORDER BY e.expence_category_assigned_to_user_id");
			
			$users_Expenses = $get_expences_query->fetchAll();
		}
		
	} else if(isset($data['beginDate'])){
			$beginningOfTimePeriod = filter_input(INPUT_GET, 'beginDate');
			$endingOfTimePeriod = filter_input(INPUT_GET, 'endDate');
			$d1=strtotime($beginningOfTimePeriod);
			$d2=strtotime($endingOfTimePeriod);
			$diff=$d2-$d1;
			
			if($diff<0){
				$_SESSION['dateMessage'] = '<p class="text-danger">Data końca okresu nie moze być mniejsza niż data początku okresu!</p>';
			}
			else{
				$get_expences_query = $db->query("SELECT e.id, e.amount, e.date_of_expence, ec.name, pm.name, e.expence_comment FROM `expenses` AS e, `expenses_category_assigned_to_users` AS ec, `payment_methods_assigned_to_users` AS pm WHERE e.date_of_expence >= '$beginningOfTimePeriod' AND e.date_of_expence <= '$endingOfTimePeriod' AND e.user_id='$user_id' AND e.expence_category_assigned_to_user_id = ec.id AND e.payment_method_assigned_to_user_id = pm.id ORDER BY e.expence_category_assigned_to_user_id");
				
				$users_Expenses = $get_expences_query->fetchAll();				
			}
			
	}
		return $users_Expenses;
	}
	/**
	*
	*
	*/
	public static function getSummaryOfLoggedUserExpenses($user_id, $data){
		$db = static::getDB();
		$expenses_categories = [];
		
		if(isset($data['timePeriod'])){
			$timePeriod = $data['timePeriod'];
		
			if($timePeriod=='lastMonth'){
				$dayOfMonth = date("d");
				$d=strtotime("- ".$dayOfMonth."Days");
				$beginningOfMonth = date("Y-m-d", $d);
				
				$get_summary_query = $db->query("SELECT SUM(e.amount), ec.name FROM `expenses` AS e, `expenses_category_assigned_to_users` AS ec WHERE e.date_of_expence > '$beginningOfMonth' AND e.user_id='$user_id' AND e.expence_category_assigned_to_user_id = ec.id GROUP BY e.expence_category_assigned_to_user_id");
				
				$expenses_categories = $get_summary_query->fetchAll();
				
			}
			else if($timePeriod=='previousMonth'){
				$dayOfMonth = date("d");
				$d=strtotime("- ".$dayOfMonth."Days");
				$beginningOfMonth = date("Y-m-d", $d);
				$d2 = strtotime($beginningOfMonth."-1 Months");
				$previousMonth = date("Y-m-d",$d2);
							
				$get_summary_query = $db->query("SELECT SUM(e.amount), ec.name FROM `expenses` AS e, `expenses_category_assigned_to_users` AS ec WHERE e.date_of_expence >= '$previousMonth' AND e.date_of_expence <= '$beginningOfMonth' AND e.user_id='$user_id' AND e.expence_category_assigned_to_user_id = ec.id GROUP BY e.expence_category_assigned_to_user_id");
				
				$expenses_categories = $get_summary_query->fetchAll();
			}
			else if($timePeriod=='lastYear'){
				$dayOfMonth = date("d");
				$month = date("m");
				$d=strtotime("- ".$dayOfMonth."Days");
				$beginningOfMonth = date("Y-m-d", $d);
				$d2 = strtotime("- ".$month."Months");
				$beginningOfYear = date("Y-m-d",$d2);
				
				$get_summary_query = $db->query("SELECT SUM(e.amount), ec.name FROM `expenses` AS e, `expenses_category_assigned_to_users` AS ec WHERE e.date_of_expence >= '$beginningOfYear' AND e.user_id='$user_id' AND e.expence_category_assigned_to_user_id = ec.id GROUP BY e.expence_category_assigned_to_user_id");
				
				$expenses_categories = $get_summary_query->fetchAll();
			}
		
		}else if(isset($data['beginDate'])){
				$beginningOfTimePeriod = filter_input(INPUT_GET, 'beginDate');
				$endingOfTimePeriod = filter_input(INPUT_GET, 'endDate');
				$d1=strtotime($beginningOfTimePeriod);
				$d2=strtotime($endingOfTimePeriod);
				$diff=$d2-$d1;
				
				if($diff<0){
					$_SESSION['dateMessage'] = '<p class="text-danger">Data końca okresu nie moze być mniejsza niż data początku okresu!</p>';
				}
				else{
					$get_summary_query = $db->query("SELECT SUM(e.amount), ec.name FROM `expenses` AS e, `expenses_category_assigned_to_users` AS ec WHERE e.date_of_expence >= '$beginningOfTimePeriod' AND e.date_of_expence <= '$endingOfTimePeriod' AND e.user_id='$user_id' AND e.expence_category_assigned_to_user_id = ec.id GROUP BY e.expence_category_assigned_to_user_id");
					
					$expenses_categories = $get_summary_query->fetchAll();
					
				}
				
		}
		return $expenses_categories;
	}
	/**
	* add new payment way to the database 
	* @param $usr_id - user id $newPaymentWay - payment way added by user
	* @return boolean
	*/
	public static function addNewPaymentWay($user_id, $newPaymentWay){		
			if(static::paymentExists($user_id,$newPaymentWay) == false){
				$db = static::getDB();
				$sql = "INSERT INTO payment_methods_assigned_to_users VALUES(NULL, :user_id, :name)";
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':name', $newPaymentWay, PDO::PARAM_STR);
				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				return $stmt->execute();			
			} else {
				return false;
			}				
	}
	/**
	* check if payment way assigned to the user exists in the database 
	* @param $usr_id - user id $newPaymentWay - payment way added by user
	* @return boolean
	*/
	public static function paymentExists($user_id,$newPaymentWay){
		$db = static::getDB();
		$sql = "SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :user_id";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		if($stmt->rowCount()>0){
			$results = $stmt->fetchAll();
			foreach($results as $result){
				if($result['name'] == $newPaymentWay){
					return true;
				}
			}
		}
		return false;
	}
	/**
	* remove new payment way from the database 
	* @param $usr_id - user id $paymentWayID - payment way id to remove
	* @return boolean
	*/
	public static function removePaymentWay($user_id, $paymentWayID){
			$db = static::getDB();
			
			$sql = "DELETE FROM payment_methods_assigned_to_users WHERE id = :paymentWayID";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':paymentWayID', $paymentWayID, PDO::PARAM_INT);
			return $stmt->execute();			
	}
	/**
	* add new category to the database 
	* @param $usr_id - user id $category - category way added by user
	* @return boolean
	*/
	public static function addNewCategory($user_id, $category){
		if(!static::checkIfCategoryAlreadyExists($user_id, $category)){
			$db = static::getDB();
		
			$sql = "INSERT INTO expenses_category_assigned_to_users VALUES(NULL, :user_id, :category, NULL, NULL)";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':category', $category, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			
			return $stmt->execute();
		} else {
			return false;
		}
	}
	/**
	* check if category assigned to the user exists in the database 
	* @param $usr_id - user id $category - category added by user
	* @return boolean
	*/
	public static function checkIfCategoryAlreadyExists($user_id, $category){
		$db = static::getDB();
		$sql = "SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :user_id";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		if($stmt->rowCount()>0){
			$results = $stmt->fetchAll();
			foreach($results as $result){
				if($result['name'] == $category){
					return true;
				}
			}
		}
		return false;
	}
	/**
	* remove category of expense from the database 
	* @param $usr_id - user id $categoryID - id of category to delete
	* @return boolean
	*/
	public static function removeCategory($user_id, $categoryID){
			$db = static::getDB();
		
			$sql = "DELETE FROM expenses_category_assigned_to_users WHERE id = :categoryID";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
			return $stmt->execute();
	}
	public static function setExpenseLimit($catID, $limit){
			$db = static::getDB();
		
			$dayOfMonth = date("d");
			$month = date("m");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			$d2 = strtotime($beginningOfMonth . "+1Months");
			$beginningOfNextMonth = date("Y-m-d",$d2);
		
			$sql = "DELETE FROM expenses_category_assigned_to_users WHERE id = :categoryID";
			$sql = 'UPDATE expenses_category_assigned_to_users 
			SET category_limit = :limit, limit_expiry = :date 
			WHERE id = :id';
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':limit', $limit, PDO::PARAM_STR);
			$stmt->bindValue(':date', $beginningOfNextMonth, PDO::PARAM_STR);
			$stmt->bindValue(':id', $catID, PDO::PARAM_INT);
			
			return $stmt->execute();
	}
	public static function removeCategoryLimit($categoryID){
		$sql = 'UPDATE expenses_category_assigned_to_users 
				SET category_limit = NULL, 
				limit_expiry = NULL
				WHERE id = :category_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category_id', $categoryID, PDO::PARAM_INT);
		return $stmt->execute();
	}
	public static function getLimitOfCategorie($data){
		$sql = $sql = "SELECT * FROM expenses_category_assigned_to_users WHERE id = :category_id";
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category_id', $data, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	public static function getExpensesOfCategory($data){
		$dayOfMonth = date("d");
		$d=strtotime("- ".$dayOfMonth."Days");
		$beginningOfMonth = date("Y-m-d", $d);
				
		$sql = $sql = "SELECT * FROM expenses WHERE expence_category_assigned_to_user_id = :category_id AND date_of_expence > :beginningOfMonth";
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category_id', $data, PDO::PARAM_INT);
		$stmt->bindValue(':beginningOfMonth', $beginningOfMonth, PDO::PARAM_STR);
		
		$stmt->execute();
		return $stmt->fetchAll();
	}
	/**
	* remove expense from expenses table
	*/
	public static function removeExpense($expenseID){
		$db = static::getDB();
	
		$sql = "DELETE FROM expenses WHERE id = :expenseID";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':expenseID', $expenseID, PDO::PARAM_INT);
		return $stmt->execute();
	}
}