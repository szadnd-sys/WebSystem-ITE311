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
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($enrolled)): ?>
          <?php foreach ($enrolled as $s): ?>
            <tr id="enrollment-row-<?= esc($s['enrollment_id'] ?? '') ?>">
              <td><?= esc($s['name'] ?? '') ?></td>
              <td><?= esc($s['email'] ?? '') ?></td>
              <td class="small text-secondary"><?= esc($s['enrollment_date'] ?? '') ?></td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-danger unenroll-btn" 
                        data-enrollment-id="<?= esc($s['enrollment_id'] ?? '') ?>"
                        data-course-id="<?= esc($course_id) ?>"
                        data-student-name="<?= esc($s['name'] ?? '') ?>"
                        data-course-title="Course #<?= esc($course_id) ?>">
                  <i class="bi bi-person-x me-1"></i>Unenroll
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center text-secondary">No students enrolled yet.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Unenroll button handler
            $(document).on('click', '.unenroll-btn', function() {
                var btn = $(this);
                var enrollmentId = btn.data('enrollment-id');
                var courseId = btn.data('course-id');
                var studentName = btn.data('student-name');
                var courseTitle = btn.data('course-title');
                
                if (!confirm('Are you sure you want to unenroll ' + studentName + ' from ' + courseTitle + '?')) {
                    return;
                }
                
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Unenrolling...');
                
                $.post('<?= base_url('course/unenroll-student') ?>', {
                    enrollment_id: enrollmentId,
                    course_id: courseId
                }, function(response) {
                    if (response.success) {
                        // Remove the row with fade effect
                        $('#enrollment-row-' + enrollmentId).fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if table is empty
                            var tbody = $(this).closest('tbody');
                            if (tbody.find('tr').length === 0) {
                                tbody.html('<tr><td colspan="4" class="text-center text-secondary">No students enrolled yet.</td></tr>');
                            }
                            
                            // Update total count
                            var badge = $('.badge.text-bg-secondary');
                            var currentCount = parseInt(badge.text().replace('Total: ', '')) || 0;
                            badge.text('Total: ' + Math.max(0, currentCount - 1));
                        });
                        
                        // Show success message
                        alert('Student unenrolled successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to unenroll student.'));
                        btn.prop('disabled', false).html('<i class="bi bi-person-x me-1"></i>Unenroll');
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Unenroll failed:', status, error);
                    alert('Error: Failed to unenroll student. Please try again.');
                    btn.prop('disabled', false).html('<i class="bi bi-person-x me-1"></i>Unenroll');
                });
            });
        });
    </script>
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
					<th class="text-end">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($enrolled)): ?>
					<?php foreach ($enrolled as $s): ?>
						<tr id="enrollment-row-<?= esc($s['enrollment_id'] ?? '') ?>">
							<td><?= esc($s['name'] ?? '') ?></td>
							<td><?= esc($s['email'] ?? '') ?></td>
							<td><?= esc($s['enrollment_date'] ?? '') ?></td>
							<td class="text-end">
								<button type="button" class="btn btn-sm btn-danger unenroll-btn" 
										data-enrollment-id="<?= esc($s['enrollment_id'] ?? '') ?>"
										data-course-id="<?= esc($course_id) ?>"
										data-student-name="<?= esc($s['name'] ?? '') ?>"
										data-course-title="Course #<?= esc($course_id) ?>">
									<i class="bi bi-person-x me-1"></i>Unenroll
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="4" class="text-center text-muted">No students enrolled yet.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


