<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-2">
    <div class="col-md-6 col-lg-5">
        <h1 class="text-center mb-4">Sign In</h1>

        <?php if (session()->getFlashdata('login_error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc(session()->getFlashdata('login_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="<?= base_url('login') ?>" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= esc(old('email')) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>

    </div>
<?php /* close container started in template via section */ ?>
<?= $this->endSection() ?>