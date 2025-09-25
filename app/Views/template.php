<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITE311</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

    <!-- Navbar -->
    <?= $this->include('templates/header') ?>

    <!-- Main Content -->
    <div class="container my-5">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Footer -->
    <footer class="bg-black text-center text-secondary py-3">
        <p>&copy; <?= date("Y"); ?> ITE311 Laboratory</p>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
