<div class="container mt-4">
	<h3>Course Materials</h3>

	<?php if (session()->getFlashdata('access_error')): ?>
		<div class="alert alert-warning"><?php echo session()->getFlashdata('access_error'); ?></div>
	<?php endif; ?>

	<div class="table-responsive">
		<table class="table table-striped align-middle">
			<thead>
				<tr>
					<th>#</th>
					<th>File Name</th>
					<th>Uploaded</th>
					<th class="text-end">Download</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($materials)): ?>
					<?php foreach ($materials as $index => $m): ?>
						<tr>
							<td><?php echo $index + 1; ?></td>
							<td><?php echo esc($m['file_name']); ?></td>
							<td><?php echo esc($m['created_at']); ?></td>
							<td class="text-end">
								<a class="btn btn-sm btn-primary" href="<?php echo base_url('materials/download/' . $m['id']); ?>">Download</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="4" class="text-center text-muted">No materials available.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


