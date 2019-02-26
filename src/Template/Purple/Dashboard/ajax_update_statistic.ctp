<div id="dashboard-statistic-container" class="col-md-<?= $totalVisitorsPlatform == 0 ? '12' : '8' ?> grid-margin stretch-card">
    <div class="card">
        <div id="load-dashboard-statistic" class="card-body">
			<div class="clearfix">
			    <h4 class="card-title float-left">Visitors Statistics<br><small id="selected-title-date-range"><?= $current ?></small></h4>
			    <div id="visit-sale-chart-legend" class="rounded-legend legend-horizontal legend-top-right float-right"></div>                                     
			    </div>
			    <canvas id="visit-sale-chart" class="mt-4"></canvas>
			</div>
		</div>
	</div>
</div>

<?php 
	if ($totalVisitorsPlatform > 0):
?>
<div id="recent-activity-container" class="col-md-4 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Visitors Platform</h4>
            
            <?php 
            	if ($visitorsWindows > 0):
            		$windowsPercent = round(($visitorsWindows / $sumNormal) * 100, 2);
            ?>
            <p class="uk-margin-remove-bottom">Windows (<?= $windowsPercent ?>%)</p>
            <div class="progress">
              	<div class="progress-bar bg-gradient-primary" role="progressbar" style="width: <?= $windowsPercent ?>%" aria-valuenow="<?= $windowsPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <?php
            	endif;
            ?>

            <?php 
            	if ($visitorsMac > 0):
            		$macPercent = round(($visitorsMac / $sumNormal) * 100, 2);
            ?>
            <p class="uk-margin-remove-bottom">Mac OS (<?= $macPercent ?>%)</p>
            <div class="progress">
              	<div class="progress-bar bg-gradient-primary" role="progressbar" style="width: <?= $macPercent ?>%" aria-valuenow="<?= $macPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <?php
            	endif;
            ?>

            <?php 
            	if ($visitorsLinux > 0):
            		$linuxPercent = round(($visitorsLinux / $sumNormal) * 100, 2);
            ?>
            <p class="uk-margin-remove-bottom">Linux (<?= $linuxPercent ?>%)</p>
            <div class="progress">
              	<div class="progress-bar bg-gradient-primary" role="progressbar" style="width: <?= $linuxPercent ?>%" aria-valuenow="<?= $linuxPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <?php
            	endif;
            ?>

            <?php 
            	if ($visitorsAndroid > 0):
            		$androidPercent = round(($visitorsAndroid / $sumNormal) * 100, 2);
            ?>
            <p class="uk-margin-remove-bottom">Android (<?= $androidPercent ?>%)</p>
            <div class="progress">
              	<div class="progress-bar bg-gradient-primary" role="progressbar" style="width: <?= $androidPercent ?>%" aria-valuenow="<?= $androidPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <?php
            	endif;
            ?>

            <?php 
            	if ($visitorsIos > 0):
            		$iosPercent = round(($visitorsIos / $sumNormal) * 100, 2);
            ?>
            <p class="uk-margin-remove-bottom">iOS (<?= $iosPercent ?>%)</p>
            <div class="progress">
              	<div class="progress-bar bg-gradient-primary" role="progressbar" style="width: <?= $iosPercent ?>%" aria-valuenow="<?= $iosPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <?php
            	endif;
            ?>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h5 class="uk-margin-small-bottom">Total Visitors</h5>
                    <p class="uk-margin-small uk-margin-remove-bottom"><?= $this->Purple->shortenNumber($sumNormal) ?></p>
                    <hr class="uk-hidden@l uk-hidden@xl">
                </div>
            </div>
        </div>
    </div>
</div>
<?php
	endif;
?>

<script type="text/javascript">
    $(document).ready(function() {
		$("#selected-date-range").html('<?= $current ?>');

		if ($("#visit-sale-chart").length) {

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
	            			foreach($label as $newLabel) { 
	            				echo "'".$newLabel."',";
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
		            			foreach($value as $newValue) { 
		            				echo $newValue.",";
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
		            			foreach($mobile as $newMobile) { 
		            				echo $newMobile.",";
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
	                      		stepSize: 20,
	                      		max: <?php if ($maxVisitor == 0) echo '50'; else { $maxAxis = $maxVisitor + 200; echo $maxAxis; }  ?>
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