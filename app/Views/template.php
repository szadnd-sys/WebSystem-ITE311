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
            var badgeCount = parseInt(count) || 0;
            if (badgeCount > 0) {
                badge.textContent = String(badgeCount);
                badge.classList.remove('d-none');
                // Remove any inline styles that might force visibility
                badge.style.removeProperty('display');
                badge.style.removeProperty('visibility');
                badge.style.removeProperty('opacity');
            } else {
                // Hide badge when count is 0
                badge.textContent = '0';
                badge.classList.add('d-none');
                // Force hide with inline styles to override any other styles
                badge.style.setProperty('display', 'none', 'important');
                badge.style.setProperty('visibility', 'hidden', 'important');
                badge.style.setProperty('opacity', '0', 'important');
            }
        }

        function renderItems(items){
            if (!items || !items.length) {
                list.innerHTML = '<div class="p-3 text-muted text-center small">No notifications</div>';
                return;
            }
            var html = items.map(function(n){
                var isRead = Number(n.is_read) === 1 || n.is_read === '1' || n.is_read === true;
                var readClass = isRead ? '' : 'fw-semibold';
                var title = n.title ? String(n.title) : 'Notification';
                var msg = n.message ? String(n.message) : '';
                var time = n.created_at ? String(n.created_at) : '';
                var link = n.link_url ? '<a href="'+ encodeURI(n.link_url) +'" class="stretched-link"></a>' : '';
                // Show "Mark as read" button for unread notifications
                var markReadBtn = !isRead 
                    ? '<button type="button" class="btn btn-sm btn-outline-primary mark-read-btn" data-id="'+ String(n.id) +'">Mark as read</button>'
                    : '';
                var deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger delete-notif-btn" data-id="'+ String(n.id) +'" title="Delete notification"><i class="bi bi-trash"></i></button>';
                var actions = '<div class="mt-2 position-relative d-flex gap-2 align-items-center" style="z-index: 10; pointer-events: auto;">' + markReadBtn + deleteBtn + '</div>';
                return (
                    '<div class="list-group-item position-relative" data-notif-id="'+ String(n.id) +'">'
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
                if (resp && resp.success) {
                    var count = parseInt(resp.count) || 0;
                    updateBadge(count);
                } else {
                    updateBadge(0);
                }
            }, 'json').fail(function(){
                // On error, hide badge
                updateBadge(0);
            });
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
        var dropdownMenu = dropdown ? document.querySelector('[aria-labelledby="notifDropdown"]') : null;
        var clickTargetElement = null;
        
        if (dropdown) {
            dropdown.addEventListener('show.bs.dropdown', function(){
                fetchList();
            });
        }
        
        // Track clicks inside dropdown menu to identify button clicks
        if (dropdownMenu) {
            dropdownMenu.addEventListener('mousedown', function(e){
                // Store the clicked element if it's a button
                if (e.target.closest && (e.target.closest('.mark-read-btn') || e.target.closest('.delete-notif-btn') || e.target.closest('#markAllReadBtn') || e.target.closest('#deleteAllNotifBtn'))) {
                    clickTargetElement = e.target.closest('.mark-read-btn, .delete-notif-btn, #markAllReadBtn, #deleteAllNotifBtn');
                } else {
                    clickTargetElement = null;
                }
            });
        }
        
        // Prevent dropdown from closing when clicking mark-read or delete buttons
        if (dropdown) {
            dropdown.addEventListener('hide.bs.dropdown', function(e){
                // If we have a stored click target that's a mark-read or delete button, prevent closing
                if (clickTargetElement && (clickTargetElement.classList.contains('mark-read-btn') || clickTargetElement.classList.contains('delete-notif-btn') || clickTargetElement.id === 'markAllReadBtn' || clickTargetElement.id === 'deleteAllNotifBtn')) {
                    e.preventDefault();
                    e.stopPropagation();
                    clickTargetElement = null; // Reset after handling
                    return false;
                }
                clickTargetElement = null; // Reset
            });
        }

        if (markBtn) {
            markBtn.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                var btn = this;
                btn.disabled = true;
                btn.textContent = 'Marking all...';
                $.post('<?= base_url('notifications/mark-all-read') ?>', {}, function(resp){
                    if (resp && resp.success) {
                        // Refresh the notification list first
                        fetchList();
                        // Then update badge count after a short delay
                        setTimeout(function() {
                            fetchCount();
                        }, 200);
                    } else {
                        btn.disabled = false;
                        btn.textContent = 'Mark all read';
                        alert('Failed to mark all notifications as read. Please try again.');
                    }
                }, 'json').fail(function(){
                    btn.disabled = false;
                    btn.textContent = 'Mark all read';
                    alert('An error occurred. Please try again.');
                });
            });
        }

        // Delete all notifications button
        var deleteAllBtn = document.getElementById('deleteAllNotifBtn');
        if (deleteAllBtn) {
            deleteAllBtn.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                
                // Confirm deletion
                if (!confirm('Are you sure you want to delete ALL notifications? This action cannot be undone.')) {
                    return;
                }
                
                var btn = this;
                var originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                
                $.post('<?= base_url('notifications/delete-all') ?>', {}, function(resp){
                    if (resp && resp.success) {
                        // Clear the notification list
                        $('#notifList').html('<div class="p-3 text-muted text-center small">No notifications</div>');
                        // Update badge count
                        updateBadge(0);
                        fetchCount();
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        alert('Failed to delete all notifications. Please try again.');
                    }
                }, 'json').fail(function(){
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    alert('An error occurred. Please try again.');
                });
            });
        }

        // Delegate click for individual mark-as-read
        $(document).on('click', '.mark-read-btn', function(e){
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).data('id');
            if (!id) return;
            var btn = $(this);
            btn.prop('disabled', true).text('Marking...');
            $.post('<?= base_url('notifications/mark-read') ?>/' + encodeURIComponent(id), {}, function(resp){
                if (resp && resp.success) {
                    // Refresh the notification list first
                    fetchList();
                    // Then update badge count after a short delay to ensure server has updated
                    setTimeout(function() {
                        fetchCount();
                    }, 200);
                } else {
                    btn.prop('disabled', false).text('Mark as read');
                    alert('Failed to mark notification as read. Please try again.');
                }
            }, 'json').fail(function(){
                btn.prop('disabled', false).text('Mark as read');
                alert('An error occurred. Please try again.');
            });
        });

        // Delegate click for delete notification
        $(document).on('click', '.delete-notif-btn', function(e){
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).data('id');
            if (!id) return;
            
            // Confirm deletion
            if (!confirm('Are you sure you want to delete this notification?')) {
                return;
            }
            
            var btn = $(this);
            var notifItem = btn.closest('.list-group-item');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            
            $.ajax({
                url: '<?= base_url('notifications/delete') ?>/' + encodeURIComponent(id),
                type: 'POST',
                success: function(resp){
                    if (resp && resp.success) {
                        // Fade out and remove the notification item
                        notifItem.fadeOut(300, function(){
                            $(this).remove();
                            // Check if list is empty
                            var remainingItems = $('#notifList .list-group-item').length;
                            if (remainingItems === 0) {
                                $('#notifList').html('<div class="p-3 text-muted text-center small">No notifications</div>');
                            }
                        });
                        fetchCount();
                    } else {
                        btn.prop('disabled', false).html('<i class="bi bi-trash"></i>');
                        alert('Failed to delete notification. Please try again.');
                    }
                },
                error: function(){
                    btn.prop('disabled', false).html('<i class="bi bi-trash"></i>');
                    alert('An error occurred. Please try again.');
                }
            });
        });
    })();
    </script>
    <?php endif; ?>
</body>
</html>
