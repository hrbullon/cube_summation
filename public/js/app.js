$(document).ready(function(){
	
	
	$("[name='formCube']").submit(function(){
		
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: $(this).serialize(),
			beforeSend: function() {
				$('#p2').removeClass('hidden');
   		    },
			success: function(response) {
				console.log(response);
			},
			complete: function() {
				$('#p2').addClass('hidden');
			}
			
		});
		return false;
		
	});
	
});