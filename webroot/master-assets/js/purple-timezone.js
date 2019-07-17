$(document).ready(function() {
	// Get client timezone
	clientTimezone = function(guessTimezone) {
		var timezone = moment.tz.guess(true);
		// if (guessTimezone != timezone) {
			var token = $('#csrf-ajax-token').val(),
				url   = $('#client-timezone-url').val();

			$.ajax({
				type: "POST",
				url:  url,
				headers : {
					'X-CSRF-Token': token
				},
				data: { 'timezone':timezone },
				cache: false,
				beforeSend: function() {},
				success: function(data) {
					var json    = $.parseJSON(data),
						status  = (json.status);

					if (status == 'ok') {
						var clientTz = (json.timezone);
						console.log('timezone : ' + clientTz);
					}
					else {
						console.log('Error to get client timezone');
					}
				}
			})
		// } 
	}
})