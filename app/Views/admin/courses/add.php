<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">Add New Course</h1>
            <div class="text-secondary">Create a new course</div>
        </div>
        <a href="<?= base_url('admin/courses') ?>" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Courses
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <strong>Course Information</strong>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_url('admin/courses/create') ?>">
                <?= csrf_field() ?>
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= ($validation && $validation->hasError('title')) ? 'is-invalid' : '' ?>" 
                               id="title" name="title" value="<?= esc(old('title') ?? '') ?>" required>
                        <?php if ($validation && $validation->hasError('title')): ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('title')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control <?= ($validation && $validation->hasError('description')) ? 'is-invalid' : '' ?>" 
                                  id="description" name="description" rows="4"><?= esc(old('description') ?? '') ?></textarea>
                        <?php if ($validation && $validation->hasError('description')): ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('description')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Teacher assignment will be done through the <a href="<?= base_url('admin/courses/assign') ?>" class="alert-link">Assign Courses</a> page after creating the course.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="schedule_day" class="form-label">Schedule Day</label>
                        <select class="form-select" id="schedule_day" name="schedule_day">
                            <option value="">Select Day</option>
                            <option value="Monday" <?= old('schedule_day') === 'Monday' ? 'selected' : '' ?>>Monday</option>
                            <option value="Tuesday" <?= old('schedule_day') === 'Tuesday' ? 'selected' : '' ?>>Tuesday</option>
                            <option value="Wednesday" <?= old('schedule_day') === 'Wednesday' ? 'selected' : '' ?>>Wednesday</option>
                            <option value="Thursday" <?= old('schedule_day') === 'Thursday' ? 'selected' : '' ?>>Thursday</option>
                            <option value="Friday" <?= old('schedule_day') === 'Friday' ? 'selected' : '' ?>>Friday</option>
                            <option value="Saturday" <?= old('schedule_day') === 'Saturday' ? 'selected' : '' ?>>Saturday</option>
                            <option value="Sunday" <?= old('schedule_day') === 'Sunday' ? 'selected' : '' ?>>Sunday</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="schedule_time" class="form-label">Schedule Time</label>
                        <input type="text" class="form-control" id="schedule_time" name="schedule_time" 
                               placeholder="e.g., 9:00 AM - 11:00 AM" value="<?= esc(old('schedule_time') ?? '') ?>">
                        <small class="form-text text-muted">Enter time range (e.g., 9:00 AM - 11:00 AM)</small>
                    </div>

                    <div class="col-md-6">
                        <label for="schedule_room" class="form-label">Room/Location</label>
                        <input type="text" class="form-control" id="schedule_room" name="schedule_room" 
                               placeholder="e.g., Room 101, Online" value="<?= esc(old('schedule_room') ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="schedule_start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="schedule_start_date" name="schedule_start_date" 
                               value="<?= esc(old('schedule_start_date') ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="schedule_end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="schedule_end_date" name="schedule_end_date" 
                               value="<?= esc(old('schedule_end_date') ?? '') ?>">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Create Course
                    </button>
                    <a href="<?= base_url('admin/courses') ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>

