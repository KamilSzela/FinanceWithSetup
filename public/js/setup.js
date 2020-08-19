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
	loadExpencePaymentWays();
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
	//loadIncomeCathegoriesToDiv();
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
function loadExpencePaymentWays(){
	$.get("/Setup/loadExpencePaymentWays", function(json){
				generatePaymentWaysList(json);								
			});
}
function generatePaymentWaysList(json){
	var jsonObj = $.parseJSON(json);
	$('#expenceMethodDelete').html("");
	if(jsonObj.length == 0){
			$('#expenceMethodDelete').html('<p class="text-center"><b>Brak metod zapłaty wydatku</b></p>');
	} else {				
		$("<form action=\"#\" class=\"form-group\">").appendTo('#expenceMethodDelete');
		
		for(var key in jsonObj){
				var data = jsonObj[key];      
				var id = data[0];
				var userId = data[1];
				var paymentName = data[2];
				
				
				$("<div class=\"form-control bg-light\"><input type=\"radio\" name=\"expenceDelete\" value=\""+id+"\"> "+paymentName+"</div>").appendTo('#expenceMethodDelete');             
			}
	}
		
		$('#setupContainer').css({'height': 'auto'});
		
}
function adjustSetupContainerheight(){
	let windowHeight = $(window).height() - 70;
	let stringHeight = windowHeight.toString() +"px";
	$('#setupContainer').css({'height': stringHeight});	
}