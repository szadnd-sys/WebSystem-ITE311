<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">User Management</h1>
            <div class="text-secondary">Manage all system users</div>
        </div>
        <a href="<?= base_url('admin/users/add') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New User
        </a>
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

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-shield-lock me-1"></i> <strong>Admin Protection:</strong> Admin users are protected and cannot be edited, deleted, or have their roles changed through this interface.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Search and Filter Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/users') ?>" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Search by name or email..." value="<?= esc($search ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="role" class="form-label">Filter by Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">All Roles</option>
                        <option value="admin" <?= ($roleFilter ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="teacher" <?= ($roleFilter ?? '') === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                        <option value="student" <?= ($roleFilter ?? '') === 'student' ? 'selected' : '' ?>>Student</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>All Users</strong>
            <span class="badge text-bg-secondary"><?= count($users ?? []) ?> users</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users) && is_array($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <?php 
                                // Check if user is admin (protected role)
                                $roleRaw = strtolower(trim((string)($user['role'] ?? 'student')));
                                $isAdmin = ($roleRaw === 'admin' || $roleRaw === 'administrator');
                                
                                // Check account status (default to active if field doesn't exist)
                                $isActive = isset($user['is_active']) ? (int)$user['is_active'] : 1;
                                ?>
                                <tr class="<?= $isActive === 0 ? 'table-secondary opacity-75' : '' ?>">
                                    <td class="text-secondary">#<?= esc($user['id'] ?? '') ?></td>
                                    <td class="fw-semibold">
                                        <?= esc($user['name'] ?? '') ?>
                                        <?php if ($isAdmin): ?>
                                            <i class="bi bi-shield-lock-fill text-danger ms-1" title="Protected Admin Account"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-secondary"><?= esc($user['email'] ?? '') ?></td>
                                    <td>
                                        <?php
                                        $roleBadgeClass = 'text-bg-secondary';
                                        $roleDisplay = $user['role_display'] ?? 'STUDENT';
                                        if ($roleDisplay === 'ADMIN') {
                                            $roleBadgeClass = 'text-bg-danger';
                                        } elseif ($roleDisplay === 'TEACHER') {
                                            $roleBadgeClass = 'text-bg-primary';
                                        } else {
                                            $roleBadgeClass = 'text-bg-success';
                                        }
                                        ?>
                                        <span class="badge <?= $roleBadgeClass ?> text-uppercase"><?= esc($roleDisplay) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($isActive === 1): ?>
                                            <span class="badge text-bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Active
                                            </span>
                                        <?php else: ?>
                                            <span class="badge text-bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Deactivated
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-secondary"><?= esc($user['created_at'] ?? 'N/A') ?></td>
                                    <td class="text-end">
                                        <?php if ($isAdmin): ?>
                                            <span class="text-muted small">
                                                <i class="bi bi-lock-fill me-1"></i>Protected
                                            </span>
                                        <?php else: ?>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" 
                                                   class="btn btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if ($isActive === 1): ?>
                                                    <a href="<?= base_url('admin/users/deactivate/' . $user['id']) ?>" 
                                                       class="btn btn-outline-warning" 
                                                       title="Deactivate"
                                                       onclick="return confirm('Are you sure you want to deactivate this user? They will not be able to login.');">
                                                        <i class="bi bi-pause-circle"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url('admin/users/activate/' . $user['id']) ?>" 
                                                       class="btn btn-outline-success" 
                                                       title="Activate">
                                                        <i class="bi bi-play-circle"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($user['id'] != session('user_id')): ?>
                                                    <a href="<?= base_url('admin/users/delete/' . $user['id']) ?>" 
                                                       class="btn btn-outline-danger" 
                                                       title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-outline-secondary" disabled title="Cannot delete your own account">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox me-2"></i>No users found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

