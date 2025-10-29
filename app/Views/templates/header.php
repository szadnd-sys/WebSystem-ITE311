<nav class="navbar navbar-expand-lg navbar-dark bg-black">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/') ?>">ITE311</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
                <?php if (session('isLoggedIn')): ?>
                    <?php $roleNav = strtolower((string) session('role')); if ($roleNav === 'instructor') { $roleNav = 'teacher'; } ?>
                    <?php if ($roleNav === 'student'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('announcements') ?>">Announcements</a></li>
                    <?php endif; ?>

                <!-- Notifications Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="bi bi-bell"></span>
                        <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notifDropdown" style="min-width: 320px;">
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Notifications</span>
                            <button type="button" id="markAllReadBtn" class="btn btn-sm btn-outline-secondary">Mark all read</button>
                        </li>
                        <li>
                            <div id="notifList" class="list-group list-group-flush" style="max-height: 360px; overflow:auto;">
                                <div class="p-3 text-muted text-center small">Loading...</div>
                            </div>
                        </li>
                        <li class="p-2"><a class="small text-decoration-none" href="<?= base_url('announcements') ?>">View all</a></li>
                    </ul>
                </li>
                    <?php if ($roleNav === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Admin Dashboard</a></li>
                    <?php elseif ($roleNav === 'teacher'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('teacher/dashboard') ?>">Teacher Dashboard</a></li>
                    <?php elseif ($roleNav === 'student'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('student/dashboard') ?>">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                        
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


