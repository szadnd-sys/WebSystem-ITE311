<nav class="navbar navbar-expand-lg navbar-dark bg-black">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/') ?>">ITE311</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
                <?php if (session('isLoggedIn')): ?>
                    <?php $roleNav = strtolower((string) session('role')); if ($roleNav === 'instructor') { $roleNav = 'teacher'; } ?>
                    <?php if ($roleNav === 'student'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('announcements') ?>">Announcements</a></li>
                    <?php endif; ?>
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


