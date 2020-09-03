<?php

namespace App\Models;
use \App\Models\User;
use \App\Flash;
use PDO;
/**
* Income model
* php 7.3.8
*/
class Income extends \Core\Model
{
	/**
	* find income cathegories assigned to called user_id
	* @return mixed - cathegories array if found null otherwise
*/	
	public static function getIncomeCathegories($user_id){
		$sql = "SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :user_id";
		
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

		if(isset($data['incomeAmount'])){
			$incomeAmount = preg_replace("/[^0-9.,]/", "", $data['incomeAmount']);
			
			if($incomeAmount != $data['incomeAmount']||$incomeAmount==""){
				Flash::addMessage('Kwota dochodu powinna zawierać jedynie cyfry i znak "." lub ","', Flash::WARNING);	
				$validData = false;
			}
						
			if($validData==true){
				$dateValue = preg_replace("/[^0-9\-]/","",$data['dateIncome']);
				if($dateValue!=$data['dateIncome']||$dateValue==""){
					$validData = false;
					Flash::addMessage('Proszę wpisać datę w formacie rrrr-mm-dd', Flash::WARNING);
				}
			}

			if(!isset($data['incomeCategory'])){
				$validData = false;
				Flash::addMessage('Proszę wybrać kategorię dochodu', Flash::WARNING);
			}
		} else {
			$validData = false;
		}
		return $validData;
	}
	/**
	* add new income to the database
	*
	*/
	public static function addNewIncome($data, $user){
		
		$incomeAmountCommaReplacement = str_replace(',','.',$data['incomeAmount']);
		$incomeFloatFormat = floatval($incomeAmountCommaReplacement);
		$userId = $user->id;
		$cathegory_assigned_to_user = $data['incomeCategory'];
		$dateValue = $data['dateIncome'];
		
		$db = static::getDB();
		
		$comment = htmlspecialchars($data['commentIncome']);

		$insert_income_query = $db->exec("INSERT INTO incomes VALUES(NULL, '$userId', '$cathegory_assigned_to_user', '$incomeFloatFormat','$dateValue','$comment')");
		
		if($insert_income_query > 0) return true;
		else return false;
	}
	/**
	*	get incomes of logged user form the database
	*	return mixed associative array if found null otherwise
	*/
	public static function getLoggedUserIncomes($user_id, $data){
		$users_Incomes = [];
		$db = static::getDB();
		
		if(isset($data['timePeriod'])){
		$timePeriod = $data['timePeriod'];
		if($timePeriod=='lastMonth'){
			$dayOfMonth = date("d");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			
			$get_incomes_query = $db->query("SELECT i.amount, i.date_of_income, ic.name, i.income_comment FROM `incomes` AS i, `incomes_category_assigned_to_users` AS ic WHERE i.date_of_income > '$beginningOfMonth' AND i.user_id='$user_id' AND i.income_category_assigned_to_user_id = ic.id ORDER BY i.income_category_assigned_to_user_id");
			 
			$users_Incomes = $get_incomes_query->fetchAll();
			
		}
		else if($timePeriod=='previousMonth'){
			$dayOfMonth = date("d");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			$d2 = strtotime($beginningOfMonth."-1 Months");
			$previousMonth = date("Y-m-d",$d2);
						
			$get_incomes_query = $db->query("SELECT i.amount, i.date_of_income, ic.name, i.income_comment FROM `incomes` AS i, `incomes_category_assigned_to_users` AS ic WHERE i.date_of_income >= '$previousMonth' AND i.date_of_income <= '$beginningOfMonth' AND i.user_id='$user_id' AND i.income_category_assigned_to_user_id = ic.id ORDER BY i.income_category_assigned_to_user_id");
			 
			$users_Incomes = $get_incomes_query->fetchAll();
		}
		else if($timePeriod=='lastYear'){
			$dayOfMonth = date("d");
			$month = date("m");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			$d2 = strtotime("- ".$month."Months");
			$beginningOfYear = date("Y-m-d",$d2);
			
			$get_incomes_query = $db->query("SELECT i.amount, i.date_of_income, ic.name, i.income_comment FROM `incomes` AS i, `incomes_category_assigned_to_users` AS ic WHERE i.date_of_income >= '$beginningOfYear' AND i.user_id='$user_id' AND i.income_category_assigned_to_user_id = ic.id ORDER BY i.income_category_assigned_to_user_id");
			 
			$users_Incomes = $get_incomes_query->fetchAll();
		}
		
	}else if(isset($data['beginDate'])){
			$beginningOfTimePeriod = filter_input(INPUT_GET, 'beginDate');
			$endingOfTimePeriod = filter_input(INPUT_GET, 'endDate');
			$d1=strtotime($beginningOfTimePeriod);
			$d2=strtotime($endingOfTimePeriod);
			$diff=$d2-$d1;
			
			if($diff<0){
				Flash::addMessage('Data końca okresu nie moze być mniejsza niż data początku okresu!');
			}
			else{
				$get_incomes_query = $db->query("SELECT i.amount, i.date_of_income, ic.name, i.income_comment FROM `incomes` AS i, `incomes_category_assigned_to_users` AS ic WHERE i.date_of_income >= '$beginningOfTimePeriod' AND i.date_of_income <= '$endingOfTimePeriod' AND i.user_id='$user_id' AND i.income_category_assigned_to_user_id = ic.id ORDER BY i.income_category_assigned_to_user_id");
			 
				$users_Incomes = $get_incomes_query->fetchAll();				
			}
			
	}
		return $users_Incomes;
	}
	/**
	*	get summary of user's incomes
	*	return mixed associative array if found null otherwies
	*/
	public static function getSummaryOfLoggedUserIncomes($user_id, $data){
		$db = static::getDB();
		$incomes_categories = [];
		
		if(isset($data['timePeriod'])){
		$timePeriod = $data['timePeriod'];
		if($timePeriod=='lastMonth'){
			$dayOfMonth = date("d");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			
			$get_incomes_summary_query = $db->query("SELECT SUM(i.amount), ic.name FROM `incomes` AS i, `incomes_category_assigned_to_users` AS ic WHERE i.date_of_income > '$beginningOfMonth' AND i.user_id='$user_id' AND i.income_category_assigned_to_user_id = ic.id GROUP BY i.income_category_assigned_to_user_id");
			
			$incomes_categories = $get_incomes_summary_query->fetchAll();
			
		}
		else if($timePeriod=='previousMonth'){
			$dayOfMonth = date("d");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			$d2 = strtotime($beginningOfMonth."-1 Months");
			$previousMonth = date("Y-m-d",$d2);
						
			$get_incomes_summary_query = $db->query("SELECT SUM(i.amount), ic.name FROM `incomes` AS i, `incomes_category_assigned_to_users` AS ic WHERE i.date_of_income >= '$previousMonth' AND i.date_of_income <= '$beginningOfMonth' AND i.user_id='$user_id' AND i.income_category_assigned_to_user_id = ic.id GROUP BY i.income_category_assigned_to_user_id");
			
			$incomes_categories = $get_incomes_summary_query->fetchAll();
		}
		else if($timePeriod=='lastYear'){
			$dayOfMonth = date("d");
			$month = date("m");
			$d=strtotime("- ".$dayOfMonth."Days");
			$beginningOfMonth = date("Y-m-d", $d);
			$d2 = strtotime("- ".$month."Months");
			$beginningOfYear = date("Y-m-d",$d2);
			
			$get_incomes_summary_query = $db->query("SELECT SUM(i.amount), ic.name FROM `incomes` AS i, `incomes_category_assigned_to_users` AS ic WHERE i.date_of_income >= '$beginningOfYear' AND i.user_id='$user_id' AND i.income_category_assigned_to_user_id = ic.id GROUP BY i.income_category_assigned_to_user_id");
			
			$incomes_categories = $get_incomes_summary_query->fetchAll();
		}
		
	}else if(isset($data['beginDate'])){
			$beginningOfTimePeriod = filter_input(INPUT_GET, 'beginDate');
			$endingOfTimePeriod = filter_input(INPUT_GET, 'endDate');
			$d1=strtotime($beginningOfTimePeriod);
			$d2=strtotime($endingOfTimePeriod);
			$diff=$d2-$d1;
			
			if($diff<0){
				Flash::addMessage('Data końca okresu nie moze być mniejsza niż data początku okresu!');
			}
			else{
				$get_incomes_summary_query = $db->query("SELECT SUM(i.amount), ic.name FROM `incomes` AS i, `incomes_category_assigned_to_users` AS ic WHERE i.date_of_income >= '$beginningOfTimePeriod' AND i.date_of_income <= '$endingOfTimePeriod' AND i.user_id='$user_id' AND i.income_category_assigned_to_user_id = ic.id GROUP BY i.income_category_assigned_to_user_id");
			
				$incomes_categories = $get_incomes_summary_query->fetchAll();
				
			}	
	}
		return $incomes_categories;
	}
	/**
	* add new income category to users data
	* param $user_id the user id, $category - category added by user
	* return boolean
	*/
	public static function addNewCategory($user_id, $category){
		$db = static::getDB();
		if(!static::categoryExists($user_id, $category)){
			$sql = "INSERT INTO incomes_category_assigned_to_users VALUES(NULL, :user_id, :category)";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':category', $category, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			return $stmt->execute();
		} else {
			return false;
		}
	}
	/**
	* check if category already exists in the database
	* param $user_id the user id, $category - category added by user
	* @return boolean
	*/
	public static function categoryExists($user_id, $category){
		$db = static::getDB();
		$sql = "SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :user_id";
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
	public static function removeCategory($categoryID){
			$db = static::getDB();
		
			$sql = "DELETE FROM incomes_category_assigned_to_users WHERE id = :categoryID";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
			return $stmt->execute();
	}
}