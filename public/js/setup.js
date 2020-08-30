$('#listLoginChange').on('click', function(){
	
	$('#loginSetup').removeClass('d-none');
	$('#loginSetup').addClass('d-flex');
	
	adjustSetupContainerheight()
	
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
			$('#expenceMethodDelete').html('<p class="text-center"><b>Brak metod zapłaty wydatku</b></p>');
	} else {				
				
		for(var key in jsonObj){
				var data = jsonObj[key];      
				var id = data[0];
				var userId = data[1];
				var paymentName = data[2];
								
				$("<div class=\"custom-control custom-radio light-input-bg pl-4\"><input type=\"radio\" class=\"custom-control-input\" id=\""+ paymentName +"\" name=\"expenceDelete\" value=\""+id+"\"> <label class=\"custom-control-label\" for=\""+paymentName+"\">"+paymentName+"</label></div>").appendTo('#expenceMethodDelete');             
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
								
				$("<div class=\"custom-control custom-radio light-input-bg pl-4\"><input type=\"radio\" class=\"custom-control-input\" id=\""+ categorie +"\" name=\"expenceDelete\" value=\""+id+"\"> <label class=\"custom-control-label\" for=\""+categorie+"\">"+categorie+"</label></div>").appendTo('#expenceCathegoryDelete');             
			}
	}	
		$('#setupContainer').css({'height': 'auto'});
		
}
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
								
				$("<div class=\"custom-control custom-radio light-input-bg pl-4\"><input type=\"radio\" class=\"custom-control-input\" id=\""+ categorie +"\" name=\"expenceDelete\" value=\""+id+"\"> <label class=\"custom-control-label\" for=\""+categorie+"\">"+categorie+"</label></div>").appendTo('#incomeCathegoryDelete');             
			}
	}	
		
}
function adjustSetupContainerheight(){
	let windowHeight = $(window).height() - 70;
	let stringHeight = windowHeight.toString() +"px";
	$('#setupContainer').css({'height': stringHeight});	
}