$( document ).ready(function() {

	var date = new Date();
	
	
	
	function fetchStamps() {
			
		$.ajax({
			url: "http://goedbezig.marijnvdwerf.nl/app/api/stamps?since="+date.toISOString(),
			type: "GET",            
			dataType: "JSON",
			success: function(result) {
				date = new Date();
				console.log("Hij doet het");
				$.each(result.stamps, function(index, element) {
					console.log(element);
				    $(".lijstje").append('<li>' + element.type + '</li>');
				});
				$("#checktime").html("Last check: "+date);		
			},
			error: function (error) {
			   	console.error(error);
			}
		});
		
	}
	fetchStamps();
	window.setInterval(fetchStamps, 10000);

});

