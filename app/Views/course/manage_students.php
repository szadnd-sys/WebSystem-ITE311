<div class="container mt-4">
	<h3>Manage Students for Course #<?= esc($course_id) ?></h3>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
	<?php endif; ?>
	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
	<?php endif; ?>
	<?php if (session()->getFlashdata('access_error')): ?>
		<div class="alert alert-warning"><?= session()->getFlashdata('access_error') ?></div>
	<?php endif; ?>

	<div class="card mb-4">
		<div class="card-header bg-white"><strong>Add Student by Email</strong></div>
		<div class="card-body">
			<form method="post" action="<?= base_url('admin/course/' . $course_id . '/students') ?>">
				<?= csrf_field() ?>
				<div class="row g-2 align-items-end">
					<div class="col-sm-8 col-md-6">
						<label for="student_email" class="form-label">Student Email</label>
						<input type="email" class="form-control" id="student_email" name="student_email" placeholder="student@example.com" required>
					</div>
					<div class="col-sm-4 col-md-3">
						<button type="submit" class="btn btn-primary w-100">Add Student</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<h5>Enrolled Students</h5>
	<div class="table-responsive">
		<table class="table table-striped align-middle">
			<thead>
				<tr>
					<th>Name</th>
					<th>Email</th>
					<th>Enrolled On</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($enrolled)): ?>
					<?php foreach ($enrolled as $s): ?>
						<tr>
							<td><?= esc($s['name'] ?? '') ?></td>
							<td><?= esc($s['email'] ?? '') ?></td>
							<td><?= esc($s['enrollment_date'] ?? '') ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="3" class="text-center text-muted">No students enrolled yet.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


