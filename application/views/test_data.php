<section class="content card">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-6">
						<h3>Test Data</h3>
						<button type="button" class="btn btn-primary btn-lg" onclick="test_data();">Test</button>
						<div class="row">
							<div class="preloader" id="loader" style="visibility: hidden;">
								<div class="spinner-layer pl-red">
									<div class="circle-clipper left">
										<div class="circle"></div>
									</div>
									<div class="circle-clipper right">
										'<div class="circle"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-9" id="accuracy_chart">
								<canvas id="myChart" width="600" height="600"></canvas>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<table class="table table-hover table-striped">
									<thead>
										<tr>
											<th>Label</th>
											<th>Precision</th>
											<th>Recall</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>+</td>
											<td id="precision_pos"></td>
											<td id="recall_pos"></td>
										</tr>
										<tr>
											<td>-</td>
											<td id="precision_neg"></td>
											<td id="recall_neg"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<h3>Optimize Attribute</h3>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group form-float" style="width: 100%;">
									<div class="form-line">
										<input type="number" min="0" max="1" step="0.01" name="mutation_rate" id="mutation_rate" class="form-control">
										<label class="form-label">Mutation Rate</label>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-float" style="width: 100%;">
									<div class="form-line">
										<input type="number" name="pop_size" id="pop_size" class="form-control">
										<label class="form-label">Ukuran Populasi</label>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group form-float" style="width: 100%;">
									<div class="form-line">
										<input type="number" name="num_generations" id="num_generations" class="form-control">
										<label class="form-label">Jumlah Generasi</label>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-float" style="width: 100%;">
									<div class="form-line">
										<input type="number" min="0" max="1" step="0.01" name="target" id="target" class="form-control">
										<label class="form-label">Target</label>
									</div>
								</div>
							</div>
						</div>
						<button type="button" class="btn btn-primary btn-lg" onclick="optimize();">Optimize</button>
						<div class="row">
							<div class="col-md-9" id="generation_chart">
								<canvas id="myLineChart" width="600" height="600"></canvas>
							</div>
						</div>
						<div class="row">
							<div class="col-md-8">
								<table class="table table-hover table-striped">
									<thead>
										<tr>
											<th>Label</th>
											<th>Precision</th>
											<th>Recall</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>+</td>
											<td id="optimized_precision_pos"></td>
											<td id="optimized_recall_pos"></td>
										</tr>
										<tr>
											<td>-</td>
											<td id="optimized_precision_neg"></td>
											<td id="optimized_recall_neg"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-md-8">
								<h3>Selected Attribute</h3>
								<table class="table table-hover table-striped">
									<thead>
										<tr>
											<th>No.</th>
											<th>Attribute</th>
										</tr>
									</thead>
									<tbody id="selected_attribute">
										<tr>
											<td></td>
											<td></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
	$(document).ready(function() {
		display_bar_chart(0, 0);
		display_line_chart([0], [0])
	});

	var nb = 0;
	var nbga = 0;

	function test_data() {
		$.ajax({
			url: '<?= base_url('user/test') ?>',
			type: 'POST',
			data: {
				test: true,
			},
			beforeSend: function() {
				$('#loader').css('visibility', 'visible');
			},
			success: function(response) {
				var json = $.parseJSON(response);
				nb = json.accuracy * 100;
				display_bar_chart(nb, nbga);
				var cm = json.cm;
				$('#precision_pos').text((cm.precision_pos * 100).toPrecision(4) + '%');
				$('#precision_neg').text((cm.precision_neg * 100).toPrecision(4) + '%');
				$('#recall_pos').text((cm.recall_pos * 100).toPrecision(4) + '%');
				$('#recall_neg').text((cm.recall_neg * 100).toPrecision(4) + '%');
				$('#loader').css('visibility', 'hidden');
			},
			error: function(e) {
				console.log(e.responseText);
				$('#loader').css('visibility', 'hidden');
			}
		});
		return false;
	}

	function optimize() {
		$.ajax({
			url: '<?= base_url('user/test') ?>',
			type: 'POST',
			data: {
				optimize: true,
				mutation_rate: $('#mutation_rate').val(),
				num_generations: $('#num_generations').val(),
				num_populations: $('#pop_size').val(),
				set_criteria: $('#target').val()
			},
			beforeSend: function() {
				$('#loader').css('visibility', 'visible');
			},
			success: function(response) {
				console.log(response);
				var json = $.parseJSON(response);
				nbga = json.fittest_chromosomes[json.fittest_chromosomes.length - 1].fitness * 100;
				display_bar_chart(nb, nbga);

				var labels = [];
				var data = [];
				for (var i = 0; i < json.fittest_chromosomes.length; i++) {
					labels.push(i + 1);
					data.push(json.fittest_chromosomes[i].fitness * 100);
				}
				display_line_chart(data, labels);

				$('#optimized_precision_pos').text((json.fittest_chromosomes[json.fittest_chromosomes.length - 1].cm.precision_pos * 100).toPrecision(4) + '%');
				$('#optimized_precision_neg').text((json.fittest_chromosomes[json.fittest_chromosomes.length - 1].cm.precision_neg * 100).toPrecision(4) + '%');
				$('#optimized_recall_pos').text((json.fittest_chromosomes[json.fittest_chromosomes.length - 1].cm.recall_pos * 100).toPrecision(4) + '%');
				$('#optimized_recall_neg').text((json.fittest_chromosomes[json.fittest_chromosomes.length - 1].cm.recall_neg * 100).toPrecision(4) + '%');

				var allele = json.allele;
				var attr_html = '';
				for (var i = 0; i < allele.length; i++) {
					attr_html += '<tr>';
					attr_html += '<td>' + (i + 1) + '</td>';
					attr_html += '<td>' + allele[i].name + '</td>';
					attr_html += '</tr>';
				}

				$('#selected_attribute').html(attr_html);
				$('#loader').css('visibility', 'hidden');
			},
			error: function(e) {
				console.log(e.responseText);
				$('#loader').css('visibility', 'hidden');
			}
		});
		return false;
	}

	function display_bar_chart(nb, nbga) {
		var ctx = document.getElementById("myChart").getContext('2d');
		var myChart = new Chart(ctx, {
		    type: 'bar',
		    data: {
		        labels: ['Model'],
		        datasets: [{
		            label: 'Naive Bayes',
		            data: [nb],
		            backgroundColor: [
		                'rgba(255, 99, 132, 0.2)',
		            ],
		            borderColor: [
		                'rgba(255,99,132,1)',
		            ],
		            borderWidth: 1
		        }, {
		        	label: 'Naive Bayes + Genetic Algorithm',
		            data: [nbga],
		            backgroundColor: [
		                'rgba(54, 162, 235, 0.2)',
		            ],
		            borderColor: [
		                'rgba(54, 162, 235, 1)',
		            ],
		            borderWidth: 1
		        }]
		    },
		    options: {
		        scales: {
		            yAxes: [{
		                ticks: {
		                    beginAtZero:true,
		                    max: 100
		                },
		                scaleLabel: {
		                	display: true,
		                	labelString: 'Accuracy'
		                }
		            }]
		        }
		    }
		});
	}

	function display_line_chart(data, labels) {
		var ctx = document.getElementById("myLineChart").getContext('2d');
		var myLineChart = new Chart(ctx, {
		     type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: "Accuracy",
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    data: data,
                    fill: false,
                }]
            },
            options: {
		        scales: {
		            yAxes: [{
		                ticks: {
		                    beginAtZero:true,
		                    max: 100
		                },
		                scaleLabel: {
		                	display: true,
		                	labelString: 'Accuracy'
		                }
		            }],
		            xAxes: [{
		            	scaleLabel: {
		            		display: true,
		                	labelString: 'Generation'
		                }
		            }]
		        }
		    }
		});
	}
</script>