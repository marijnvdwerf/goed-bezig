$( document ).ready(function() {

	var date = new Date();
	
	function fetchCards() {
		$.ajax({
			url: 'http://goedbezig.marijnvdwerf.nl/app/api/cards?',
			success: function(data) {
				$.each(data.cards, function(index, card) {
					var cardEl = $('<div/>', {
						'class': 'card',
						'data-achievement-id': card.id
					});
					
					cardEl.append('<h2>' + card.name + '</h2>');
					
					var stampList = $('<ul/>');
					$.each(card.stamps, function(index, stamp) {
						stampList.append('<li>' + stamp.type + '</li>');
					});
					cardEl.append(stampList);
					
					cardEl.appendTo('body');
				});
			},
			error: function(error) {
				console.log(error);
			}
		});
	}
	
	function fetchStamps() {
			
		$.ajax({
			url: "http://goedbezig.marijnvdwerf.nl/app/api/stamps?since="+date.toISOString(),
			type: "GET",            
			dataType: "JSON",
			success: function(result) {
				date = new Date();
				console.log("New check");
				$.each(result.stamps, function(index, element) {
				    $(".card[data-achievement-id=" + element.achievement_id + "] ul").append('<li>' + element.type + '</li>');
				});
				$("#checktime").html("Last check: "+date);		
			},
			error: function (error) {
			   	console.error(error);
			}
		});
		
	}
	
	fetchCards();
	window.setInterval(fetchStamps, 5000);

});

