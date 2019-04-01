<script type="text/javascript">
    $(document).ready(function() {
    	/* Where in the world are you? */
	  	/*
	  	navigator.geolocation.getCurrentPosition(function(position) {
	  		//load weather using your lat/lng coordinates
	    	loadWeather(position.coords.latitude+','+position.coords.longitude); 
	  	});

		loadWeather('Denpasar','');

		function loadWeather(location, woeid) {
		  	$.simpleWeather({
			    location: location,
			    woeid: woeid,
			    unit: 'c',
			    success: function(weather) {
			    	$("#weather-card").find('.weather-temp').html(weather.temp+'&deg;'+weather.units.temp);
			    	$("#weather-card").find('.weather-location').html(weather.city+', '+weather.region);
			    	$("#weather-card").find('.weather-currently').html(weather.currently+' <i class="weather-icon icon-'+weather.code+' float-right"></i>');
			    },
			    error: function(error) {
			      	$("#weather-card").find('.weather-temp').html('');
			    	$("#weather-card").find('.weather-location').html('');
			    	$("#weather-card").find('.weather-currently').html(error);
			    }
		  	});
		}
		*/
	
		setTimeout(function() {
			$('.greeting-to-user').addClass('uk-animation-fade uk-animation-reverse');
		}, 5000);
		setTimeout(function() {
			$('.greeting-to-user').hide('500');
		}, 5800);

		if ($("#visit-sale-chart").length) {
			<?php
				$firstMonth = ucwords(strtolower($visitorsMonth[0]));
				$lastMonth  = ucwords(strtolower($visitorsMonth[5]));
			?>
			var firstMonth = "<?= $firstMonth ?>";
			var lastMonth  = "<?= $lastMonth ?>";

			$("#selected-date-range").html(firstMonth + ' - ' + lastMonth);

	      	Chart.defaults.global.legend.labels.usePointStyle = true;
	      	var ctx = document.getElementById('visit-sale-chart').getContext("2d");

	      	var gradientStrokeViolet = ctx.createLinearGradient(0, 0, 0, 181);
	      	gradientStrokeViolet.addColorStop(0, 'rgba(218, 140, 255, 1)');
	      	gradientStrokeViolet.addColorStop(1, 'rgba(154, 85, 255, 1)');
	      	var gradientLegendViolet = 'linear-gradient(to right, rgba(218, 140, 255, 1), rgba(154, 85, 255, 1))';
	      
	      	var gradientStrokeBlue = ctx.createLinearGradient(0, 0, 0, 360);
	      	gradientStrokeBlue.addColorStop(0, 'rgba(54, 215, 232, 1)');
	      	gradientStrokeBlue.addColorStop(1, 'rgba(177, 148, 250, 1)');
	      	var gradientLegendBlue = 'linear-gradient(to right, rgba(54, 215, 232, 1), rgba(177, 148, 250, 1))';

	      	var myChart = new Chart(ctx, {
	        	type: 'bar',
	        	data: {
	            	labels: [
	            		<?php 
	            			foreach($visitorsMonth as $lastSixMonth) { 
	            				echo "'".$lastSixMonth."',";
	            			} 
	            		?>
	            	],
	            	datasets: [
	              	{
		                label: "Visitors",
		                borderColor: gradientStrokeViolet,
		                backgroundColor: gradientStrokeViolet,
		                hoverBackgroundColor: gradientStrokeViolet,
		                legendColor: gradientLegendViolet,
		                pointRadius: 0,
		                fill: false,
		                borderWidth: 1,
		                fill: 'origin',
		                data: [
		                	<?php 
		            			foreach($totalVisitors6Month as $totalVisitors) { 
		            				echo $totalVisitors.",";
		            			} 
		            		?>
		                ]
	              	},
	              	{
		                label: "Mobile Visitors",
		                borderColor: gradientStrokeBlue,
		                backgroundColor: gradientStrokeBlue,
		                hoverBackgroundColor: gradientStrokeBlue,
		                legendColor: gradientLegendBlue,
		                pointRadius: 0,
		                fill: false,
		                borderWidth: 1,
		                fill: 'origin',
		                data: [
		                	<?php 
		            			foreach($totalMobileVisitors6Month as $totalMobileVisitors) { 
		            				echo $totalMobileVisitors.",";
		            			} 
		            		?>
		                ]
	              	}]
	        	},
	        	options: {
	          		responsive: true,
	          		legend: false,
	          		legendCallback: function(chart) {
			            var text = []; 
			            text.push('<ul>'); 
			            for (var i = 0; i < chart.data.datasets.length; i++) { 
			                text.push('<li><span class="legend-dots" style="background:' + 
			                           chart.data.datasets[i].legendColor + 
			                           '"></span>'); 
			                if (chart.data.datasets[i].label) { 
			                    text.push(chart.data.datasets[i].label); 
			                } 
			                text.push('</li>'); 
			            } 
			            text.push('</ul>'); 
			            return text.join('');
	         		},
	          		scales: {
	              		yAxes: [{
	                  		ticks: {
	                      		display: false,
	                      		min: 0,
	                      		stepSize: <?php if (max($totalVisitors6Month) == 0) echo '50'; else { $stepSize = round((max($totalVisitors6Month) + 200) / 10); echo $stepSize; }  ?>,
	                      		max: <?php if (max($totalVisitors6Month) <= 50) echo '50'; else { $findDivider = round(max($totalVisitors6Month) / 50, PHP_ROUND_HALF_DOWN); $maxAxis = ($findDivider + 1) * 50; echo $maxAxis; }  ?>
	                  	},
	                  	gridLines: {
	                    	drawBorder: false,
	                    	color: 'rgba(235,237,242,1)',
	                    	zeroLineColor: 'rgba(235,237,242,1)'
	                  	}
	              	}],
	              	xAxes: [{
	                  	gridLines: {
		                    display:false,
		                    drawBorder: false,
		                    color: 'rgba(0,0,0,1)',
		                    zeroLineColor: 'rgba(235,237,242,1)'
	                  	},
	                  	ticks: {
		                    padding: 20,
		                    fontColor: "#9c9fa6",
		                    autoSkip: true,
	                  	},
	                  	categoryPercentage: 0.5,
	                  	barPercentage: 0.5
	              	}]
	            }
	        },
		        elements: {
		            point: {
		              	radius: 0
		            }
		        }
		    })
		     
		    $("#visit-sale-chart-legend").html(myChart.generateLegend());
	    }
    })
</script>