$(document).ready(function(){
	$('#sidebar_link').click(function(e){
		$('#sidebar').addClass('show');
		e.stopPropagation();
		e.preventDefault();
	});

	$(document).click(function(){
		$('#sidebar').removeClass('show');
	});

	$('#sidebar').click(function(e){
		e.stopPropagation();
	});
});