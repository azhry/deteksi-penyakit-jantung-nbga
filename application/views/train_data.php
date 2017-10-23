<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron">
				<h2>Train Data</h2>
			</div>

			<div class="row">
				<div class="col-md-6">
					<input type="number" step="0.1" min="0.1" max="0.9" class="form-control" name="split_ratio" id="split_ratio" placeholder="Split Ratio" style="width: 30%;">
					<button type="button" class="btn btn-primary" onclick="train_data(); return false;">Train Data</button>
				</div>
				<div class="col-md-6" id="prior">
					
				</div>
			</div>
			<div class="row">
				<div class="col-md-6" id="likelihood_pos">
					
				</div>
				<div class="col-md-6" id="likelihood_neg">
					
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript">
	function train_data() {
		$.ajax({
			url: '<?= base_url('user/train') ?>',
			type: 'POST',
			data: {
				train: true,
				split_ratio: $('#split_ratio').val()
			},
			beforeSend: function() {
				$('#likelihood_pos').html('<div class="preloader">' +
						'<div class="spinner-layer pl-red">' +
							'<div class="circle-clipper left">' +
								'<div class="circle"></div>' +
							'</div>' +
							'<div class="circle-clipper right">' +
								'<div class="circle"></div>' +
							'</div>' +
						'</div>' +
					'</div>');
				$('#prior').html('');
				$('#likelihood_neg').html('');
			},
			success: success_response,
			error: function(e) {
				console.log(e.responseText);
				$('#prior').html('');
				$('#likelihood_pos').html(e.responseText);
				$('#likelihood_neg').html('');
			}
		});
		return false;
	}

	function success_response(response) {
		$('#prior').html('');
		$('#likelihood_pos').html('');
		$('#likelihood_neg').html('');
		var json = $.parseJSON(response);
		if (json.error == false) {
			var html_prior = '<table class="table table-striped table-hover"><thead>';
			html_prior += '<tr>';
			html_prior += '<th>Label</th>';
			html_prior += '<th>Prior</th>';
			html_prior += '</tr>';
			html_prior += '</thead><tbody>';
			for (var i = 0; i < json.prior.length; i++) {
				html_prior += '<tr>';
				html_prior += '<td>' + (json.prior[i].class == 0 ? '-' : '+') + '</td>';
				html_prior += '<td>' + json.prior[i].value + '</td>';
				html_prior += '</tr>';
			}
			html_prior += '</tbody></table>';

			var html_pos = '<table class="table table-striped table-hover"><thead>';
			html_pos += '<tr>';
			html_pos += '<th>Attribute</th>';
			html_pos += '<th>Value</th>';
			html_pos += '<th>Label</th>';
			html_pos += '<th>Likelihood</th>';
			html_pos += '</tr>';
			html_pos += '</thead><tbody>';
			var html_neg = html_pos;
			for (var i = 0; i < json.likelihood_pos.length; i++) {
				html_pos += '<tr>';
				html_pos += '<td>' + json.likelihood_pos[i].name + '</td>';
				html_pos += '<td>' + json.likelihood_pos[i].value + '</td>';
				html_pos += '<td>' + (json.likelihood_pos[i].class == 0 ? '-' : '+') + '</td>';
				html_pos += '<td>' + json.likelihood_pos[i].likelihood + '</td>';
				html_pos += '</tr>';
			}
			for (var i = 0; i < json.likelihood_neg.length; i++) {
				html_neg += '<tr>';
				html_neg += '<td>' + json.likelihood_neg[i].name + '</td>';
				html_neg += '<td>' + json.likelihood_neg[i].value + '</td>';
				html_neg += '<td>' + (json.likelihood_neg[i].class == 0 ? '-' : '+') + '</td>';
				html_neg += '<td>' + json.likelihood_neg[i].likelihood + '</td>';
				html_neg += '</tr>';
			}
			html_pos += '</tbody></table>';
			html_neg += '</tbody></table>';

			$('#prior').html(html_prior);
			$('#likelihood_pos').html(html_pos);
			$('#likelihood_neg').html(html_neg);

		} else {
			$('#likelihood_pos').html('ERROR');
		}
	}
</script>