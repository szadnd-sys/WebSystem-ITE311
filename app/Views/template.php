<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITE311</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- App Theme -->
    <link rel="stylesheet" href="<?= base_url('public/css/app.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <?= $this->include('templates/header') ?>

    <!-- Main Content -->
    <main class="flex-grow-1">
        <div class="container my-5">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-3">
        <p class="mb-0 fw-semibold">&copy; <?= date("Y"); ?> ITE311 • All rights reserved</p>
    </footer>

    <!-- jQuery (for AJAX exercises) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (session('isLoggedIn')): ?>
    <script>
    (function(){
        var badge = document.getElementById('notifBadge');
        var list = document.getElementById('notifList');
        var markBtn = document.getElementById('markAllReadBtn');
        if (!badge || !list) return;

        function updateBadge(count){
            if (count > 0) {
                badge.textContent = String(count);
                badge.classList.remove('d-none');
            } else {
                badge.textContent = '0';
                badge.classList.add('d-none');
            }
        }

        function renderItems(items){
            if (!items || !items.length) {
                list.innerHTML = '<div class="p-3 text-muted text-center small">No notifications</div>';
                return;
            }
            var html = items.map(function(n){
                var readClass = (Number(n.is_read) === 1) ? '' : 'fw-semibold';
                var title = n.title ? String(n.title) : 'Notification';
                var msg = n.message ? String(n.message) : '';
                var time = n.created_at ? String(n.created_at) : '';
                var link = n.link_url ? '<a href="'+ encodeURI(n.link_url) +'" class="stretched-link"></a>' : '';
                var actions = (Number(n.is_read) === 1)
                    ? ''
                    : '<div class="mt-2"><button class="btn btn-sm btn-outline-primary mark-read-btn" data-id="'+ String(n.id) +'">Mark as read</button></div>';
                return (
                    '<div class="list-group-item position-relative">'
                    + '<div class="small text-muted">' + time + '</div>'
                    + '<div class="'+ readClass +'">' + title + '</div>'
                    + (msg ? '<div class="small text-secondary">' + msg + '</div>' : '')
                    + actions
                    + link +
                    '</div>'
                );
            }).join('');
            list.innerHTML = html;
        }

        function fetchCount(){
            $.get('<?= base_url('notifications/unread-count') ?>', function(resp){
                if (resp && resp.success) updateBadge(resp.count || 0);
            }, 'json');
        }
        function fetchList(){
            $.get('<?= base_url('notifications/list') ?>', { limit: 10 }, function(resp){
                if (resp && resp.success) renderItems(resp.notifications || []);
            }, 'json');
        }

        // Initial
        fetchCount();
        // Poll every 20s for badge count only
        setInterval(fetchCount, 20000);

        // Load list when dropdown is opened
        var dropdown = document.getElementById('notifDropdown');
        if (dropdown) {
            dropdown.addEventListener('show.bs.dropdown', function(){
                fetchList();
            });
        }

        if (markBtn) {
            markBtn.addEventListener('click', function(){
                $.post('<?= base_url('notifications/mark-all-read') ?>', {}, function(resp){
                    fetchCount();
                    fetchList();
                }, 'json');
            });
        }

        // Delegate click for individual mark-as-read
        $(document).on('click', '.mark-read-btn', function(){
            var id = $(this).data('id');
            if (!id) return;
            $.post('<?= base_url('notifications/mark-read') ?>/' + encodeURIComponent(id), {}, function(resp){
                fetchCount();
                fetchList();
            }, 'json');
        });
    })();
    </script>
    <?php endif; ?>
</body>
</html>
