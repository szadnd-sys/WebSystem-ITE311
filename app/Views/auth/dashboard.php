<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 text-light">Dashboard</h1>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-primary">Logout</a>
    </div>

    <div class="alert alert-success" role="alert">
        Welcome, <?= esc(session('user_name') ?: session('user_email')) ?>!
    </div>

    <?php if (session('role') === 'admin'): ?>
        <div class="mt-4">
            <h2 class="h4 text-light mb-3">Admin Overview</h2>

            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-muted">Total Users</div>
                            <div class="fs-4 fw-bold"><?= esc($totalUsers ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-muted">Admins</div>
                            <div class="fs-4 fw-bold"><?= esc($totalAdmins ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-muted">Teachers</div>
                            <div class="fs-4 fw-bold"><?= esc($totalTeachers ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-muted">Students</div>
                            <div class="fs-4 fw-bold"><?= esc($totalStudents ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-muted">Courses</div>
                            <div class="fs-4 fw-bold"><?= esc($totalCourses ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <strong>Recent Users</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
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
                                            <td><?= esc($u['name'] ?? '') ?></td>
                                            <td><?= esc($u['email'] ?? '') ?></td>
                                            <td><?= esc($u['role'] ?? '') ?></td>
                                            <td><?= esc($u['created_at'] ?? '') ?></td>
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
    });
    </script>

    <?php if (session('role') === 'teacher'): ?>
        <div class="mt-4">
            <h2 class="h4 text-light mb-3">Teacher Overview</h2>

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
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" id="courseFilter" class="form-control form-control-sm w-auto" placeholder="Filter courses...">
                            <button type="button" id="exportCoursesCsv" class="btn btn-sm btn-outline-secondary">Export CSV</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="coursesTable">
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
                                            <td><?= esc($c['title'] ?? '') ?></td>
                                            <td><?= esc($c['description'] ?? '') ?></td>
                                            <td><?= esc($c['created_at'] ?? '') ?></td>
                                            <td class="text-end">
                                                <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/course/' . esc($c['id']) . '/students') ?>">Add Students</a>
                                                <a class="btn btn-sm btn-primary" href="<?= base_url('admin/course/' . esc($c['id']) . '/upload') ?>">Upload Materials</a>
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
                    <strong>Recent Submissions</strong>
                    <button type="button" id="exportSubmissionsCsv" class="btn btn-sm btn-outline-secondary">Export CSV</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
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
            <h2 class="h4 text-light mb-3">Student Overview</h2>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <strong>Enrolled Courses</strong>
                </div>
                <div class="card-body">
                    <?php if (!empty($enrolledCourses) && is_array($enrolledCourses)): ?>
                        <div class="list-group">
                            <?php foreach ($enrolledCourses as $c): ?>
                                <div class="list-group-item">
                                    <h5 class="mb-1"><?= esc($c['title'] ?? '') ?></h5>
                                    <p class="mb-1"><?= esc($c['description'] ?? '') ?></p>
                                    <small>Enrolled on: <?= esc($c['created_at'] ?? '') ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted">No enrolled courses.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <strong>Available Courses</strong>
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
                        <div class="row">
                            <?php foreach ($availableCourses as $course): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= esc($course['title'] ?? '') ?></h5>
                                            <p class="card-text"><?= esc($course['description'] ?? '') ?></p>
                                            <button class="btn btn-primary enroll-btn" data-course-id="<?= esc($course['id']) ?>" data-title="<?= esc($course['title']) ?>" data-description="<?= esc($course['description']) ?>">Enroll</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted">No available courses.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <strong>Upcoming Deadlines</strong>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
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
                            <strong>Recent Grades</strong>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
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
        })();
        </script>
    <?php endif; ?>
<?= $this->endSection() ?>


