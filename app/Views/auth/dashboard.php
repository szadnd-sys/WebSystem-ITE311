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

    <?php if (session('role') === 'teacher'): ?>
        <div class="mt-4">
            <h2 class="h4 text-light mb-3">Teacher Overview</h2>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <strong>Your Courses</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($courses) && is_array($courses)): ?>
                                    <?php foreach ($courses as $c): ?>
                                        <tr>
                                            <td><?= esc($c['title'] ?? '') ?></td>
                                            <td><?= esc($c['description'] ?? '') ?></td>
                                            <td><?= esc($c['created_at'] ?? '') ?></td>
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
                <div class="card-header bg-white">
                    <strong>Recent Submissions</strong>
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
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($enrolledCourses) && is_array($enrolledCourses)): ?>
                                    <?php foreach ($enrolledCourses as $c): ?>
                                        <tr>
                                            <td><?= esc($c['title'] ?? '') ?></td>
                                            <td><?= esc($c['description'] ?? '') ?></td>
                                            <td><?= esc($c['created_at'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No enrolled courses.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
<?= $this->endSection() ?>


