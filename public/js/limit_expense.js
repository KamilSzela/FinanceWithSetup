$(document).ready(function(){
	
	$('#test').on('click', function(){
		var cat = $('input[name="expenceCat"]:checked');
		
		console.log(cat[0].id);
		//alert(cat.);
	});
	
	$("input[name='expenseCat']").change(
    function(){
        if (this.checked && this.value != '') {
			var amount = $('input[name="expenceAmount"]').val();
			alert(amount);
        }
    });
	
});
	
