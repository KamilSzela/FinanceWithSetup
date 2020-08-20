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
$('#addPaymentWayButton').on('click', function(){
	var newExpencePaymentWay = $('#addExpencePayment').value();
	$.ajax({
		url         : "/Setup/addNewExpencePaymentWay", //gdzie się łączymy
		method      : "post", //typ połączenia, domyślnie get
		dataType    : "boolean", //typ danych jakich oczekujemy w odpowiedzi

		contentType : "application/string", //gdy wysyłamy dane czasami chcemy ustawić ich typ
		data        : { //dane do wysyłki
			name : newExpencePaymentWay
		})
		success: function(response){
			
		}
	});
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