<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron">
				<h2>Kelompok Klasifikasi</h2>
			</div>

			<div class="row">
				<div class="col-md-6">
					<?= form_open_multipart('user') ?>
						<div class="form-group">
							<label>Import data (.txt)</label>
							<input type="file" name="file" class="form-control">
						</div>
						<input type="submit" name="import" value="Import" class="btn btn-primary">
					<?= form_close() ?>	
				</div>
				<div class="col-md-6">
					<?= form_open('user') ?>
						<div class="form-group">
							<label>Train Model</label>
						</div>
						<input type="submit" name="train" value="Train" class="btn btn-primary">
					<?= form_close() ?>	
				</div>
			</div>
		</div>
	</div>
</section>