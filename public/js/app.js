$(document).ready(function(){
	
	
	$("[name='formCube']").submit(function(){
		
		$.ajax({
			type: 'POST',
			dataType: "json",
			url: $(this).attr('action'),
			data: $(this).serialize(),
			beforeSend: function() {
				$('#p2').removeClass('hidden');
   		    },
			success: function(xhr)
			{ 
				showResults(xhr,1);
			},
			complete: function() {
				$('#p2').addClass('hidden');
			},
			error: function(xhr, status, error) {
				
				if(xhr.status == 422)
				{
					showResults(xhr.responseJSON,0);
				}
			}
			
		});
		return false;
		
	});
	
	
	function showResults(xhr, type)
	{
		var color = '#88da99';//Color verde
		var content = '';
		if(type == 0)
		{
			content  = 'Errores encontrados!';
			response = xhr.errors;
			color = '#e8a1a1';	
		}else{
			content  = 'Resultados';
			response = xhr.results;
		}   	
		
		content += '<ul>';
		
		$.each(response, function(index, value){
			content += '<li>'+ value +'</li>';
		});
		
		content += '</ul>'
		
		$("#info").css({background:color});
		$("#info").html(content);
		if(type == 0)
		{
			$("#info").effect("shake");	
		}
	}
	
	
});