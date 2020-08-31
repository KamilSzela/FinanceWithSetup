$(document).ready(function(){
$('#listLoginChange').on('click', function(){
	
	$('#loginSetup').removeClass('d-none');
	$('#loginSetup').addClass('d-flex');
	
	adjustSetupContainerheight();
	
	$('#expenceMenuSetup').removeClass('d-flex');
	$('#expenceMenuSetup').addClass('d-none');
	
	$('#incomeMenuSetup').removeClass('d-flex');
	$('#incomeMenuSetup').addClass('d-none');
	
	$('#lastInputsMenuSetup').removeClass('d-flex');
	$('#lastInputsMenuSetup').addClass('d-none');
	
	$("#functionMessage-loginSetup").html("");
});
$('#listExpenceChange').on('click', function(){
	$('#loginSetup').removeClass('d-flex');
	$('#loginSetup').addClass('d-none');
	
	$('#expenceMenuSetup').removeClass('d-none');
	$('#expenceMenuSetup').addClass('d-flex');	
	
	$('#incomeMenuSetup').removeClass('d-flex');
	$('#incomeMenuSetup').addClass('d-none');
	
	$('#lastInputsMenuSetup').removeClass('d-flex');
	$('#lastInputsMenuSetup').addClass('d-none');
	
	$("#functionMessage-loginSetup").html("");
	loadExpenceAttribiutesLists();
});
$('#listIncomeChange').on('click', function(){
	$('#loginSetup').removeClass('d-flex');
	$('#loginSetup').addClass('d-none');
	
	$('#expenceMenuSetup').removeClass('d-flex');
	$('#expenceMenuSetup').addClass('d-none');
	
	$('#incomeMenuSetup').removeClass('d-none');
	$('#incomeMenuSetup').addClass('d-flex');
	
	$('#lastInputsMenuSetup').removeClass('d-flex');
	$('#lastInputsMenuSetup').addClass('d-none');
	
	$("#functionMessage-loginSetup").html("");
	adjustSetupContainerheight();
	loadIncomeCathegoriesToDiv();
});
$('#listLastInputsDelete').on('click', function(){
	$('#loginSetup').removeClass('d-flex');
	$('#loginSetup').addClass('d-none');
	
	$('#expenceMenuSetup').removeClass('d-flex');
	$('#expenceMenuSetup').addClass('d-none');
	
	$('#incomeMenuSetup').removeClass('d-flex');
	$('#incomeMenuSetup').addClass('d-none');
	
	$('#lastInputsMenuSetup').removeClass('d-none');
	$('#lastInputsMenuSetup').addClass('d-flex');
	
	$("#functionMessage-loginSetup").html("");
	
	adjustSetupContainerheight();
});
$('#changeLoginButton').on('click', function(){
	
	const newChangeLogin = $('#loginChange').val();
	if(newChangeLogin != ""){
		$.post("/Setup/changeLogin", {newLogin: newChangeLogin}, function(data){			
			$('#functionMessage-loginSetup').html(data);
			$('#loginChange').val("");
		});
	} else {
		$('#functionMessage-loginSetup').html("<p class=\"text-danger light-input-bg\"><b>Nie wpisano nowej nazwy konta</b></p>");
	}
});
$('#changeEmailButton').on('click', function(){
	
	const newChangeEmail = $('#emailChange').val();
	if(newChangeEmail != ""){
		$.post("/Setup/changeEmail", {newEmail: newChangeEmail}, function(data){			
			$('#functionMessage-loginSetup').html(data);
			$('#emailChange').val("");
		});
	} else {
		$('#functionMessage-loginSetup').html("<p class=\"text-danger light-input-bg\"><b>Nie wpisano nowego adresu Email</b></p>");
	}
});
$('#changePasswordButton').on('click', function(){
	
	var newChangePassword = $('#passwordChange').val();
	if(newChangePassword.length < 6){
		$('#functionMessage-loginSetup').html("<p class=\"text-danger light-input-bg\"><b>Hasło musi skladać się z co najmniej 6 znaków</b></p>");
	} else {
		var pattern = /[A-Za-z]+\d+/;
		if(pattern.test(newChangePassword)){			
				$.post("/Setup/changePassword", {newPassword: newChangePassword}, function(data){			
					$('#functionMessage-loginSetup').html(data);
					$('#passwordChange').val("");
				});			
		} else {
			$('#functionMessage-loginSetup').html("<p class=\"text-danger light-input-bg\"><b>Hasło musi zawierać co najmniej jedną literę i jedną cyfrę</b></p>");
		}
	}
});
$('#addPaymentWayButton').on('click', function(){
	var newExpencePaymentWay = $('#addExpencePayment').val();
	if(newExpencePaymentWay != ""){
		newExpencePaymentWay = newExpencePaymentWay.substr(0,1).toUpperCase() + newExpencePaymentWay.substr(1).toLowerCase();
		$.post("/Setup/addNewExpencePaymentWay", {newPaymentWay: newExpencePaymentWay}, function(data){
			loadExpenceAttribiutesLists();
			$('#expenceMenuMethodInfo').html(data);
			$('#addExpencePayment').val("");
		});
	} else {
		$('#expenceMenuMethodInfo').html("<p class=\"text-danger light-input-bg\"><b>Nie wpisano nowej metody płatności</b></p>");
	}
});
$('#deleteMethodButton').on('click', function(){
	var methodToDelete = $("input[name='expencePaymentWayDelete']:checked").val();
	if(methodToDelete){
		$.post("/Setup/removeExpencePaymentWay", {toDelete: methodToDelete}, function(data){
			loadExpenceAttribiutesLists();
			$('#expenceMenuMethodInfo').html(data);
		});
	} else {
		$('#expenceMenuMethodInfo').html("<p class=\"text-danger light-input-bg\"><b>Nie zaznaczono metody do usuniecia </b></p>");
	}
});
$('#addExpenceCathegoryButton').on('click', function(){
	var newExpenseCategory = $('#addExpenceCathegory').val();
	if(newExpenseCategory != ""){
		newExpenseCategory = newExpenseCategory.substr(0,1).toUpperCase() + newExpenseCategory.substr(1).toLowerCase();
		$.post("/Setup/addNewExpenseCategory", {newExpenseCat: newExpenseCategory}, function(data){
			loadExpenceAttribiutesLists()
			$('#expenceMenuCatInfo').html(data);
			$('#addExpenceCathegory').val("");
		});
	} else {
		$('#expenceMenuCatInfo').html("<p class=\"text-danger light-input-bg\"><b>Nie wpisano nowej kategorii wydatku</b></p>");
	}
});
$('#deleteExpenceCathegoryButton').on('click', function(){
	var categoryToDelete = $("input[name='expenceCategoryListDelete']:checked").val();
	if(categoryToDelete){
		$.post("/Setup/removeExpenceCategory", {toDelete: categoryToDelete}, function(data){
			loadExpenceAttribiutesLists();
			$('#expenceMenuCatInfo').html(data);
		});
	} else {
		$('#expenceMenuCatInfo').html("<p class=\"text-danger light-input-bg\"><b>Nie zaznaczono kategorii do usuniecia </b></p>");
	}
});
function loadExpenceAttribiutesLists(){
	$.get("/Setup/loadExpencePaymentWays", function(json){
				generatePaymentWaysList(json);								
			});
	$.get("/Setup/loadExpenceCathegories", function(json){
				generateExpenceCathegoriesList(json);			
			});
}
function generatePaymentWaysList(json){
	var jsonObj = $.parseJSON(json);
	$('#expenceMethodDelete').html("");
	if(jsonObj.length == 0){
			$('#expenceMethodDelete').html('<p class="text-center light-input-bg"><b>Brak metod zapłaty wydatku</b></p>');
	} else {				
				
		for(var key in jsonObj){
				var data = jsonObj[key];      
				var id = data[0];
				var userId = data[1];
				var paymentName = data[2];
								
				$("<div class=\"custom-control custom-radio light-input-bg pl-4\"><input type=\"radio\" class=\"custom-control-input\" id=\""+ paymentName +"\" name=\"expencePaymentWayDelete\" value=\""+id+"\"> <label class=\"custom-control-label\" for=\""+paymentName+"\">"+paymentName+"</label></div>").appendTo('#expenceMethodDelete');             
			}
	}
	
		$('#setupContainer').css({'height': 'auto'});
		
}
function generateExpenceCathegoriesList(json){
	var jsonObj = $.parseJSON(json);
	$('#expenceCathegoryDelete').html("");
	if(jsonObj.length == 0){
			$('#expenceCathegoryDelete').html('<p class="text-center"><b>Brak aktualnych kategorii wydatku</b></p>');
	} else {				
				
		for(var key in jsonObj){
				var data = jsonObj[key];      
				var id = data[0];
				var userId = data[1];
				var categorie = data[2];
								
				$("<div class=\"custom-control custom-radio light-input-bg pl-4\"><input type=\"radio\" class=\"custom-control-input\" id=\""+ categorie +"\" name=\"expenceCategoryListDelete\" value=\""+id+"\"> <label class=\"custom-control-label\" for=\""+categorie+"\">"+categorie+"</label></div>").appendTo('#expenceCathegoryDelete');             
			}
	}	
		$('#setupContainer').css({'height': 'auto'});
		
}

$('#addIncomeCathegoryButton').on('click', function(){
	var newIncomeCategory = $('#addIncomeCathegoryTextInput').val();
	if(newIncomeCategory != ""){
		newIncomeCategory = newIncomeCategory.substr(0,1).toUpperCase() + newIncomeCategory.substr(1).toLowerCase();
		$.post("/Setup/addNewIncomeCategory", {newIncomeCat: newIncomeCategory}, function(data){
			loadIncomeCathegoriesToDiv()
			$('#incomeMenuMethodInfo').html(data);
			$('#addIncomeCathegoryTextInput').val("");
		});
	} else {
		$('#incomeMenuMethodInfo').html("<p class=\"text-danger light-input-bg\"><b>Nie wpisano nowej metody płatności</b></p>");
	}
});
$('#deleteIncomeCathegoryButton').on('click', function(){
	var incomeCathegoryToDelete = $("input[name='incomeCatDelete']:checked").val();
	if(incomeCathegoryToDelete){
		$.post("/Setup/removeIncomeCategory", {toDelete: incomeCathegoryToDelete}, function(data){
			loadIncomeCathegoriesToDiv();
			$('#incomeMenuMethodInfo').html(data);
		});
	} else {
		$('#incomeMenuMethodInfo').html("<p class=\"text-danger light-input-bg\"><b>Nie zaznaczono kategorii do usuniecia </b></p>");
	}
});
function loadIncomeCathegoriesToDiv(){
	$.get("/Setup/loadIncomeCathegories", function(json){
			generateIncomeCathegoriesList(json);			
		});
}
function generateIncomeCathegoriesList(json){
	var jsonObj = $.parseJSON(json);
	$('#incomeCathegoryDelete').html("");
	if(jsonObj.length == 0){
			$('#incomeCathegoryDelete').html('<p class="text-center"><b>Brak aktualnych kategorii dochodu</b></p>');
	} else {				
				
		for(var key in jsonObj){
				var data = jsonObj[key];      
				var id = data[0];
				var userId = data[1];
				var categorie = data[2];
								
				$("<div class=\"custom-control custom-radio light-input-bg pl-4\"><input type=\"radio\" class=\"custom-control-input\" id=\""+ categorie +"\" name=\"incomeCatDelete\" value=\""+id+"\"> <label class=\"custom-control-label\" for=\""+categorie+"\">"+categorie+"</label></div>").appendTo('#incomeCathegoryDelete');             
			}
	}	
		
}
$('#setLimitExpenceCathegoryButton').on('click', function(){
	$.get("/Setup/loadExpenceCathegories", function(json){
			loadUserCategoriesToSelectList(json);			
		});
	
});
function loadUserCategoriesToSelectList(json){
	var jsonObj = $.parseJSON(json);
	$('#limit_message').html("");
	$('#selectExpenseOption').html("");
	if(jsonObj.length == 0){
			$('#limit_message').html('<p class="text-center"><b>Brak aktualnych kategorii wydatku</b></p>');
	} else {				
		var string = "<div class=\"input-group-prepend\"> <label class=\"input-group-text\" for=\"selectExpense\">Wybierz kategorię:</label></div><select class=\"custom-select\" id=\"selectExpense\">";

		for(var key in jsonObj){
				var data = jsonObj[key];      
				var id = data[0];
				var userId = data[1];
				var categorie = data[2];
				string += "<option value="+ id +">"+categorie+"</option>";				             
			}
			string += "</select>";
			$(string).appendTo('#selectExpenseOption');
	}			
};
$('#sendLimitButton').on('click', function(){
	var limit = $('#limitAmount').val();
	if(limit != ""){
			
		$('#limit_message').html("");
		var pattern = /[^\d,.]+/;
		
		if(pattern.test(limit)){
			$('#limit_message').html('<p class="text-center text-danger"><b>Kwota powinna się składać jedynie z cyfr i przecinka</b></p>');
		} else {
			$('#limit_message').html('<p class="text-center"><b>Poprawne dane</b></p>');
		}
	} else {
		$('#limit_message').html('<p class="text-center"><b>Nie wpisano kwoty ustawianego limitu</b></p>');
	}
});
function adjustSetupContainerheight(){
	let windowHeight = $(window).height() - 70;
	let stringHeight = windowHeight.toString() +"px";
	$('#setupContainer').css({'height': stringHeight});	
}
});