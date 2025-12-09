<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">Add New User</h1>
            <div class="text-secondary">Create a new user account</div>
        </div>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Users
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
            <strong>User Information</strong>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_url('admin/users/create') ?>">
                <?= csrf_field() ?>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= ($validation && $validation->hasError('name')) ? 'is-invalid' : '' ?>" 
                               id="name" name="name" value="<?= esc(old('name') ?? '') ?>" required>
                        <?php if ($validation && $validation->hasError('name')): ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('name')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control <?= ($validation && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                               id="email" name="email" value="<?= esc(old('email') ?? '') ?>" required>
                        <?php if ($validation && $validation->hasError('email')): ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('email')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control <?= ($validation && $validation->hasError('password')) ? 'is-invalid' : '' ?>" 
                               id="password" name="password" required>
                        <?php if ($validation && $validation->hasError('password')): ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('password')) ?></div>
                        <?php endif; ?>
                        <small class="form-text text-muted">Minimum 6 characters</small>
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control <?= ($validation && $validation->hasError('password_confirm')) ? 'is-invalid' : '' ?>" 
                               id="password_confirm" name="password_confirm" required>
                        <?php if ($validation && $validation->hasError('password_confirm')): ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('password_confirm')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select <?= ($validation && $validation->hasError('role')) ? 'is-invalid' : '' ?>" 
                                id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="student" <?= old('role') === 'student' ? 'selected' : '' ?>>Student</option>
                            <option value="instructor" <?= old('role') === 'instructor' ? 'selected' : '' ?>>Teacher/Instructor</option>
                        </select>
                        <?php if ($validation && $validation->hasError('role')): ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('role')) ?></div>
                        <?php endif; ?>
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>Admin role is protected and cannot be created through this interface.
                        </small>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Create User
                    </button>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>

