<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">Dashboard</h1>
            <div class="text-secondary">Welcome back, <span class="fw-semibold"><?= esc(session('user_name') ?: session('user_email')) ?></span></div>
        </div>
    </div>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-emoji-smile me-1"></i> You are signed in.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <?php if (session('role') === 'admin'): ?>
        <div class="mt-4">
            <h2 class="h4 text-light mb-3">Admin Overview</h2>

            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-people me-1"></i> Total Users</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalUsers ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-shield-lock me-1"></i> Admins</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalAdmins ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-mortarboard me-1"></i> Teachers</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalTeachers ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-person-badge me-1"></i> Students</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalStudents ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12 col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-journal-bookmark me-1"></i> Courses</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalCourses ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Recent Users</strong>
                    <span class="badge text-bg-secondary">Last 10</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentUsers) && is_array($recentUsers)): ?>
                                    <?php foreach ($recentUsers as $u): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= esc($u['name'] ?? '') ?></td>
                                            <td class="text-secondary small"><?= esc($u['email'] ?? '') ?></td>
                                            <td><span class="badge text-bg-light text-uppercase"><?= esc($u['role'] ?? '') ?></span></td>
                                            <td class="small text-secondary"><?= esc($u['created_at'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent users.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.enroll-btn').on('click', function(e) {
            e.preventDefault();
            var courseId = $(this).data('course-id');
            var title = $(this).data('title');
            var description = $(this).data('description');
            var btn = $(this);

            $.post('<?= base_url('course/enroll') ?>', { course_id: courseId }, function(response) {
                if (response.success) {
                    // Show success message
                    var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        response.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>';
                    $('.alert-success').after(alertHtml);

                    // Disable the button
                    btn.prop('disabled', true).text('Enrolled');

                    // Move the course to enrolled list
                    var enrolledList = $('.list-group');
                    if (enrolledList.length === 0) {
                        // If no enrolled courses, create the list
                        $('.card-body').first().html('<div class="list-group"></div>');
                        enrolledList = $('.list-group');
                    }
                    var newItem = '<div class="list-group-item">' +
                        '<h5 class="mb-1">' + title + '</h5>' +
                        '<p class="mb-1">' + description + '</p>' +
                        '<small>Enrolled on: ' + new Date().toLocaleDateString() + '</small>' +
                        '</div>';
                    enrolledList.append(newItem);

                    // Remove the card from available courses
                    btn.closest('.col-md-4').remove();

                    // If no more available courses, show message
                    if ($('.enroll-btn').length === 0) {
                        $('.card-body').last().html('<p class="text-center text-muted">No available courses.</p>');
                    }
                } else {
                    // Show error message
                    var alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        response.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>';
                    $('.alert-success').after(alertHtml);
                }
            }, 'json').fail(function() {
                var alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    'An error occurred. Please try again.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>';
                $('.alert-success').after(alertHtml);
            });
        });

        // Drop course with confirmation
        $(document).on('click', '.drop-btn', function(e){
            e.preventDefault();
            var courseId = $(this).data('course-id');
            var title = $(this).data('title');
            if (!courseId) return;
            var msg = 'Are you sure you want to drop\n"' + (title || 'this course') + '"?';
            if (!confirm(msg)) return;
            var btn = $(this);
            $.post('<?= base_url('course/drop') ?>', { course_id: courseId }, function(response){
                if (response && response.success) {
                    // Remove this accordion item
                    var item = btn.closest('.accordion-item');
                    var container = item.closest('#enrolledCoursesAccordion');
                    item.remove();
                    // If no more enrolled items, show empty text
                    if (container.find('.accordion-item').length === 0) {
                        container.closest('.card-body').html('<p class="text-center text-muted">No enrolled courses.</p>');
                    }
                    // Also add back to Available Courses without reload
                    var availRow = $('#availableCoursesRow');
                    var noMsg = $('#noAvailableMsg');
                    if (noMsg.length) {
                        // Replace message with a row container
                        noMsg.replaceWith('<div class="row" id="availableCoursesRow"></div>');
                        availRow = $('#availableCoursesRow');
                    }
                    if (availRow.length) {
                        var cardHtml = ''
                            + '<div class="col-md-4 mb-3">'
                            +   '<div class="card h-100">'
                            +     '<div class="card-body">'
                            +       '<h5 class="card-title">' + $('<div>').text(title || '').html() + '</h5>'
                            +       '<p class="card-text"></p>'
                            +       '<button class="btn btn-primary enroll-btn" data-course-id="' + String(courseId) + '" data-title="' + $('<div>').text(title || '').html() + '" data-description="">Enroll</button>'
                            +     '</div>'
                            +   '</div>'
                            + '</div>';
                        availRow.prepend(cardHtml);
                    }
                } else {
                    alert((response && response.message) ? response.message : 'Failed to drop the course.');
                }
            }, 'json').fail(function(){
                alert('An error occurred. Please try again.');
            });
        });
    });
    </script>

    <?php if (session('role') === 'admin' && !empty($allCourses) && is_array($allCourses)): ?>
        <div class="mt-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <strong>Manage Courses</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allCourses as $c): ?>
                                    <tr>
                                        <td><?= esc($c['title'] ?? '') ?></td>
                                        <td><?= esc($c['description'] ?? '') ?></td>
                                        <td><?= esc($c['created_at'] ?? '') ?></td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-primary" href="<?= base_url('admin/course/' . esc($c['id']) . '/upload') ?>">Upload Materials</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session('role') === 'teacher'): ?>
        <div class="mt-4">
            <h2 class="h4 text-light mb-3"><i class="bi bi-mortarboard me-2"></i>Teacher Overview</h2>

            <div class="row g-3 mb-2">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-muted">Your Courses</div>
                            <div class="fs-4 fw-bold"><?= esc(is_array($courses ?? null) ? count($courses) : 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-muted">Recent Submissions</div>
                            <div class="fs-4 fw-bold"><?= esc(is_array($notifications ?? null) ? count($notifications) : 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <strong>Your Courses</strong>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <input type="text" id="courseFilter" class="form-control form-control-sm w-auto" placeholder="Filter courses...">
                            <button type="button" id="exportCoursesCsv" class="btn btn-sm btn-outline-secondary">Export CSV</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle" id="coursesTable">
                            <thead>
                                <tr>
                                    <th data-sort="text">Title</th>
                                    <th>Description</th>
                                    <th data-sort="date">Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($courses) && is_array($courses)): ?>
                                    <?php foreach ($courses as $c): ?>
                                        <tr>
                                            <td>
                                                <a class="text-decoration-none" href="<?= base_url('teacher/course/' . esc($c['id']) . '/students') ?>">
                                                    <?= esc($c['title'] ?? '') ?>
                                                </a>
                                            </td>
                                            <td><?= esc($c['description'] ?? '') ?></td>
                                            <td><?= esc($c['created_at'] ?? '') ?></td>
                                        <td class="text-end text-nowrap">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Course actions">
                                                <a class="btn btn-outline-secondary" href="<?= base_url('teacher/course/' . esc($c['id']) . '/students') ?>">View Students</a>
                                                <a class="btn btn-primary" href="<?= base_url('teacher/course/' . esc($c['id']) . '/upload') ?>">Upload</a>
                                                <a class="btn btn-outline-primary" href="<?= base_url('teacher/course/' . esc($c['id']) . '/announce') ?>">Announce</a>
                                            </div>
                                        </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No courses found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <strong><i class="bi bi-inbox me-2"></i>Recent Submissions</strong>
                    <button type="button" id="exportSubmissionsCsv" class="btn btn-sm btn-outline-secondary">Export CSV</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($notifications) && is_array($notifications)): ?>
                                    <?php foreach ($notifications as $n): ?>
                                        <tr>
                                            <td><?= esc($n['student_name'] ?? '') ?></td>
                                            <td><?= esc($n['course_id'] ?? '') ?></td>
                                            <td><?= esc($n['created_at'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No recent submissions.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session('role') === 'student'): ?>
        <div class="mt-4">
            <h2 class="h4 text-light mb-3"><i class="bi bi-person-badge me-2"></i>Student Overview</h2>

            

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-journal-check me-2"></i>Enrolled Courses</strong>
                    <?php $enrolledCount = !empty($enrolledCourses) && is_array($enrolledCourses) ? count($enrolledCourses) : 0; ?>
                    <span class="badge text-bg-secondary"><?= $enrolledCount ?></span>
                </div>
                <div class="card-body">
                    <?php if (!empty($enrolledCourses) && is_array($enrolledCourses)): ?>
                        <div class="accordion" id="enrolledCoursesAccordion">
                            <?php foreach ($enrolledCourses as $idx => $c): ?>
                                <?php $cid = (int)($c['id'] ?? 0); ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $idx ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $idx ?>" aria-expanded="false" aria-controls="collapse<?= $idx ?>">
                                            <?= esc($c['title'] ?? '') ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $idx ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $idx ?>" data-bs-parent="#enrolledCoursesAccordion">
                                        <div class="accordion-body">
                                            <p class="mb-2"><?= esc($c['description'] ?? '') ?></p>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <small class="d-block">Enrolled on: <?= esc($c['created_at'] ?? '') ?></small>
                                                <button class="btn btn-sm btn-outline-danger drop-btn" data-course-id="<?= $cid ?>" data-title="<?= esc($c['title'] ?? '') ?>">Drop course</button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>File Name</th>
                                                            <th>Uploaded</th>
                                                            <th class="text-end">Download</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $materialsForCourse = $materialsByCourse[$cid] ?? []; ?>
                                                        <?php if (!empty($materialsForCourse)): ?>
                                                            <?php foreach ($materialsForCourse as $mi => $m): ?>
                                                                <tr>
                                                                    <td><?= $mi + 1 ?></td>
                                                                    <td><?= esc($m['file_name'] ?? '') ?></td>
                                                                    <td><?= esc($m['created_at'] ?? '') ?></td>
                                                                    <td class="text-end">
                                                                        <a class="btn btn-sm btn-primary" href="<?= base_url('materials/download/' . esc($m['id'])) ?>">Download</a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted">No materials yet.</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted">No enrolled courses.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-journal-plus me-2"></i>Available Courses</strong>
                </div>
                <div class="card-body">
                    <?php
                    // Fetch available courses (not enrolled by the user)
                    $db = \Config\Database::connect();
                    $user_id = session('user_id');
                    $availableCourses = $db->table('courses')
                        ->whereNotIn('id', function($builder) use ($user_id) {
                            return $builder->select('course_id')->from('enrollments')->where('user_id', $user_id);
                        })
                        ->get()
                        ->getResultArray();
                    ?>
                    <?php if (!empty($availableCourses) && is_array($availableCourses)): ?>
                        <div class="row" id="availableCoursesRow">
                            <?php foreach ($availableCourses as $course): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title d-flex align-items-center gap-2"><i class="bi bi-journal-text text-primary"></i><span><?= esc($course['title'] ?? '') ?></span></h5>
                                            <p class="card-text"><?= esc($course['description'] ?? '') ?></p>
                                            <button class="btn btn-primary enroll-btn" data-course-id="<?= esc($course['id']) ?>" data-title="<?= esc($course['title']) ?>" data-description="<?= esc($course['description']) ?>">Enroll</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p id="noAvailableMsg" class="text-center text-muted">No available courses.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <strong><i class="bi bi-calendar-event me-2"></i>Upcoming Deadlines</strong>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Assignment</th>
                                            <th>Course</th>
                                            <th>Due</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($upcomingDeadlines) && is_array($upcomingDeadlines)): ?>
                                            <?php foreach ($upcomingDeadlines as $d): ?>
                                                <tr>
                                                    <td><?= esc($d['title'] ?? '') ?></td>
                                                    <td><?= esc($d['course_title'] ?? '') ?></td>
                                                    <td><?= esc($d['due_date'] ?? '') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No upcoming deadlines.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <strong><i class="bi bi-graph-up-arrow me-2"></i>Recent Grades</strong>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Assignment</th>
                                            <th>Course</th>
                                            <th>Score</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentGrades) && is_array($recentGrades)): ?>
                                            <?php foreach ($recentGrades as $g): ?>
                                                <tr>
                                                    <td><?= esc($g['assignment_title'] ?? '') ?></td>
                                                    <td><?= esc($g['course_title'] ?? '') ?></td>
                                                    <td><?= esc($g['score'] ?? '') ?></td>
                                                    <td><?= esc($g['created_at'] ?? '') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No recent grades.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session('role') === 'teacher'): ?>
        <script>
        (function(){
            var input = document.getElementById('courseFilter');
            if (!input) return;
            var table = document.getElementById('coursesTable');
            var tbody = table ? table.querySelector('tbody') : null;
            if (!tbody) return;
            input.addEventListener('input', function(){
                var q = this.value.toLowerCase();
                Array.prototype.forEach.call(tbody.rows, function(row){
                    var title = (row.cells[0] && row.cells[0].textContent || '').toLowerCase();
                    var desc  = (row.cells[1] && row.cells[1].textContent || '').toLowerCase();
                    var match = !q || title.indexOf(q) !== -1 || desc.indexOf(q) !== -1;
                    row.style.display = match ? '' : 'none';
                });
            });

            // simple sort by clicking on header
            var headers = table ? table.querySelectorAll('thead th[data-sort]') : [];
            Array.prototype.forEach.call(headers, function(h, idx){
                var asc = true;
                h.style.cursor = 'pointer';
                h.addEventListener('click', function(){
                    var type = h.getAttribute('data-sort');
                    var rows = Array.prototype.slice.call(tbody.rows);
                    rows.sort(function(a, b){
                        var av = (a.cells[idx] && a.cells[idx].textContent || '').trim();
                        var bv = (b.cells[idx] && b.cells[idx].textContent || '').trim();
                        if (type === 'date') {
                            var ad = Date.parse(av) || 0;
                            var bd = Date.parse(bv) || 0;
                            return asc ? ad - bd : bd - ad;
                        }
                        av = av.toLowerCase();
                        bv = bv.toLowerCase();
                        if (av < bv) return asc ? -1 : 1;
                        if (av > bv) return asc ? 1 : -1;
                        return 0;
                    });
                    // repaint
                    rows.forEach(function(r){ tbody.appendChild(r); });
                    asc = !asc;
                });
            });

            function tableToCsv(tableEl) {
                var rows = tableEl.querySelectorAll('tr');
                return Array.prototype.map.call(rows, function(row){
                    var cells = row.querySelectorAll('th,td');
                    return Array.prototype.map.call(cells, function(cell){
                        var text = (cell.textContent || '').replace(/"/g, '""');
                        return '"' + text + '"';
                    }).join(',');
                }).join('\n');
            }
            function downloadCsv(filename, csvText) {
                var blob = new Blob([csvText], { type: 'text/csv;charset=utf-8;' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }
            var exportCoursesBtn = document.getElementById('exportCoursesCsv');
            if (exportCoursesBtn && table) {
                exportCoursesBtn.addEventListener('click', function(){
                    var csv = tableToCsv(table);
                    downloadCsv('courses.csv', csv);
                });
            }
            var submissionsTable = document.querySelector('#coursesTable') ? document.querySelector('#coursesTable').closest('.mt-4').querySelectorAll('table')[1] : null;
            var exportSubmissionsBtn = document.getElementById('exportSubmissionsCsv');
            if (exportSubmissionsBtn) {
                // safer lookup for the submissions table within the second card
                var submissionsCard = exportSubmissionsBtn.closest('.card');
                var submissionsTbl = submissionsCard ? submissionsCard.querySelector('table') : null;
                exportSubmissionsBtn.addEventListener('click', function(){
                    if (!submissionsTbl) return;
                    var csv = tableToCsv(submissionsTbl);
                    downloadCsv('submissions.csv', csv);
                });
            }

            // Removed quick-upload select to avoid redundancy; per-row actions remain
        })();
        </script>
    <?php endif; ?>
<?= $this->endSection() ?>


