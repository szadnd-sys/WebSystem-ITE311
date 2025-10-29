<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 text-light">Announcements</h1>
        <a href="<?= base_url('student/dashboard') ?>" class="btn btn-outline-primary">Go to Dashboard</a>
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

    <?php if (!empty($announcements) && is_array($announcements)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>Your Announcements</strong>
                    <small class="text-muted"><?= count($announcements) ?> announcement(s)</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="list-group-item border-bottom">
                            <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                <h5 class="mb-1 fw-bold"><?= esc($announcement['title'] ?? 'Announcement') ?></h5>
                                <small class="text-muted"><?= esc($announcement['created_at'] ?? '') ?></small>
                            </div>
                            <p class="mb-2 text-dark"><?= esc($announcement['message'] ?? '') ?></p>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <small class="text-muted d-block">
                                        <strong>Course:</strong> <?= esc($announcement['course_title'] ?? 'N/A') ?>
                                    </small>
                                    <?php if (!empty($announcement['instructor_name'])): ?>
                                        <small class="text-muted d-block">
                                            <strong>From:</strong> <?= esc($announcement['instructor_name']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($announcement['material_name'])): ?>
                                    <div>
                                        <span class="badge bg-primary">
                                            <i class="bi bi-file-earmark"></i> New Material: <?= esc($announcement['material_name']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <h5 class="text-muted mb-3">No Announcements</h5>
                <p class="text-muted">You don't have any announcements at this time. Check back later for updates from your instructors.</p>
                <a href="<?= base_url('student/dashboard') ?>" class="btn btn-primary mt-3">Go to Dashboard</a>
            </div>
        </div>
    <?php endif; ?>
<?= $this->endSection() ?>


