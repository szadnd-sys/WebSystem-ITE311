<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">Settings</h1>
            <div class="text-secondary">Manage your account settings</div>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Profile Information Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('profile_success')): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            <i class="bi bi-check-circle me-1"></i> <?= esc(session()->getFlashdata('profile_success')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('settings/update-profile') ?>">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= ($validation && $validation->hasError('name')) ? 'is-invalid' : '' ?>" 
                                   id="name" name="name" value="<?= esc(old('name') ?? $user['name'] ?? '') ?>" required>
                            <?php if ($validation && $validation->hasError('name')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('name')) ?></div>
                            <?php endif; ?>
                            <div id="name-error" class="invalid-feedback" style="display: none;">
                                Full name cannot contain special characters. Only letters, spaces, hyphens, apostrophes, and periods are allowed.
                            </div>
                            <small class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i>Only letters, spaces, hyphens (-), apostrophes ('), and periods (.) are allowed.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?= ($validation && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" value="<?= esc(old('email') ?? $user['email'] ?? '') ?>" required>
                            <?php if ($validation && $validation->hasError('email')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('email')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="<?= esc(ucfirst($user['role'] ?? 'Student')) ?>" disabled>
                            <small class="form-text text-muted">Role cannot be changed. Contact administrator if needed.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Change Password</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('password_success')): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            <i class="bi bi-check-circle me-1"></i> <?= esc(session()->getFlashdata('password_success')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('settings/update-password') ?>" id="passwordForm">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control <?= ($validation && $validation->hasError('current_password')) ? 'is-invalid' : '' ?>" 
                                   id="current_password" name="current_password" required>
                            <?php if ($validation && $validation->hasError('current_password')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('current_password')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control <?= ($validation && $validation->hasError('new_password')) ? 'is-invalid' : '' ?>" 
                                   id="new_password" name="new_password" required>
                            <?php if ($validation && $validation->hasError('new_password')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('new_password')) ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Minimum 6 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control <?= ($validation && $validation->hasError('confirm_password')) ? 'is-invalid' : '' ?>" 
                                   id="confirm_password" name="confirm_password" required>
                            <?php if ($validation && $validation->hasError('confirm_password')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('confirm_password')) ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key me-1"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Account Created:</strong>
                            <div class="text-secondary"><?= esc($user['created_at'] ?? 'N/A') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Account Status:</strong>
                            <div>
                                <?php if (isset($user['is_active']) && (int)$user['is_active'] === 1): ?>
                                    <span class="badge text-bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge text-bg-danger">Deactivated</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Clear password form on successful submission
        document.getElementById('passwordForm')?.addEventListener('submit', function() {
            setTimeout(function() {
                if (document.querySelector('.alert-success')) {
                    document.getElementById('passwordForm').reset();
                }
            }, 100);
        });

        // Real-time validation for name field
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            if (nameInput) {
                const nameError = document.getElementById('name-error');
                
                // Regex pattern: only letters, spaces, hyphens, apostrophes, and periods
                const namePattern = /^[a-zA-Z\s\-\'\.]*$/;
                
                function validateName() {
                    const value = nameInput.value.trim();
                    
                    if (value === '') {
                        // Empty is okay, required validation will handle it
                        nameInput.classList.remove('is-invalid');
                        if (nameError) nameError.style.display = 'none';
                        return true;
                    }
                    
                    if (!namePattern.test(value)) {
                        // Special characters detected
                        nameInput.classList.add('is-invalid');
                        if (nameError) nameError.style.display = 'block';
                        return false;
                    } else {
                        // Valid input
                        nameInput.classList.remove('is-invalid');
                        if (nameError) nameError.style.display = 'none';
                        return true;
                    }
                }
                
                // Validate on input (as user types)
                nameInput.addEventListener('input', validateName);
                
                // Validate on blur (when field loses focus)
                nameInput.addEventListener('blur', validateName);
                
                // Validate on form submit
                const form = nameInput.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        if (!validateName()) {
                            e.preventDefault();
                            nameInput.focus();
                        }
                    });
                }
            }
        });
    </script>
<?= $this->endSection() ?>

