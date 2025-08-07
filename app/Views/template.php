<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Site</title>
    <!-- ✅ Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- ✅ Bootstrap Navbar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MySite</a>
        </div>
    </nav>

    <!-- ✅ Content section -->
    <div class="container mt-4">
        <?= $this->renderSection('content') ?>
    </div>

</body>
</html>
