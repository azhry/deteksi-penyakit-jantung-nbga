<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron">
				<h2>ETL</h2>
			</div>

			<div class="row">
				<div class="col-md-6">
					<?= form_open_multipart('user', ['id' => 'upload_form', 'onsubmit' => 'return false;']) ?>
						<div class="form-group">
							<label>Import data (.txt)</label>
							<input type="file" name="file" class="form-control">
						</div>
						<input type="submit" name="import" onclick="import_data(); return false;" value="Import" class="btn btn-primary">
						<button type="button" class="btn btn-primary" onclick="preprocess_data(); return false;">Preprocess Data</button>
					<?= form_close() ?>	
				</div>
			</div>
			<div class="row">
				<div class="col-md-12" id="result" style="text-align: center;">
					
				</div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	function preprocess_data() {
		$.ajax({
			url: '<?= base_url('user') ?>',
			type: 'POST',
			data: {
				preprocess: true
			},
			beforeSend: function() {
				$('#result').html('<div class="preloader">' +
						'<div class="spinner-layer pl-red">' +
							'<div class="circle-clipper left">' +
								'<div class="circle"></div>' +
							'</div>' +
							'<div class="circle-clipper right">' +
								'<div class="circle"></div>' +
							'</div>' +
						'</div>' +
					'</div>');
			},
			success: success_response,
			error: function(e) {
				console.log(e.responseText);
				$('#result').html(e.responseText);
			}
		});
		return false;
	}

	function import_data() {
		var form = $('#upload_form')[0];
		var form_data = new FormData(form);
		form_data.append('import', true);
		$.ajax({
			url: '<?= base_url('user') ?>',
			type: 'POST',
			data: form_data,
			enctype: 'multipart/form-data',
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function() {
				$('#result').html('<div class="preloader">' +
						'<div class="spinner-layer pl-red">' +
							'<div class="circle-clipper left">' +
								'<div class="circle"></div>' +
							'</div>' +
							'<div class="circle-clipper right">' +
								'<div class="circle"></div>' +
							'</div>' +
						'</div>' +
					'</div>');
			},
			success: success_response,
			error: function(e) {
				console.log(e.responseText);
				$('#result').html(e.responseText);
			}
		});

		return false;
	}

	function success_response(response) {
		$('#result').html('');
		var json = $.parseJSON(response);
		var html = '<table class="table table-striped table-hover">';
		html += '<thead>';
		html += '<tr>';
		html += '<th>Usia</th>';
		html += '<th>Jenis Kelamin</th>';
		html += '<th>Tipe Nyeri Dada</th>';
		html += '<th>Tekanan Darah</th>';
		html += '<th>Kolesterol</th>';
		html += '<th>Kadar Gula Darah</th>';
		html += '<th>Gelombang Elektrokardiograf</th>';
		html += '<th>Detak Jantung</th>';
		html += '<th>Angina</th>';
		html += '<th>Oldpeak</th>';
		html += '<th>Slope</th>';
		html += '<th>Ca</th>';
		html += '<th>Thal</th>';
		html += '<th>Label</th>';
		html += '</tr>';
		html += '</thead>';

		html += '<tbody>';
		for (var i = 0; i < json.length; i++) {
			html += '<tr>';
			html += '<td>' + json[i].age + '</td>';
			html += '<td>' + json[i].sex + '</td>';
			html += '<td>' + json[i].cp + '</td>';
			html += '<td>' + json[i].trestbps + '</td>';
			html += '<td>' + json[i].chol + '</td>';
			html += '<td>' + json[i].fbs + '</td>';
			html += '<td>' + json[i].restecg + '</td>';
			html += '<td>' + json[i].thalach + '</td>';
			html += '<td>' + json[i].exang + '</td>';
			html += '<td>' + json[i].oldpeak + '</td>';
			html += '<td>' + json[i].slope + '</td>';
			html += '<td>' + json[i].ca + '</td>';
			html += '<td>' + json[i].thal + '</td>';
			html += '<td>' + (json[i].num == 0 ? '-' : '+') + '</td>';
			html += '</tr>';
		}
		html += '</tbody></table>';
		$('#result').html(html);
	}
</script>