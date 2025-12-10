<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">Assign Courses to Teachers</h1>
            <div class="text-secondary">Manage course assignments and check for schedule conflicts</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/courses') ?>" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i> Back to Courses
            </a>
            <a href="<?= base_url('admin/courses/schedule') ?>" class="btn btn-warning">
                <i class="bi bi-calendar-week me-1"></i> Manage Schedules
            </a>
            <a href="<?= base_url('admin/courses/add') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add Course
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Teacher Schedules Overview -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info bg-opacity-10">
            <strong><i class="bi bi-calendar-week me-2"></i>Teacher Schedules</strong>
            <small class="text-muted ms-2">View all teacher schedules to avoid conflicts</small>
        </div>
        <div class="card-body">
            <?php if (!empty($teachers) && is_array($teachers)): ?>
                <div class="row g-3">
                    <?php foreach ($teachers as $teacher): ?>
                        <?php 
                        $schedules = $teacherSchedules[$teacher['id']] ?? [];
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">
                                        <i class="bi bi-person-badge me-1"></i>
                                        <?= esc($teacher['name']) ?>
                                    </h6>
                                    <small class="text-muted d-block mb-2"><?= esc($teacher['email']) ?></small>
                                    <?php if (!empty($schedules)): ?>
                                        <div class="small">
                                            <?php foreach ($schedules as $schedule): ?>
                                                <div class="mb-1">
                                                    <span class="badge bg-primary"><?= esc($schedule['schedule_day'] ?? '') ?></span>
                                                    <span class="text-muted"><?= esc($schedule['schedule_time'] ?? '') ?></span>
                                                    <?php if (!empty($schedule['schedule_room'])): ?>
                                                        <span class="text-muted">• <?= esc($schedule['schedule_room']) ?></span>
                                                    <?php endif; ?>
                                                    <br>
                                                    <small class="text-muted"><?= esc($schedule['title'] ?? '') ?></small>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">No scheduled courses</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No teachers found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Unassigned Courses -->
    <?php if (!empty($unassignedCourses) && is_array($unassignedCourses)): ?>
    <div class="card border-0 shadow-sm mb-4 border-warning">
        <div class="card-header bg-warning bg-opacity-10">
            <strong><i class="bi bi-exclamation-triangle me-2"></i>Unassigned Courses</strong>
            <span class="badge bg-warning text-dark ms-2"><?= count($unassignedCourses) ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Course Title</th>
                            <th>Description</th>
                            <th>Schedule</th>
                            <th>Assign Teacher</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unassignedCourses as $course): ?>
                            <tr id="course-row-<?= esc($course['id']) ?>">
                                <td><strong><?= esc($course['title'] ?? '') ?></strong></td>
                                <td>
                                    <small class="text-muted">
                                        <?= esc(strlen($course['description'] ?? '') > 80 ? substr($course['description'], 0, 80) . '...' : ($course['description'] ?? 'No description')) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if (!empty($course['schedule_day']) || !empty($course['schedule_time'])): ?>
                                        <small>
                                            <?= esc($course['schedule_day'] ?? '') ?> 
                                            <?= !empty($course['schedule_time']) ? ' - ' . esc($course['schedule_time']) : '' ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">Not scheduled</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm assign-teacher-select" 
                                            data-course-id="<?= esc($course['id']) ?>"
                                            data-course-title="<?= esc($course['title'] ?? '') ?>">
                                        <option value="">Select Teacher</option>
                                        <?php if (!empty($teachers) && is_array($teachers)): ?>
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?= esc($teacher['id']) ?>">
                                                    <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td class="text-end">
                                    <a href="<?= base_url('admin/courses/edit/' . $course['id']) ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Edit Course">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- All Courses with Teacher Assignments -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>All Courses - Teacher Assignments</strong>
            <span class="badge text-bg-primary"><?= count($courses ?? []) ?> courses</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($courses) && is_array($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Course Title</th>
                                <th>Assigned Teacher</th>
                                <th>Schedule</th>
                                <th>Change Assignment</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr id="course-assign-row-<?= esc($course['id']) ?>">
                                    <td>
                                        <strong><?= esc($course['title'] ?? '') ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= esc(strlen($course['description'] ?? '') > 60 ? substr($course['description'], 0, 60) . '...' : ($course['description'] ?? 'No description')) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['instructor_name'])): ?>
                                            <div>
                                                <strong><?= esc($course['instructor_name']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= esc($course['instructor_email'] ?? '') ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-danger">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['schedule_day']) || !empty($course['schedule_time'])): ?>
                                            <div class="small">
                                                <strong><?= esc($course['schedule_day'] ?? '') ?></strong>
                                                <?php if (!empty($course['schedule_time'])): ?>
                                                    <br><?= esc($course['schedule_time']) ?>
                                                <?php endif; ?>
                                                <?php if (!empty($course['schedule_room'])): ?>
                                                    <br><i class="bi bi-geo-alt"></i> <?= esc($course['schedule_room']) ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Not scheduled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <select class="form-select form-select-sm change-teacher-select" 
                                                    data-course-id="<?= esc($course['id']) ?>"
                                                    data-course-title="<?= esc($course['title'] ?? '') ?>"
                                                    data-current-day="<?= esc($course['schedule_day'] ?? '') ?>"
                                                    data-current-time="<?= esc($course['schedule_time'] ?? '') ?>">
                                                <option value="">Change Teacher</option>
                                                <?php if (!empty($teachers) && is_array($teachers)): ?>
                                                    <?php foreach ($teachers as $teacher): ?>
                                                        <option value="<?= esc($teacher['id']) ?>" 
                                                                <?= ($course['instructor_id'] ?? '') == $teacher['id'] ? 'selected' : '' ?>>
                                                            <?= esc($teacher['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                <option value="0">Unassign</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/courses/edit/' . $course['id']) ?>" 
                                               class="btn btn-outline-primary" title="Edit Course & Schedule">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('admin/course/' . $course['id'] . '/students') ?>" 
                                               class="btn btn-outline-info" title="Manage Students">
                                                <i class="bi bi-people"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-x display-4 text-muted"></i>
                    <p class="text-muted mt-3">No courses found.</p>
                    <a href="<?= base_url('admin/courses/add') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Your First Course
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Assign teacher to unassigned course
            $(document).on('change', '.assign-teacher-select', function() {
                var select = $(this);
                var courseId = select.data('course-id');
                var courseTitle = select.data('course-title');
                var teacherId = select.val();

                if (!teacherId) {
                    return;
                }

                // Get schedule from the row
                var row = select.closest('tr');
                var scheduleDay = row.find('td:eq(2)').text().split(' - ')[0].trim();
                var scheduleTime = row.find('td:eq(2)').text().includes(' - ') ? row.find('td:eq(2)').text().split(' - ')[1].trim() : '';

                select.prop('disabled', true);

                $.post('<?= base_url('admin/courses/assign-teacher') ?>', {
                    course_id: courseId,
                    teacher_id: teacherId,
                    schedule_day: scheduleDay,
                    schedule_time: scheduleTime
                }, function(response) {
                    if (response.success) {
                        // Reload page to show updated assignment
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to assign course.'));
                        select.prop('disabled', false);
                        select.val('');
                    }
                }).fail(function(xhr) {
                    var errorMsg = 'An error occurred. Please try again.';
                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    } catch(e) {}
                    alert('Error: ' + errorMsg);
                    select.prop('disabled', false);
                    select.val('');
                });
            });

            // Change teacher assignment
            $(document).on('change', '.change-teacher-select', function() {
                var select = $(this);
                var courseId = select.data('course-id');
                var courseTitle = select.data('course-title');
                var teacherId = select.val();
                var currentDay = select.data('current-day');
                var currentTime = select.data('current-time');

                if (teacherId === '0') {
                    // Unassign teacher
                    if (!confirm('Unassign teacher from "' + courseTitle + '"?')) {
                        select.val(select.data('original-value') || '');
                        return;
                    }
                } else if (!teacherId) {
                    return;
                } else {
                    // Check if schedule exists
                    if (!currentDay || !currentTime) {
                        alert('Please set the schedule first in the Edit Course page before assigning a teacher.');
                        select.val(select.data('original-value') || '');
                        return;
                    }
                }

                select.prop('disabled', true);
                var originalValue = select.find('option[selected]').val() || select.data('original-value') || '';

                $.post('<?= base_url('admin/courses/assign-teacher') ?>', {
                    course_id: courseId,
                    teacher_id: teacherId === '0' ? '' : teacherId,
                    schedule_day: currentDay,
                    schedule_time: currentTime
                }, function(response) {
                    if (response.success) {
                        // Reload page to show updated assignment
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to assign course.'));
                        select.prop('disabled', false);
                        select.val(originalValue);
                    }
                }).fail(function(xhr) {
                    var errorMsg = 'An error occurred. Please try again.';
                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    } catch(e) {}
                    alert('Error: ' + errorMsg);
                    select.prop('disabled', false);
                    select.val(originalValue);
                });
            });
        });
    </script>
<?= $this->endSection() ?>

