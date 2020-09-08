$(document).ready(function(){
	/*
	$('#test').on('click', function(){
		var cat = $('input[name="expenceCat"]:checked');
		
		console.log(cat[0].id);
		alert(cat);
	});
	
	$("input[name='expenseCat']").change(
    function(){
        if (this.checked && this.value != '') {
			var amount = $('input[name="expenceAmount"]').val();
			alert(amount);
        }
    });
	*/
	$('#addExpenceButton').on('click', function(){
		if(checkInputs()){
			var amount = $('#amount').val();
			let categorieVal = $("input[name='expenceCat']:checked").val();
			$.post("/Expenses/checkLimitOfLastMonth", {categorie: categorieVal, expenseAmount: amount}, function(response){		
				if(response != 0){
					$('#delete_limit_message').html('<p class="text-center text-danger light-input-bg"><b>Przekroczono limit na kategorię o '+response+'zł</b></p>');
					$('#confirm_modal_over_limit').modal('show');
					$('#addExpenseOffLimitButton').on('click', function(){
						addNewExpenseToTheDatabase();
						$('#confirm_modal_over_limit').modal('hide');
					});
				} else {
					addNewExpenseToTheDatabase();
				}					
			});	
		} 
	});
	function addNewExpenseToTheDatabase(){
		var amount = $('#amount').val();
		var date = $('#expenseDate').val();
		var paymentWay = $("input[name='payment']:checked").val();
		var categorie = $("input[name='expenceCat']:checked").val();
		var comment = $('#commentExpense').val();
		var array = {
			expenceAmount: amount,
			dateExpence: date,
			payment: paymentWay,
			expenceCat: categorie,
			commentExpence: comment
		}
		$.post("/Expenses/addExpense", {data: array}, function(response){
			if(response){
				$('#expenseMessageDiv').html(response);
			} else {
				$('#expenseMessageDiv').html('<p class="text-center text-danger light-input-bg"><b>Wystapił błąd podczas dodawania wydatku do bazy danych</b></p>');
			}
		});
	};
	function checkInputs(){
		var validInputs = true;
		var amount = $('#amount').val();
		// check amount value
		if(amount=="") {
			$('#expenseMessageDiv').html('<p class="text-center text-danger light-input-bg"><b>Nie wprowadzono wartości wydatku</b></p>');
			validInputs = false;
		} else {
			var pattern = /[^\d,.]+/;
			if(pattern.test(amount)){
				$('#expenseMessageDiv').html('<p class="text-center text-danger light-input-bg"><b>Kwota wydatku powinna zawierac jedynie cyfry oraz przecinek lub kropkę</b></p>');
				validInputs = false;
			}
			if(validInputs){
				amount = replaceCommaWithDot(amount);
				if(checkForMoreThanOneDot(amount)){
					$('#expenseMessageDiv').html('<p class="text-center text-danger light-input-bg"><b>Wprowadzono więcej niż jedną kropkę lub przecinek</b></p>');
					validInputs = false;
				}
			}
		}
		// check date format
		if(validInputs){
			var date = $('#expenseDate').val();
			if(date){
				var pattern = /[^\d-]/;
				if(pattern.test(date)){
					validInputs = false;
					$('#expenseMessageDiv').html('<p class="text-center text-danger light-input-bg"><b>Proszę wprowadzić datę w formacie rrrr-mm-dd</b></p>');
				}
			} else {
				validInputs = false;
				$('#expenseMessageDiv').html('<p class="text-center text-danger light-input-bg"><b>Nie wprowadzono daty wydatku</b></p>');
			}
		}
		//check payment way
		if(validInputs){
			var paymentWay = $("input[name='payment']:checked").val();
			if(!paymentWay){
				$('#expenseMessageDiv').html('<p class="text-center text-danger light-input-bg"><b>Nie zadeklarowano metody płatności</b></p>');
				validInputs = false;
			}
		}
		//check categorie
		if(validInputs){
			var categorie = $("input[name='expenceCat']:checked").val();
			if(!categorie){
				$('#expenseMessageDiv').html('<p class="text-center text-danger light-input-bg"><b>Nie zadeklarowano kategorii wydatku</b></p>');
				validInputs = false;
			}
		}
		
		return validInputs;
	};
function replaceCommaWithDot(string){
	return string.replace(/\,+/gm, ".");
};
function checkForMoreThanOneDot(string){
	let count=0;
	for(let i=0; i<string.length; i++){
		if(string[i]=='.') count++;
	}
	if(count>1) return true;
	else return false;
};
});
	
