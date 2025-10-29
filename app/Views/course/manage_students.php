<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Enrolled Students</h1>
    <a href="<?= previous_url() ?: base_url('teacher/dashboard') ?>" class="btn btn-outline-primary">Back</a>
  </div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <strong>Course ID: <?= esc($course_id) ?></strong>
    <span class="badge text-bg-secondary">Total: <?= esc(is_array($enrolled ?? null) ? count($enrolled) : 0) ?></span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-striped table-hover mb-0 align-middle">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Enrolled</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($enrolled)): ?>
          <?php foreach ($enrolled as $s): ?>
            <tr>
              <td><?= esc($s['name'] ?? '') ?></td>
              <td><?= esc($s['email'] ?? '') ?></td>
              <td class="small text-secondary"><?= esc($s['enrollment_date'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3" class="text-center text-secondary">No students enrolled yet.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

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


