<?php $roleNav = strtolower((string) (session('role') ?? '')); if ($roleNav === 'instructor') { $roleNav = 'teacher'; } ?>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
    <div class="container-fluid px-3">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('/') ?>">
            <i class="bi bi-braces"></i>
            <span>ITE311</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left group: primary navigation -->
            <ul class="navbar-nav me-auto align-items-center gap-2">
                <li class="nav-item"><a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= base_url('/') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('about') ? 'active' : '' ?>" href="<?= base_url('about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('contact') ? 'active' : '' ?>" href="<?= base_url('contact') ?>">Contact</a></li>
            </ul>

            <!-- Right group: role links, notifications, auth -->
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <?php if (session('isLoggedIn')): ?>
                    <?php if ($roleNav === 'student'): ?>
                        <li class="nav-item"><a class="nav-link <?= url_is('announcements') ? 'active' : '' ?>" href="<?= base_url('announcements') ?>">Announcements</a></li>
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
                        <li class="nav-item"><a class="nav-link <?= url_is('admin/dashboard') ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <?php elseif ($roleNav === 'teacher'): ?>
                        <li class="nav-item"><a class="nav-link <?= url_is('teacher/dashboard') ? 'active' : '' ?>" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a></li>
                    <?php elseif ($roleNav === 'student'): ?>
                        <li class="nav-item"><a class="nav-link <?= url_is('student/dashboard') ? 'active' : '' ?>" href="<?= base_url('student/dashboard') ?>">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="btn btn-sm btn-light" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-1"></i> Logout</a></li>
                <?php else: ?>
                    <?php if (!url_is('login')): ?>
                        <li class="nav-item"><a class="btn btn-sm btn-light" href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


