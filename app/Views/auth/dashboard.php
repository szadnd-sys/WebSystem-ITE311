<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">Dashboard</h1>
            <div class="text-secondary">Welcome back, <span class="fw-semibold"><?= esc(session('user_name') ?: session('user_email')) ?></span></div>
        </div>
    </div>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-emoji-smile me-1"></i> You are signed in.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <?php if (session('role') === 'admin'): ?>
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 text-light mb-0">Admin Overview</h2>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-people me-1"></i> Manage Users
                </a>
            </div>

            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-people me-1"></i> Total Users</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalUsers ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-shield-lock me-1"></i> Admins</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalAdmins ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-mortarboard me-1"></i> Teachers</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalTeachers ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-person-badge me-1"></i> Students</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalStudents ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12 col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary small mb-1"><i class="bi bi-journal-bookmark me-1"></i> Courses</div>
                            <div class="display-6 fw-bold mb-0"><?= esc($totalCourses ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Recent Users</strong>
                    <div class="d-flex gap-2 align-items-center">
                    <span class="badge text-bg-secondary">Last 10</span>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right me-1"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentUsers) && is_array($recentUsers)): ?>
                                    <?php foreach ($recentUsers as $u): ?>
                                        <?php 
                                        $roleRaw = strtolower(trim((string)($u['role'] ?? 'student')));
                                        // Normalize role display: show instructor as TEACHER, handle missing roles
                                        $roleDisplay = 'STUDENT';
                                        if ($roleRaw === 'admin' || $roleRaw === 'administrator') {
                                            $roleDisplay = 'ADMIN';
                                        } elseif ($roleRaw === 'instructor' || $roleRaw === 'teacher' || $roleRaw === 'professor') {
                                            $roleDisplay = 'TEACHER';
                                        } elseif ($roleRaw === 'student') {
                                            $roleDisplay = 'STUDENT';
                                        } elseif (empty($roleRaw)) {
                                            $roleDisplay = 'N/A';
                                        } else {
                                            $roleDisplay = strtoupper($roleRaw);
                                        }
                                        ?>
                                        <tr>
                                            <td class="fw-semibold"><?= esc($u['name'] ?? '') ?></td>
                                            <td class="text-secondary small"><?= esc($u['email'] ?? '') ?></td>
                                            <td><span class="badge text-bg-light text-uppercase"><?= esc($roleDisplay) ?></span></td>
                                            <td class="small text-secondary"><?= esc($u['created_at'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent users.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Toast Notification Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div id="enrollmentToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong class="me-auto">Enrollment Successful</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div id="toastMessage"></div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Helper function to show toast notification
        function showToast(message) {
            $('#toastMessage').text(message);
            var toastElement = document.getElementById('enrollmentToast');
            var toast = new bootstrap.Toast(toastElement, { delay: 7000 });
            toast.show();
        }

        // Helper function to update notification badge - uses same method as template
        function updateNotificationBadge() {
            $.get('<?= base_url('notifications/unread-count') ?>', function(resp) {
                console.log('Notification count response:', resp);
                if (resp && resp.success) {
                    var badge = document.getElementById('notifBadge');
                    if (!badge) {
                        console.error('Badge element not found in updateNotificationBadge');
                        return;
                    }
                    
                    var count = parseInt(resp.count) || 0;
                    console.log('Updating badge with count:', count);
                    if (count > 0) {
                        badge.textContent = String(count);
                        badge.classList.remove('d-none');
                        // Remove any inline styles that might force visibility
                        badge.style.removeProperty('display');
                        badge.style.removeProperty('visibility');
                        badge.style.removeProperty('opacity');
                        console.log('Badge shown with count:', count);
                    } else {
                        // Hide badge when count is 0
                        badge.textContent = '0';
                        badge.classList.add('d-none');
                        // Force hide with inline styles to override any other styles
                        badge.style.setProperty('display', 'none', 'important');
                        badge.style.setProperty('visibility', 'hidden', 'important');
                        badge.style.setProperty('opacity', '0', 'important');
                        console.log('Badge hidden (count is 0)');
                    }
                } else {
                    console.error('Bad response from notification count API:', resp);
                    // On error, hide badge
                    var badge = document.getElementById('notifBadge');
                    if (badge) {
                        badge.textContent = '0';
                        badge.classList.add('d-none');
                        badge.style.setProperty('display', 'none', 'important');
                    }
                }
            }, 'json').fail(function(xhr, status, error) {
                console.error('Failed to fetch notification count:', status, error);
                // On error, hide badge
                var badge = document.getElementById('notifBadge');
                if (badge) {
                    badge.textContent = '0';
                    badge.classList.add('d-none');
                    badge.style.setProperty('display', 'none', 'important');
                }
            });
        }
        
        // Helper function to refresh notification list (for real-time updates)
        // This will refresh the notification dropdown list if it exists
        function refreshNotificationList() {
            var notifList = document.getElementById('notifList');
            if (!notifList) return;
            
            $.get('<?= base_url('notifications/list') ?>', { limit: 10 }, function(resp) {
                if (resp && resp.success) {
                    var items = resp.notifications || [];
                    if (!items || !items.length) {
                        notifList.innerHTML = '<div class="p-3 text-muted text-center small">No notifications</div>';
                        return;
                    }
                    var html = items.map(function(n){
                        var isRead = Number(n.is_read) === 1 || n.is_read === '1' || n.is_read === true;
                        var readClass = isRead ? '' : 'fw-semibold';
                        var title = n.title ? String(n.title) : 'Notification';
                        var msg = n.message ? String(n.message) : '';
                        var time = n.created_at ? String(n.created_at) : '';
                        var link = n.link_url ? '<a href="'+ encodeURI(n.link_url) +'" class="stretched-link"></a>' : '';
                        var markReadBtn = !isRead 
                            ? '<button type="button" class="btn btn-sm btn-outline-primary mark-read-btn" data-id="'+ String(n.id) +'">Mark as read</button>'
                            : '';
                        var deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger delete-notif-btn" data-id="'+ String(n.id) +'" title="Delete notification"><i class="bi bi-trash"></i></button>';
                        var actions = '<div class="mt-2 position-relative d-flex gap-2 align-items-center" style="z-index: 10; pointer-events: auto;">' + markReadBtn + deleteBtn + '</div>';
                        return (
                            '<div class="list-group-item position-relative" data-notif-id="'+ String(n.id) +'">' +
                            '<div class="small text-muted">' + time + '</div>' +
                            '<div class="'+ readClass +'">' + title + '</div>' +
                            (msg ? '<div class="small text-secondary">' + msg + '</div>' : '') +
                            actions +
                            link +
                            '</div>'
                        );
                    }).join('');
                    notifList.innerHTML = html;
                }
            }, 'json');
        }

        // Enroll button handler - using event delegation for dynamically added buttons
        $(document).on('click', '.enroll-btn', function(e) {
            e.preventDefault();
            var courseId = $(this).data('course-id');
            var title = $(this).data('title');
            var description = $(this).data('description') || '';
            var btn = $(this);
            var card = btn.closest('.col-md-4');

            // Disable button immediately to prevent double clicks
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Enrolling...');

            $.post('<?= base_url('course/enroll') ?>', { course_id: courseId }, function(response) {
                if (response.success) {
                    // Show real-time toast notification
                    showToast(response.message + ' Welcome aboard! Happy learning! 🎉');
                    
                    // Immediately update badge optimistically (notification should be created)
                    updateNotificationBadge();
                    
                    // Refresh notification badge and list from server after notification is created
                    setTimeout(function() {
                        updateNotificationBadge();
                        refreshNotificationList();
                    }, 500);
                    
                    // Refresh again to ensure notification appears
                    setTimeout(function() {
                        updateNotificationBadge();
                        refreshNotificationList();
                    }, 1000);
                    
                    // Final refresh to ensure everything is synced
                    setTimeout(function() {
                        updateNotificationBadge();
                        refreshNotificationList();
                    }, 2000);

                    // Get enrolled courses container
                    var enrolledContainer = $('#enrolledCoursesAccordion');
                    var enrolledCardBody = enrolledContainer.closest('.card-body');
                    
                    // If no enrolled courses section exists, create it
                    if (enrolledContainer.length === 0) {
                        var enrolledCard = $('.card-header:contains("Enrolled Courses")').closest('.card');
                        if (enrolledCard.length) {
                            enrolledCardBody = enrolledCard.find('.card-body');
                            enrolledCardBody.html('<div class="accordion" id="enrolledCoursesAccordion"></div>');
                            enrolledContainer = $('#enrolledCoursesAccordion');
                        }
                    }

                    // Add course to enrolled accordion (insert at beginning for newest first order)
                    // Use course ID for unique and stable accordion IDs
                    var headingId = 'heading' + courseId;
                    var collapseId = 'collapse' + courseId;
                    var newItem = '' +
                        '<div class="accordion-item">' +
                            '<h2 class="accordion-header" id="' + headingId + '">' +
                                '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#' + collapseId + '" aria-expanded="false">' +
                                    $('<div>').text(title).html() +
                                '</button>' +
                            '</h2>' +
                            '<div id="' + collapseId + '" class="accordion-collapse collapse" data-bs-parent="#enrolledCoursesAccordion">' +
                                '<div class="accordion-body">' +
                                    '<p class="mb-2">' + $('<div>').text(description).html() + '</p>' +
                                    '<div class="d-flex align-items-center justify-content-between mb-3">' +
                                        '<small class="d-block">Enrolled on: ' + new Date().toLocaleString() + '</small>' +
                                        '<button class="btn btn-sm btn-outline-danger drop-btn" data-course-id="' + courseId + '" data-title="' + $('<div>').text(title).html() + '" data-description="' + $('<div>').text(description).html() + '">Drop course</button>' +
                                    '</div>' +
                                    '<div class="table-responsive">' +
                                        '<table class="table table-sm table-striped align-middle">' +
                                            '<thead><tr><th>#</th><th>File Name</th><th>Uploaded</th><th class="text-end">Download</th></tr></thead>' +
                                            '<tbody><tr><td colspan="4" class="text-center text-muted">No materials yet.</td></tr></tbody>' +
                                        '</table>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                    // Insert at the beginning to match newest-first order
                    enrolledContainer.prepend(newItem);

                    // Update enrolled courses badge count
                    var badge = enrolledCardBody.prev('.card-header').find('.badge');
                    if (badge.length) {
                        var currentCount = parseInt(badge.text()) || 0;
                        badge.text(currentCount + 1);
                    }

                    // Remove the card from available courses
                    card.fadeOut(300, function() {
                        $(this).remove();
                        
                        // If no more available courses, show message
                        if ($('#availableCoursesRow .col-md-4').length === 0) {
                            $('#availableCoursesRow').remove();
                            if ($('#noAvailableMsg').length === 0) {
                                $('.card:has(.card-header:contains("Available Courses")) .card-body').html('<p id="noAvailableMsg" class="text-center text-muted">No available courses.</p>');
                            }
                        }
                    });
                } else {
                    // Re-enable button on error
                    btn.prop('disabled', false).text('Enroll');
                    
                    // Show error toast
                    var errorToast = $('#enrollmentToast');
                    errorToast.find('.toast-header').removeClass('bg-success text-white').addClass('bg-danger text-white');
                    errorToast.find('strong').text('Enrollment Failed');
                    showToast(response.message || 'Failed to enroll in the course.');
                    
                    // Reset toast styling
                    setTimeout(function() {
                        errorToast.find('.toast-header').removeClass('bg-danger text-white').addClass('bg-success text-white');
                        errorToast.find('strong').text('Enrollment Successful');
                    }, 5000);
                }
            }, 'json').fail(function() {
                // Re-enable button on failure
                btn.prop('disabled', false).text('Enroll');
                showToast('An error occurred. Please try again.');
            });
        });

        // Drop course with confirmation
        $(document).on('click', '.drop-btn', function(e){
            e.preventDefault();
            var courseId = $(this).data('course-id');
            var title = $(this).data('title');
            var description = $(this).data('description') || '';
            if (!courseId) return;
            var msg = 'Are you sure you want to drop\n"' + (title || 'this course') + '"?';
            if (!confirm(msg)) return;
            var btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Dropping...');
            
            $.post('<?= base_url('course/drop') ?>', { course_id: courseId }, function(response){
                if (response && response.success) {
                    // Get accordion item and container
                    var item = btn.closest('.accordion-item');
                    var container = item.closest('#enrolledCoursesAccordion');
                    var enrolledCardBody = container.closest('.card-body');
                    
                    // Find and collapse any open accordion collapse within this item
                    var collapseElement = item.find('.accordion-collapse');
                    if (collapseElement.length && collapseElement.hasClass('show')) {
                        // Collapse is open, hide it first
                        var bsCollapse = bootstrap.Collapse.getInstance(collapseElement[0]);
                        if (!bsCollapse) {
                            // Initialize collapse if not already initialized
                            bsCollapse = new bootstrap.Collapse(collapseElement[0], { toggle: false });
                        }
                        bsCollapse.hide();
                        // Use one-time event listener
                        collapseElement.one('hidden.bs.collapse', function() {
                            // After collapse is hidden, remove the item
                            item.fadeOut(300, function() {
                                $(this).remove();
                                cleanupAfterDrop();
                            });
                        });
                        return;
                    }
                    
                    // If collapse wasn't open, remove immediately
                    item.fadeOut(300, function() {
                        $(this).remove();
                        cleanupAfterDrop();
                    });
                    
                    function cleanupAfterDrop() {
                        // Update enrolled courses badge count
                        var badge = enrolledCardBody.prev('.card-header').find('.badge');
                        if (badge.length) {
                            var currentCount = parseInt(badge.text()) || 0;
                            if (currentCount > 0) {
                                badge.text(currentCount - 1);
                            }
                        }
                        
                        // If no more enrolled items, show empty text
                        if (container.find('.accordion-item').length === 0) {
                            enrolledCardBody.html('<p class="text-center text-muted">No enrolled courses.</p>');
                        }
                        
                        // Add back to Available Courses immediately
                        var availRow = $('#availableCoursesRow');
                        var noMsg = $('#noAvailableMsg');
                        
                        if (noMsg.length) {
                            // Replace message with a row container
                            noMsg.replaceWith('<div class="row" id="availableCoursesRow"></div>');
                            availRow = $('#availableCoursesRow');
                        }
                        
                        if (availRow.length) {
                            var cardHtml = '' +
                                '<div class="col-md-4 mb-3">' +
                                  '<div class="card h-100">' +
                                    '<div class="card-body">' +
                                      '<h5 class="card-title d-flex align-items-center gap-2"><i class="bi bi-journal-text text-primary"></i><span>' + $('<div>').text(title || '').html() + '</span></h5>' +
                                      '<p class="card-text">' + $('<div>').text(description || '').html() + '</p>' +
                                      '<button class="btn btn-primary enroll-btn" data-course-id="' + String(courseId) + '" data-title="' + $('<div>').text(title || '').html() + '" data-description="' + $('<div>').text(description || '').html() + '">Enroll</button>' +
                                    '</div>' +
                                  '</div>' +
                                '</div>';
                            availRow.prepend(cardHtml);
                            
                            // Animate the new card in
                            availRow.find('.col-md-4').first().hide().fadeIn(300);
                        }
                    }
                } else {
                    btn.prop('disabled', false).text('Drop course');
                    alert((response && response.message) ? response.message : 'Failed to drop the course.');
                }
            }, 'json').fail(function(){
                btn.prop('disabled', false).text('Drop course');
                alert('An error occurred. Please try again.');
            });
        });
    });
    </script>

    <?php if (session('role') === 'admin' && !empty($allCourses) && is_array($allCourses)): ?>
        <div class="mt-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <strong>Manage Courses</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allCourses as $c): ?>
                                    <tr>
                                        <td><?= esc($c['title'] ?? '') ?></td>
                                        <td><?= esc($c['description'] ?? '') ?></td>
                                        <td><?= esc($c['created_at'] ?? '') ?></td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-primary" href="<?= base_url('admin/course/' . esc($c['id']) . '/upload') ?>">Upload Materials</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session('role') === 'teacher'): ?>
        <div class="mt-4">
            <h2 class="h4 text-light mb-3"><i class="bi bi-mortarboard me-2"></i>Teacher Overview</h2>

            <div class="row g-3 mb-2">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-muted">Your Courses</div>
                            <div class="fs-4 fw-bold"><?= esc(is_array($courses ?? null) ? count($courses) : 0) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-muted">Recent Submissions</div>
                            <div class="fs-4 fw-bold"><?= esc(is_array($notifications ?? null) ? count($notifications) : 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <strong>Your Courses</strong>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <input type="text" id="courseFilter" class="form-control form-control-sm w-auto" placeholder="Filter courses...">
                            <button type="button" id="exportCoursesCsv" class="btn btn-sm btn-outline-secondary">Export CSV</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle" id="coursesTable">
                            <thead>
                                <tr>
                                    <th data-sort="text">Title</th>
                                    <th>Description</th>
                                    <th data-sort="date">Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($courses) && is_array($courses)): ?>
                                    <?php foreach ($courses as $c): ?>
                                        <tr>
                                            <td>
                                                <a class="text-decoration-none" href="<?= base_url('teacher/course/' . esc($c['id']) . '/students') ?>">
                                                    <?= esc($c['title'] ?? '') ?>
                                                </a>
                                            </td>
                                            <td><?= esc($c['description'] ?? '') ?></td>
                                            <td><?= esc($c['created_at'] ?? '') ?></td>
                                        <td class="text-end text-nowrap">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Course actions">
                                                <a class="btn btn-outline-secondary" href="<?= base_url('teacher/course/' . esc($c['id']) . '/students') ?>">View Students</a>
                                                <a class="btn btn-primary" href="<?= base_url('teacher/course/' . esc($c['id']) . '/upload') ?>">Upload</a>
                                                <a class="btn btn-outline-primary" href="<?= base_url('teacher/course/' . esc($c['id']) . '/announce') ?>">Announce</a>
                                            </div>
                                        </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No courses found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <strong><i class="bi bi-inbox me-2"></i>Recent Submissions</strong>
                    <button type="button" id="exportSubmissionsCsv" class="btn btn-sm btn-outline-secondary">Export CSV</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($notifications) && is_array($notifications)): ?>
                                    <?php foreach ($notifications as $n): ?>
                                        <tr>
                                            <td><?= esc($n['student_name'] ?? '') ?></td>
                                            <td><?= esc($n['course_id'] ?? '') ?></td>
                                            <td><?= esc($n['created_at'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No recent submissions.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session('role') === 'student'): ?>
        <div class="mt-4">
            <h2 class="h4 text-light mb-3"><i class="bi bi-person-badge me-2"></i>Student Overview</h2>

            

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-journal-check me-2"></i>Enrolled Courses</strong>
                    <?php $enrolledCount = !empty($enrolledCourses) && is_array($enrolledCourses) ? count($enrolledCourses) : 0; ?>
                    <span class="badge text-bg-primary fw-bold" style="font-size: 1rem; padding: 0.5rem 0.75rem;"><?= $enrolledCount ?></span>
                </div>
                <div class="card-body">
                    <?php if (!empty($enrolledCourses) && is_array($enrolledCourses)): ?>
                        <div class="accordion" id="enrolledCoursesAccordion">
                            <?php foreach ($enrolledCourses as $idx => $c): ?>
                                <?php $cid = (int)($c['id'] ?? 0); ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $cid ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $cid ?>" aria-expanded="false" aria-controls="collapse<?= $cid ?>">
                                            <?= esc($c['title'] ?? '') ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $cid ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $cid ?>" data-bs-parent="#enrolledCoursesAccordion">
                                        <div class="accordion-body">
                                            <p class="mb-2"><?= esc($c['description'] ?? '') ?></p>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <small class="d-block">Enrolled on: <?= esc($c['created_at'] ?? '') ?></small>
                                                <button class="btn btn-sm btn-outline-danger drop-btn" data-course-id="<?= $cid ?>" data-title="<?= esc($c['title'] ?? '') ?>" data-description="<?= esc($c['description'] ?? '') ?>">Drop course</button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>File Name</th>
                                                            <th>Uploaded</th>
                                                            <th class="text-end">Download</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $materialsForCourse = $materialsByCourse[$cid] ?? []; ?>
                                                        <?php if (!empty($materialsForCourse)): ?>
                                                            <?php foreach ($materialsForCourse as $mi => $m): ?>
                                                                <tr>
                                                                    <td><?= $mi + 1 ?></td>
                                                                    <td><?= esc($m['file_name'] ?? '') ?></td>
                                                                    <td><?= esc($m['created_at'] ?? '') ?></td>
                                                                    <td class="text-end">
                                                                        <a class="btn btn-sm btn-primary" href="<?= base_url('materials/download/' . esc($m['id'])) ?>">Download</a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted">No materials yet.</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted">No enrolled courses.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-journal-plus me-2"></i>Available Courses</strong>
                </div>
                <div class="card-body">
                    <!-- Search Filter -->
                    <div class="mb-3">
                        <input 
                            type="text" 
                            class="form-control" 
                            id="courseSearchFilter" 
                            placeholder="Search courses by title or description..."
                            autocomplete="off"
                        >
                        <small class="text-muted">Type to filter courses in real-time</small>
                    </div>
                    
                    <?php
                    // Fetch available courses (not enrolled by the user)
                    $db = \Config\Database::connect();
                    $user_id = session('user_id');
                    $availableCourses = $db->table('courses')
                        ->whereNotIn('id', function($builder) use ($user_id) {
                            return $builder->select('course_id')->from('enrollments')->where('user_id', $user_id);
                        })
                        ->get()
                        ->getResultArray();
                    ?>
                    <?php if (!empty($availableCourses) && is_array($availableCourses)): ?>
                        <div class="row" id="availableCoursesRow">
                            <?php foreach ($availableCourses as $course): ?>
                                <div class="col-md-4 mb-3 course-card-item" 
                                     data-search-text="<?= esc(strtolower(($course['title'] ?? '') . ' ' . ($course['description'] ?? ''))) ?>">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title d-flex align-items-center gap-2"><i class="bi bi-journal-text text-primary"></i><span><?= esc($course['title'] ?? '') ?></span></h5>
                                            <p class="card-text"><?= esc($course['description'] ?? '') ?></p>
                                            <button class="btn btn-primary enroll-btn" data-course-id="<?= esc($course['id']) ?>" data-title="<?= esc($course['title']) ?>" data-description="<?= esc($course['description']) ?>">Enroll</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div id="noCoursesFound" class="text-center text-muted" style="display: none;">
                            <p>No courses found matching your search.</p>
                        </div>
                    <?php else: ?>
                        <p id="noAvailableMsg" class="text-center text-muted">No available courses.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <script>
            $(document).ready(function() {
                $('#courseSearchFilter').on('input', function() {
                    var searchTerm = $(this).val().toLowerCase().trim();
                    var visibleCount = 0;
                    
                    $('.course-card-item').each(function() {
                        var $item = $(this);
                        var searchText = $item.attr('data-search-text') || '';
                        
                        if (searchTerm === '' || searchText.indexOf(searchTerm) !== -1) {
                            $item.show();
                            visibleCount++;
                        } else {
                            $item.hide();
                        }
                    });
                    
                    // Show/hide no results message
                    if (visibleCount === 0 && searchTerm !== '') {
                        $('#noCoursesFound').show();
                        $('#availableCoursesRow').hide();
                    } else {
                        $('#noCoursesFound').hide();
                        $('#availableCoursesRow').show();
                    }
                });
            });
            </script>

            <div class="row g-3 mb-3">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <strong><i class="bi bi-calendar-event me-2"></i>Upcoming Deadlines</strong>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Assignment</th>
                                            <th>Course</th>
                                            <th>Due</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($upcomingDeadlines) && is_array($upcomingDeadlines)): ?>
                                            <?php foreach ($upcomingDeadlines as $d): ?>
                                                <tr>
                                                    <td><?= esc($d['title'] ?? '') ?></td>
                                                    <td><?= esc($d['course_title'] ?? '') ?></td>
                                                    <td><?= esc($d['due_date'] ?? '') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No upcoming deadlines.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <strong><i class="bi bi-graph-up-arrow me-2"></i>Recent Grades</strong>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Assignment</th>
                                            <th>Course</th>
                                            <th>Score</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentGrades) && is_array($recentGrades)): ?>
                                            <?php foreach ($recentGrades as $g): ?>
                                                <tr>
                                                    <td><?= esc($g['assignment_title'] ?? '') ?></td>
                                                    <td><?= esc($g['course_title'] ?? '') ?></td>
                                                    <td><?= esc($g['score'] ?? '') ?></td>
                                                    <td><?= esc($g['created_at'] ?? '') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No recent grades.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session('role') === 'teacher'): ?>
        <script>
        (function(){
            var input = document.getElementById('courseFilter');
            if (!input) return;
            var table = document.getElementById('coursesTable');
            var tbody = table ? table.querySelector('tbody') : null;
            if (!tbody) return;
            input.addEventListener('input', function(){
                var q = this.value.toLowerCase();
                Array.prototype.forEach.call(tbody.rows, function(row){
                    var title = (row.cells[0] && row.cells[0].textContent || '').toLowerCase();
                    var desc  = (row.cells[1] && row.cells[1].textContent || '').toLowerCase();
                    var match = !q || title.indexOf(q) !== -1 || desc.indexOf(q) !== -1;
                    row.style.display = match ? '' : 'none';
                });
            });

            // simple sort by clicking on header
            var headers = table ? table.querySelectorAll('thead th[data-sort]') : [];
            Array.prototype.forEach.call(headers, function(h, idx){
                var asc = true;
                h.style.cursor = 'pointer';
                h.addEventListener('click', function(){
                    var type = h.getAttribute('data-sort');
                    var rows = Array.prototype.slice.call(tbody.rows);
                    rows.sort(function(a, b){
                        var av = (a.cells[idx] && a.cells[idx].textContent || '').trim();
                        var bv = (b.cells[idx] && b.cells[idx].textContent || '').trim();
                        if (type === 'date') {
                            var ad = Date.parse(av) || 0;
                            var bd = Date.parse(bv) || 0;
                            return asc ? ad - bd : bd - ad;
                        }
                        av = av.toLowerCase();
                        bv = bv.toLowerCase();
                        if (av < bv) return asc ? -1 : 1;
                        if (av > bv) return asc ? 1 : -1;
                        return 0;
                    });
                    // repaint
                    rows.forEach(function(r){ tbody.appendChild(r); });
                    asc = !asc;
                });
            });

            function tableToCsv(tableEl) {
                var rows = tableEl.querySelectorAll('tr');
                return Array.prototype.map.call(rows, function(row){
                    var cells = row.querySelectorAll('th,td');
                    return Array.prototype.map.call(cells, function(cell){
                        var text = (cell.textContent || '').replace(/"/g, '""');
                        return '"' + text + '"';
                    }).join(',');
                }).join('\n');
            }
            function downloadCsv(filename, csvText) {
                var blob = new Blob([csvText], { type: 'text/csv;charset=utf-8;' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }
            var exportCoursesBtn = document.getElementById('exportCoursesCsv');
            if (exportCoursesBtn && table) {
                exportCoursesBtn.addEventListener('click', function(){
                    var csv = tableToCsv(table);
                    downloadCsv('courses.csv', csv);
                });
            }
            var submissionsTable = document.querySelector('#coursesTable') ? document.querySelector('#coursesTable').closest('.mt-4').querySelectorAll('table')[1] : null;
            var exportSubmissionsBtn = document.getElementById('exportSubmissionsCsv');
            if (exportSubmissionsBtn) {
                // safer lookup for the submissions table within the second card
                var submissionsCard = exportSubmissionsBtn.closest('.card');
                var submissionsTbl = submissionsCard ? submissionsCard.querySelector('table') : null;
                exportSubmissionsBtn.addEventListener('click', function(){
                    if (!submissionsTbl) return;
                    var csv = tableToCsv(submissionsTbl);
                    downloadCsv('submissions.csv', csv);
                });
            }

            // Removed quick-upload select to avoid redundancy; per-row actions remain
        })();
        </script>
    <?php endif; ?>
<?= $this->endSection() ?>


