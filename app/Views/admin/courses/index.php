<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">Course Management</h1>
            <div class="text-secondary">Manage all courses in the system</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/courses/schedule') ?>" class="btn btn-warning">
                <i class="bi bi-calendar-week me-1"></i> Manage Schedules
            </a>
            <a href="<?= base_url('admin/courses/assign') ?>" class="btn btn-success">
                <i class="bi bi-person-check me-1"></i> Assign Courses
            </a>
            <a href="<?= base_url('admin/courses/add') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add New Course
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

    <!-- Search Filter -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/courses') ?>" class="row g-2">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search courses by title, description, or instructor..." 
                           value="<?= esc($search ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>All Courses</strong>
            <span class="badge text-bg-primary"><?= count($courses ?? []) ?> courses</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($courses) && is_array($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Course Title</th>
                                <th>Description</th>
                                <th>Instructor</th>
                                <th>Schedule</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($course['title'] ?? '') ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= esc(strlen($course['description'] ?? '') > 100 ? substr($course['description'], 0, 100) . '...' : ($course['description'] ?? 'No description')) ?>
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
                                            <span class="text-danger">No instructor assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['schedule_day']) || !empty($course['schedule_time'])): ?>
                                            <div class="small">
                                                <?php if (!empty($course['schedule_day'])): ?>
                                                    <strong><?= esc($course['schedule_day']) ?></strong>
                                                <?php endif; ?>
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
                                        <small class="text-muted"><?= esc($course['created_at'] ?? '') ?></small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= base_url('admin/courses/edit/' . $course['id']) ?>" 
                                               class="btn btn-outline-primary" title="Edit Course">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('admin/course/' . $course['id'] . '/students') ?>" 
                                               class="btn btn-outline-info" title="Manage Students">
                                                <i class="bi bi-people"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger delete-course-btn" 
                                                    data-course-id="<?= esc($course['id']) ?>"
                                                    data-course-title="<?= esc($course['title'] ?? '') ?>"
                                                    title="Delete Course">
                                                <i class="bi bi-trash"></i>
                                            </button>
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

    <script>
        $(document).ready(function() {
            // Delete course confirmation
            $(document).on('click', '.delete-course-btn', function() {
                var courseId = $(this).data('course-id');
                var courseTitle = $(this).data('course-title');
                
                if (confirm('Are you sure you want to delete "' + courseTitle + '"?\n\nThis action cannot be undone and will also remove all enrollments for this course.')) {
                    window.location.href = '<?= base_url('admin/courses/delete/') ?>' + courseId;
                }
            });
        });
    </script>
<?= $this->endSection() ?>

