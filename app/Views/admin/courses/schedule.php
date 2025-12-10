<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">Manage Course Schedules</h1>
            <div class="text-secondary">Edit course schedules, days, times, and dates</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/courses') ?>" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i> Back to Courses
            </a>
            <a href="<?= base_url('admin/courses/assign') ?>" class="btn btn-success">
                <i class="bi bi-person-check me-1"></i> Assign Teachers
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
            <strong><i class="bi bi-calendar-week me-2"></i>Teacher Schedules Reference</strong>
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

    <!-- Course Schedules Management -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>All Courses - Schedule Management</strong>
            <span class="badge text-bg-primary"><?= count($courses ?? []) ?> courses</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($courses) && is_array($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Teacher</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Room</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr id="schedule-row-<?= esc($course['id']) ?>">
                                    <td>
                                        <strong><?= esc($course['title'] ?? '') ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= esc(strlen($course['description'] ?? '') > 50 ? substr($course['description'], 0, 50) . '...' : ($course['description'] ?? 'No description')) ?>
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
                                            <span class="text-muted">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm schedule-day-select" 
                                                data-course-id="<?= esc($course['id']) ?>"
                                                data-course-title="<?= esc($course['title'] ?? '') ?>">
                                            <option value="">Select Day</option>
                                            <option value="Monday" <?= ($course['schedule_day'] ?? '') === 'Monday' ? 'selected' : '' ?>>Monday</option>
                                            <option value="Tuesday" <?= ($course['schedule_day'] ?? '') === 'Tuesday' ? 'selected' : '' ?>>Tuesday</option>
                                            <option value="Wednesday" <?= ($course['schedule_day'] ?? '') === 'Wednesday' ? 'selected' : '' ?>>Wednesday</option>
                                            <option value="Thursday" <?= ($course['schedule_day'] ?? '') === 'Thursday' ? 'selected' : '' ?>>Thursday</option>
                                            <option value="Friday" <?= ($course['schedule_day'] ?? '') === 'Friday' ? 'selected' : '' ?>>Friday</option>
                                            <option value="Saturday" <?= ($course['schedule_day'] ?? '') === 'Saturday' ? 'selected' : '' ?>>Saturday</option>
                                            <option value="Sunday" <?= ($course['schedule_day'] ?? '') === 'Sunday' ? 'selected' : '' ?>>Sunday</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm schedule-time-input" 
                                               placeholder="9:00 AM - 11:00 AM"
                                               value="<?= esc($course['schedule_time'] ?? '') ?>"
                                               data-course-id="<?= esc($course['id']) ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm schedule-room-input" 
                                               placeholder="Room 101"
                                               value="<?= esc($course['schedule_room'] ?? '') ?>"
                                               data-course-id="<?= esc($course['id']) ?>">
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary update-schedule-btn" 
                                                data-course-id="<?= esc($course['id']) ?>"
                                                data-course-title="<?= esc($course['title'] ?? '') ?>">
                                            <i class="bi bi-save me-1"></i> Save
                                        </button>
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
            // Update schedule when Save button is clicked
            $(document).on('click', '.update-schedule-btn', function() {
                var btn = $(this);
                var courseId = btn.data('course-id');
                var courseTitle = btn.data('course-title');
                var row = btn.closest('tr');

                // Get all schedule values from the row
                var scheduleDay = row.find('.schedule-day-select').val();
                var scheduleTime = row.find('.schedule-time-input').val();
                var scheduleRoom = row.find('.schedule-room-input').val();

                // Disable button and show loading
                btn.prop('disabled', true);
                var originalHtml = btn.html();
                btn.html('<i class="bi bi-hourglass-split me-1"></i> Saving...');

                $.post('<?= base_url('admin/courses/update-schedule') ?>', {
                    course_id: courseId,
                    schedule_day: scheduleDay,
                    schedule_time: scheduleTime,
                    schedule_room: scheduleRoom
                }, function(response) {
                    if (response.success) {
                        // Show success message
                        showToast('Schedule updated successfully for "' + courseTitle + '"', 'success');
                        // Re-enable button
                        btn.prop('disabled', false);
                        btn.html(originalHtml);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to update schedule.'));
                        btn.prop('disabled', false);
                        btn.html(originalHtml);
                    }
                }).fail(function(xhr) {
                    var errorMsg = 'An error occurred. Please try again.';
                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    } catch(e) {}
                    alert('Error: ' + errorMsg);
                    btn.prop('disabled', false);
                    btn.html(originalHtml);
                });
            });

            // Auto-save on Enter key in input fields
            $(document).on('keypress', '.schedule-time-input, .schedule-room-input', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    $(this).closest('tr').find('.update-schedule-btn').click();
                }
            });

            // Toast notification function
            function showToast(message, type) {
                type = type || 'success';
                var bgClass = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-warning');
                var toastHtml = '<div class="toast align-items-center text-white ' + bgClass + ' border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">' +
                    '<div class="d-flex">' +
                    '<div class="toast-body">' + message + '</div>' +
                    '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                    '</div>' +
                    '</div>';
                
                var $toast = $(toastHtml);
                $('body').append($toast);
                var toast = new bootstrap.Toast($toast[0]);
                toast.show();
                
                $toast.on('hidden.bs.toast', function() {
                    $toast.remove();
                });
            }
        });
    </script>
<?= $this->endSection() ?>

