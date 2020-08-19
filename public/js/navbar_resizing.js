$(document).ready(function(){	
	adjustNavBar();
});
$( window ).resize(function() {
    adjustNavBar();
});
function adjustNavBar(){
	var screenWidth = $(window).width();
	
	if(parseInt(screenWidth)< 990){
		$('#navList li').removeClass('w-20');
	}
	else{
		$('#navList li').addClass('w-20');
	}
}