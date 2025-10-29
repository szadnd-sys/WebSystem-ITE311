<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 text-light">Create Announcement</h1>
        <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-primary">Back to Dashboard</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="course_id" class="form-label">Course</label>
                    <select id="course_id" name="course_id" class="form-select" required>
                        <option value="">Select a course</option>
                        <?php if (!empty($courses) && is_array($courses)): ?>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= esc($c['id']) ?>" <?= (int)($course_id ?? 0) === (int)$c['id'] ? 'selected' : '' ?>>
                                    <?= esc($c['title'] ?? ('Course #' . $c['id'])) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (isset($validation) && $validation && $validation->hasError('course_id')): ?>
                        <div class="text-danger small mt-1"><?= esc($validation->getError('course_id')) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title" class="form-control" required value="<?= esc(old('title') ?? '') ?>">
                    <?php if (isset($validation) && $validation && $validation->hasError('title')): ?>
                        <div class="text-danger small mt-1"><?= esc($validation->getError('title')) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea id="message" name="message" rows="6" class="form-control" placeholder="Optional message to your students..."><?= esc(old('message') ?? '') ?></textarea>
                    <?php if (isset($validation) && $validation && $validation->hasError('message')): ?>
                        <div class="text-danger small mt-1"><?= esc($validation->getError('message')) ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Send Announcement</button>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>



