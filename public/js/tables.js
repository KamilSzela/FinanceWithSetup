$(document).ready(function(){	

	adjustSummaryContainerheight();
	prepareSummaryBoard();	
	jQuery.ajaxSetup({async:false});
	generateStandardSpanSummary('lastMonth');
	$('#generateSummaryButton').on('click', function(){
		//var summaryAmount = 0;
		prepareSummaryBoard();						
		
		var dateSpan = $('#chosenDateSpan').val();
		if(dateSpan == 'nonStandardSpan'){
			
			var beginningOfDateSpan = $('#beginDateInput').val();
			var endingOfDateSpan = $('#endingDateInput').val();
			generateNonStandardSpanSummary(beginningOfDateSpan, endingOfDateSpan);
			
		}else{		
			generateStandardSpanSummary(dateSpan);
		}	
		
	});
function checkIfDateOneIsOlder(dateOne, dateTwo){
	if(dateOne.substr(0,4) < dateTwo.substr(0,4)) return true;
	else if(dateOne.substr(0,4) > dateTwo.substr(0,4)) return false;
	else{
		if(dateOne.substr(5,2) < dateTwo.substr(5,2)) return true;
		else if(dateOne.substr(5,2) > dateTwo.substr(5,2)) return false;
		else{
			if(dateOne.substr(8,2) < dateTwo.substr(8,2)) return true;
			else return false;
		}
	}
}
function generateNonStandardSpanSummary(beginningOfDateSpan, endingOfDateSpan){
	if(checkIfDateOneIsOlder(beginningOfDateSpan,endingOfDateSpan)){	
			var sumOfExpences = 0;
			var sumOfIncomes = 0;	
				$.get("/Summary/expencesTables", {beginDate: beginningOfDateSpan, endDate: endingOfDateSpan} , function(json){
					generateExpenceTable(json);		
				});
				$.get("/Summary/incomesTables", {beginDate: beginningOfDateSpan, endDate: endingOfDateSpan} , function(json){
					generateIncomeTable(json);		
				});
				$.get("/Summary/expenceSummaryTable", {beginDate: beginningOfDateSpan, endDate: endingOfDateSpan} , function(json){
					sumOfExpences = generateExpenceSummaryTable(json, sumOfExpences);		
				});
				$.get("/Summary/incomeSummarytable", {beginDate: beginningOfDateSpan, endDate: endingOfDateSpan} , function(json){
					sumOfIncomes = generateIncomeSummaryTable(json, sumOfIncomes);
					showEvaluation(sumOfExpences, sumOfIncomes);
				});
			}
			else{
				$('#dateMessageDiv').html('Data końca okresu nie może być starsza niż data początku okresu');
			}
}
function generateStandardSpanSummary(dateSpan){
		var sumOfExpences = 0;
		var sumOfIncomes = 0;
			$.get("/Summary/expencesTables", {timePeriod: dateSpan} , function(json){
				generateExpenceTable(json);								
			});
			$.get("/Summary/incomesTables", {timePeriod: dateSpan} , function(json){
				generateIncomeTable(json);		
			});		
			$.get("/Summary/expenceSummaryTable", {timePeriod: dateSpan} , function(json){
				sumOfExpences = generateExpenceSummaryTable(json, sumOfExpences);		
			});			
			$.get("/Summary/incomeSummarytable", {timePeriod: dateSpan} , function(json){
				sumOfIncomes = generateIncomeSummaryTable(json, sumOfIncomes);
				showEvaluation(sumOfExpences, sumOfIncomes);				
			});
}
$('#chosenDateSpan').on('change', function(){
	prepareSummaryBoard();
});
function prepareSummaryBoard() {
	var chosenSpan = $('#chosenDateSpan').val();
	$('#expenceTable').html("");
	$('#expenceTableHeader').html("");
	$('#expenceCategoriesTable').html("");
	$('#expenceCategoriesTableHeader').html("");
	$('#incomeCategoriesTableHeader').html("");
	$('#incomeCategoriesTable').html("");
	$('#incomeTable').html("");
	$('#incomeTableHeader').html("");
	$('#chartExpencesContainer').html("");
	$('#chartExpencesContainer').css('height', '0px');
	$('#showEvaluation').html("");
	$('#showEvaluation').css({'background': 'none'});
	$('#dateMessageDiv').html("");
	$('#summaryContainer').css({
				'height': '500px'
				});
	if(chosenSpan=='nonStandardSpan'){
		$('#nonStandardDateInput').removeClass("d-none");
		$('#nonStandardDateInput').addClass("d-flex");
	}
	else{
		$('#nonStandardDateInput').removeClass("d-flex");
		$('#nonStandardDateInput').addClass("d-none");
	}
}
function generateExpenceTable(json){		
		var jsonObj = $.parseJSON(json);
		if(jsonObj.length == 0){
			$('#expenceTable').html('<p class="text-center"><b>Brak wydatków w rozpatrywanym okresie</b></p>');
		}else{
			$('#expenceTableHeader').html("<b>Tabela podsumowująca twoje wydatki:</b>");
			$("<thead><tr><th>Kwota</th><th>Data</th><th>Kategoria</th><th>Sposób płatności</th><th>Komentarz</th></tr></thead><tbody>").appendTo('#expenceTable');
			for(var klucz in jsonObj){
				var wiersz = jsonObj[klucz];      
				var kwota = wiersz[0];
				var data = wiersz[1];
				var id_kategorii = wiersz[2];
				var id_platnosc = wiersz[3];
				var komentarz = wiersz[4];
				
				$("<tr><td>"+kwota+"</td><td>"+data+"</td><td>"+id_kategorii+"</td><td>"+id_platnosc+"</td><td>"+komentarz+"</td></tr>").appendTo('#expenceTable');             
			}
			$('</tbody>').appendTo('#expenceTable');
		}		
}

function generateExpenceSummaryTable(json, sumOfExpences){
	var jsonObj = $.parseJSON(json);
	var dataPoints = [];
	
	if(jsonObj.length == 0){
			$('#expenceCategoriesTable').html('');
		}else{
			$('#expenceCategoriesTableHeader').html('<b>Tabela podsumowująca twoje wydatki względem kategorii:</b>');
			$('<thead><tr><th class="text-center">Wartość w kategorii</th><th class="text-center">Kategoria</th></thead><tbody>').appendTo('#expenceCategoriesTable');
			for(key in jsonObj){
				var row = jsonObj[key];
				var amount = row[0];
				var category = row[1];
				sumOfExpences += parseFloat(amount);
				
				let oneDataToChart = {
					y: 0,
					label: ""
				};
				oneDataToChart.y = amount;
				oneDataToChart.label = category;
				dataPoints.push(oneDataToChart);
				$('<tr><td class="text-center">'+amount+'</td><td class="text-center">'+category+'</td></tr>').appendTo('#expenceCategoriesTable');
			}
			$('</tbody>').appendTo('#expenceCategoriesTable');
		}
	if(dataPoints.length>0){	
		for(var k=0; k<dataPoints.length; k++){
		  dataPoints[k].y = dataPoints[k].y/sumOfExpences * 100;
	    }
		var chart = new CanvasJS.Chart("chartExpencesContainer", {
			animationEnabled: true,
			title: {
				text: "Wydatki według kategorii"
			},
			data: [{
				type: "pie",
				startAngle: 240,
				yValueFormatString: "##0.00\"%\"",
				indexLabel: "{label} {y}",
				dataPoints
			}]
		});
		chart.render();
		$('#chartExpencesContainer').css('height', '400px');
		$('#summaryContainer').css({
				'height': 'auto'
				});
	}
	return sumOfExpences;
}
function generateIncomeTable(json){
			
		var jsonObj = $.parseJSON(json);
		if(jsonObj.length == 0){
			$('#incomeTable').html('<p class="text-center"><b>Brak dochodów w rozpatrywanym okresie</b></p>');
		}else{
			$('#incomeTableHeader').html("<b>Tabela podsumowująca twoje dochody:</b>");
			$("<thead><tr><th>Kwota</th><th>Data</th><th>Kategoria</th><th>Komentarz</th></tr><thead><tbody>").appendTo('#incomeTable');
			for(var klucz in jsonObj){
				var wiersz = jsonObj[klucz];      
				var kwota = wiersz[0];
				var data = wiersz[1];
				var id_kategorii = wiersz[2];
				var komentarz = wiersz[3];
				  $("<tr><td>"+kwota+"</td><td>"+data+"</td><td>"+id_kategorii+"</td><td>"+komentarz+"</td></tr>").appendTo('#incomeTable');             
			}
			$('</tbody>').appendTo('#incomeTable');
		}	
}
function generateIncomeSummaryTable(json, sumOfIncomes){
	var jsonObj = $.parseJSON(json);

	if(jsonObj.length == 0){
			$('#incomeCategoriesTable').html('');
		}else{
			$('#incomeCategoriesTableHeader').html('<b>Tabela podsumowująca twoje dochody względem kategorii:</b>');
			$('<thead><tr><th class="text-center">Wartość w kategorii</th><th class="text-center">Kategoria</th></thead><tbody>').appendTo('#incomeCategoriesTable');
			for(key in jsonObj){
				var row = jsonObj[key];
				var amount = row[0];
				var category = row[1];
				sumOfIncomes += parseFloat(amount);
				
				$('<tr><td class="text-center">'+amount+'</td><td class="text-center">'+category+'</td></tr>').appendTo('#incomeCategoriesTable');
			}
			$('</tbody>').appendTo('#incomeCategoriesTable');
		}	
	return 	sumOfIncomes;
}

function showEvaluation(sumOfExpences, sumOfIncomes){
	sumOfExpences = Math.round(sumOfExpences*100)/100;
	sumOfIncomes = Math.round(sumOfIncomes*100)/100;	
	var summaryAmount = Math.round((sumOfIncomes - sumOfExpences)*100)/100;
	if(summaryAmount >= 0){
		sumDivContent = "<p>Gratulacje! Świetnie sobie radzisz z zarządzaniem swoimi pieniędzmi</p><p>Twój bilans: "+sumOfIncomes.toString()+" PLN - "+sumOfExpences.toString()+" PLN = "+summaryAmount.toString()+" PLN</p>";
		background = 'radial-gradient(#126110 10%,#2b8c29 50%,#529e51 80%)';
	}
	else{
		sumDivContent = "<p>Niestety! Suma twoich wydatków przekroczyła sumę dochodów</p><p>Twój bilans: "+sumOfIncomes.toString()+" PLN - "+sumOfExpences.toString()+" PLN = "+summaryAmount.toString()+" PLN</p>";
		background = 'radial-gradient(#941e16 10%,#a8342c 50%,#bf524b 80%)';
	}
	$('#showEvaluation').html(sumDivContent);
	$('#showEvaluation').css({'background': background});
	adjustSummaryContainerheight();
}
function adjustSummaryContainerheight(){
	let summaryHeight = $('#divToHeightEvaluation').height();
	if(summaryHeight>500){
		$('#summaryContainer').css({
			'height': 'auto'
		});
	}
	else {
		$('#summaryContainer').css({
			'height': '500px'
		});
	}
}
});