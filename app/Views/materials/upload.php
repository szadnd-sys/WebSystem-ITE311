<div class="container mt-4">
	<h3>Upload Course Materials</h3>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success"><?php echo session()->getFlashdata('success'); ?></div>
	<?php endif; ?>
	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger"><?php echo session()->getFlashdata('error'); ?></div>
	<?php endif; ?>
	<?php if (session()->getFlashdata('access_error')): ?>
		<div class="alert alert-warning"><?php echo session()->getFlashdata('access_error'); ?></div>
	<?php endif; ?>

	<div class="card mb-4">
		<div class="card-body">
			<form action="<?php echo base_url('admin/course/' . $course_id . '/upload'); ?>" method="post" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<div class="mb-3">
					<label for="userfile" class="form-label">Select file</label>
					<input class="form-control" type="file" id="userfile" name="userfile" required>
					<small class="text-muted">Allowed: pdf, doc, docx, ppt, pptx, txt, zip. Max 10MB.</small>
				</div>
				<button type="submit" class="btn btn-primary">Upload</button>
			</form>
			<?php if (isset($validation) && $validation): ?>
				<div class="mt-3 alert alert-danger">
					<?php echo $validation->listErrors(); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<h5>Existing Materials</h5>
	<div class="table-responsive">
		<table class="table table-striped align-middle">
			<thead>
				<tr>
					<th>#</th>
					<th>File Name</th>
					<th>Uploaded</th>
					<th class="text-end">Actions</th>
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
								<a class="btn btn-sm btn-success" href="<?php echo base_url('materials/download/' . $m['id']); ?>">Download</a>
								<a class="btn btn-sm btn-danger" href="<?php echo base_url('materials/delete/' . $m['id']); ?>" onclick="return confirm('Delete this material?');">Delete</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="4" class="text-center text-muted">No materials uploaded yet.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


