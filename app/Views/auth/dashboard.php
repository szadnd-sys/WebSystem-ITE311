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
                <div class="d-flex gap-2">
                    <a href="<?= base_url('admin/courses/schedule') ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-calendar-week me-1"></i> Manage Schedules
                    </a>
                    <a href="<?= base_url('admin/courses/assign') ?>" class="btn btn-info btn-sm">
                        <i class="bi bi-person-check me-1"></i> Assign Courses
                    </a>
                    <a href="<?= base_url('admin/courses') ?>" class="btn btn-success btn-sm">
                        <i class="bi bi-journal-bookmark me-1"></i> Manage Courses
                    </a>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-people me-1"></i> Manage Users
                    </a>
                </div>
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
                <strong class="me-auto">Enrollment</strong>
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
        function showToast(message, type) {
            var toast = $('#enrollmentToast');
            var header = toast.find('.toast-header');
            var icon = header.find('i');
            var strong = header.find('strong');
            
            // Reset to default
            header.removeClass('bg-success text-white bg-warning text-dark bg-danger text-white');
            icon.removeClass('bi-check-circle-fill bi-exclamation-triangle-fill bi-x-circle-fill');
            
            if (type === 'warning' || type === 'pending') {
                header.addClass('bg-warning text-dark');
                icon.addClass('bi-exclamation-triangle-fill');
                strong.text('Enrollment Request Submitted');
            } else if (type === 'error' || type === 'danger') {
                header.addClass('bg-danger text-white');
                icon.addClass('bi-x-circle-fill');
                strong.text('Enrollment Failed');
            } else {
                header.addClass('bg-success text-white');
                icon.addClass('bi-check-circle-fill');
                strong.text('Enrollment Successful');
            }
            
            $('#toastMessage').text(message);
            var toastElement = document.getElementById('enrollmentToast');
            var bsToast = new bootstrap.Toast(toastElement, { delay: 7000 });
            bsToast.show();
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

            $.post('<?= base_url('course/enroll') ?>', { course_id: courseId })
                .done(function(response) {
                    // Check if response is valid JSON
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch(e) {
                            console.error('Failed to parse response:', e);
                            showToast('An error occurred. Please try again.', 'error');
                            btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i>Enroll');
                            return;
                        }
                    }
                    
                    if (response.success) {
                    // Always show waiting for approval message for pending enrollments
                    if (response.status === 'pending') {
                        // Show warning toast with pending message
                        showToast('Your enrollment request has been submitted. Please wait for teacher approval. You will be notified once approved.', 'pending');
                        
                        // Change button to show pending status
                        btn.removeClass('btn-primary').addClass('btn-warning').prop('disabled', true);
                        btn.html('<i class="bi bi-clock-history me-1"></i>Pending Approval');
                        
                        // Reload page after 2 seconds to show in pending enrollments table
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                        return; // Don't continue with other code
                    } else {
                        // Only for approved enrollments (shouldn't happen in normal flow)
                        showToast(response.message + ' Welcome aboard! Happy learning! 🎉', 'success');
                        
                        // Add to enrolled courses only if approved
                        var enrolledContainer = $('#enrolledCoursesAccordion');
                        var enrolledCardBody = enrolledContainer.closest('.card-body');
                        
                        if (enrolledContainer.length === 0) {
                            var enrolledCard = $('.card-header:contains("Enrolled Courses")').closest('.card');
                            if (enrolledCard.length) {
                                enrolledCardBody = enrolledCard.find('.card-body');
                                enrolledCardBody.html('<div class="accordion" id="enrolledCoursesAccordion"></div>');
                                enrolledContainer = $('#enrolledCoursesAccordion');
                            }
                        }

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
                        enrolledContainer.prepend(newItem);

                        var badge = enrolledCardBody.prev('.card-header').find('.badge');
                        if (badge.length) {
                            var currentCount = parseInt(badge.text()) || 0;
                            badge.text(currentCount + 1);
                        }
                    }
                    
                    // Update notification badge (only for approved enrollments)
                    if (response.status !== 'pending') {
                        updateNotificationBadge();
                        setTimeout(function() {
                            updateNotificationBadge();
                            refreshNotificationList();
                        }, 500);
                        
                        // For approved enrollments, remove the card
                        card.fadeOut(300, function() {
                            $(this).remove();
                            
                            if ($('#availableCoursesRow .col-md-4').length === 0) {
                                $('#availableCoursesRow').remove();
                                if ($('#noAvailableMsg').length === 0) {
                                    $('.card:has(.card-header:contains("Available Courses")) .card-body').html('<p id="noAvailableMsg" class="text-center text-muted">No available courses.</p>');
                                }
                            }
                        });
                    }
                } else {
                    // Re-enable button on error
                    btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i>Enroll');
                    
                    // Show error toast
                    showToast(response.message || 'Failed to submit enrollment request. Please try again.', 'error');
                }
            }, 'json').fail(function(xhr, status, error) {
                console.error('Enrollment failed:', status, error, xhr);
                
                // Re-enable button on failure
                btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i>Enroll');
                
                var errorMsg = 'An error occurred. Please try again.';
                try {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        var errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMsg = errorResponse.message;
                        }
                    }
                } catch(e) {
                    console.error('Error parsing response:', e);
                }
                
                // Show error toast with proper styling
                showToast(errorMsg, 'error');
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

    <?php if (session('role') === 'admin'): ?>
        <div class="mt-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Quick Course Management</strong>
                    <a href="<?= base_url('admin/courses') ?>" class="btn btn-sm btn-success">
                        <i class="bi bi-arrow-right me-1"></i> View All Courses
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($allCourses) && is_array($allCourses)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Instructor</th>
                                        <th>Schedule</th>
                                        <th>Created</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($allCourses, 0, 5) as $c): ?>
                                        <tr>
                                            <td><strong><?= esc($c['title'] ?? '') ?></strong></td>
                                            <td>
                                                <?php
                                                $db = \Config\Database::connect();
                                                $instructor = $db->table('users')->select('name')->where('id', $c['instructor_id'] ?? 0)->get()->getRowArray();
                                                echo esc($instructor['name'] ?? 'Not assigned');
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($c['schedule_day']) || !empty($c['schedule_time'])): ?>
                                                    <small><?= esc($c['schedule_day'] ?? '') ?> <?= esc($c['schedule_time'] ?? '') ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">Not scheduled</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><small class="text-muted"><?= esc($c['created_at'] ?? '') ?></small></td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a class="btn btn-outline-primary" href="<?= base_url('admin/courses/edit/' . esc($c['id'])) ?>" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-outline-info" href="<?= base_url('admin/course/' . esc($c['id']) . '/students') ?>" title="Students">
                                                        <i class="bi bi-people"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted mb-3">No courses yet.</p>
                            <a href="<?= base_url('admin/courses/add') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Add Your First Course
                            </a>
                        </div>
                    <?php endif; ?>
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

            <!-- Pending Enrollments Section -->
            <?php if (!empty($pendingEnrollments) && is_array($pendingEnrollments)): ?>
            <div class="card border-0 shadow-sm mb-3 border-warning">
                <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                    <div>
                        <strong><i class="bi bi-clock-history me-2"></i>Pending Enrollment Requests</strong>
                        <span class="badge bg-warning text-dark ms-2"><?= count($pendingEnrollments) ?></span>
                    </div>
                    <a href="<?= base_url('teacher/enrollments') ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-list-check me-1"></i> View All Enrollments
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Requested</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingEnrollments as $enrollment): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong><?= esc($enrollment['student_name'] ?? '') ?></strong>
                                                <br>
                                                <small class="text-muted"><?= esc($enrollment['student_email'] ?? '') ?></small>
                                            </div>
                                        </td>
                                        <td><?= esc($enrollment['course_title'] ?? '') ?></td>
                                        <td>
                                            <small class="text-muted"><?= esc($enrollment['enrollment_date'] ?? '') ?></small>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-success approve-btn" 
                                                        data-enrollment-id="<?= esc($enrollment['id']) ?>"
                                                        data-student-name="<?= esc($enrollment['student_name'] ?? '') ?>">
                                                    <i class="bi bi-check-circle me-1"></i>Approve
                                                </button>
                                                <button type="button" class="btn btn-danger reject-btn" 
                                                        data-enrollment-id="<?= esc($enrollment['id']) ?>"
                                                        data-student-name="<?= esc($enrollment['student_name'] ?? '') ?>"
                                                        data-course-title="<?= esc($enrollment['course_title'] ?? '') ?>">
                                                    <i class="bi bi-x-circle me-1"></i>Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Show link to enrollment management even if no pending enrollments -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-list-check me-2"></i>Enrollment Management</strong>
                    <a href="<?= base_url('teacher/enrollments') ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-right me-1"></i> Manage Enrollments
                    </a>
                </div>
            </div>
            <?php endif; ?>

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

            <!-- Combined Enrollment Panel: split into two halves -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="card border-0 h-100 mb-0 border-warning">
                                <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><i class="bi bi-clock-history me-2"></i>Pending Enrollment Requests</strong>
                                        <span class="badge bg-warning text-dark ms-2"><?= !empty($pendingEnrollments) && is_array($pendingEnrollments) ? count($pendingEnrollments) : 0 ?></span>
                                    </div>
                                    <small class="text-muted">Waiting for teacher approval</small>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (!empty($pendingEnrollments) && is_array($pendingEnrollments)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0 align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Course Title</th>
                                                        <th>Description</th>
                                                        <th>Requested Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($pendingEnrollments as $enrollment): ?>
                                                        <tr>
                                                            <td><strong><?= esc($enrollment['title'] ?? '') ?></strong></td>
                                                            <td><?= esc($enrollment['description'] ?? '') ?></td>
                                                            <td><small class="text-muted"><?= esc($enrollment['created_at'] ?? '') ?></small></td>
                                                            <td>
                                                                <span class="badge bg-warning text-dark">
                                                                    <i class="bi bi-clock-history me-1"></i>Waiting for Approval
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="bi bi-inbox display-6 text-muted"></i>
                                            <p class="text-muted mt-2 mb-0">No pending enrollment requests.</p>
                                            <small class="text-muted">When you enroll in a course, it will appear here waiting for teacher approval.</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card border-0 h-100 mb-0">
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
                                                                <div>
                                                                    <small class="d-block">Enrolled on: <?= esc($c['created_at'] ?? '') ?></small>
                                                                    <?php if (isset($c['status'])): ?>
                                                                        <?php if ($c['status'] === 'pending'): ?>
                                                                            <span class="badge bg-warning text-dark mt-1">
                                                                                <i class="bi bi-clock-history me-1"></i>Pending Approval
                                                                            </span>
                                                                        <?php elseif ($c['status'] === 'rejected'): ?>
                                                                            <span class="badge bg-danger mt-1">
                                                                                <i class="bi bi-x-circle me-1"></i>Rejected
                                                                            </span>
                                                                        <?php elseif ($c['status'] === 'approved'): ?>
                                                                            <span class="badge bg-success mt-1">
                                                                                <i class="bi bi-check-circle me-1"></i>Approved
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <?php if (isset($c['status']) && $c['status'] === 'approved'): ?>
                                                                    <button class="btn btn-sm btn-outline-danger drop-btn" data-course-id="<?= $cid ?>" data-title="<?= esc($c['title'] ?? '') ?>" data-description="<?= esc($c['description'] ?? '') ?>">Drop course</button>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if (isset($c['status']) && $c['status'] === 'rejected' && !empty($c['rejection_reason'])): ?>
                                                                <div class="alert alert-danger alert-sm mb-3">
                                                                    <strong><i class="bi bi-exclamation-triangle me-1"></i>Rejection Reason:</strong>
                                                                    <p class="mb-0 mt-1"><?= esc($c['rejection_reason']) ?></p>
                                                                </div>
                                                            <?php endif; ?>
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
                                        <p class="text-center text-muted">No approved enrollments yet. Your enrollment requests will appear here once approved by the teacher.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    // Fetch available courses (not enrolled by the user - exclude approved enrollments only)
                    // Pending enrollments can still be shown in available courses, but button will be disabled
                    $db = \Config\Database::connect();
                    $user_id = session('user_id');
                    
                    // Get all course IDs where user has any enrollment (pending, approved, or rejected)
                    $allEnrolledCourseIds = $db->table('enrollments')
                        ->select('course_id')
                        ->where('user_id', $user_id)
                        ->get()
                        ->getResultArray();
                    $enrolledCourseIds = array_column($allEnrolledCourseIds, 'course_id');
                    
                    // Get available courses (not in any enrollment status)
                    $availableCourses = [];
                    if (empty($enrolledCourseIds)) {
                        $availableCourses = $db->table('courses')->get()->getResultArray();
                    } else {
                        $availableCourses = $db->table('courses')
                            ->whereNotIn('id', $enrolledCourseIds)
                            ->get()
                            ->getResultArray();
                    }
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

        <!-- Enrollment Approval Script -->
        <script>
        $(document).ready(function() {
            // Approve enrollment
            $(document).on('click', '.approve-btn', function() {
                var btn = $(this);
                var enrollmentId = btn.data('enrollment-id');
                var studentName = btn.data('student-name');
                
                if (!confirm('Approve enrollment for ' + studentName + '?')) {
                    return;
                }
                
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Approving...');
                
                $.post('<?= base_url('course/approve-enrollment') ?>', {
                    enrollment_id: enrollmentId
                }, function(response) {
                    if (response.success) {
                        // Remove the row
                        btn.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                            // Check if table is empty
                            var tbody = btn.closest('tbody');
                            if (tbody.find('tr').length === 0) {
                                tbody.html('<tr><td colspan="4" class="text-center text-muted">No pending enrollments.</td></tr>');
                            }
                        });
                        alert('Enrollment approved successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to approve enrollment.'));
                        btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Approve');
                    }
                }).fail(function() {
                    alert('Error: Failed to approve enrollment. Please try again.');
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Approve');
                });
            });
            
            // Reject enrollment
            $(document).on('click', '.reject-btn', function() {
                var btn = $(this);
                var enrollmentId = btn.data('enrollment-id');
                var studentName = btn.data('student-name');
                var courseTitle = btn.data('course-title');
                
                var reason = prompt('Please provide a reason for rejecting ' + studentName + '\'s enrollment in ' + courseTitle + ':\n\n(Reason is required)');
                
                if (reason === null) {
                    return; // User cancelled
                }
                
                reason = reason.trim();
                if (reason === '') {
                    alert('Rejection reason is required.');
                    return;
                }
                
                if (!confirm('Reject enrollment for ' + studentName + '?')) {
                    return;
                }
                
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Rejecting...');
                
                $.post('<?= base_url('course/reject-enrollment') ?>', {
                    enrollment_id: enrollmentId,
                    rejection_reason: reason
                }, function(response) {
                    if (response.success) {
                        // Remove the row
                        btn.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                            // Check if table is empty
                            var tbody = btn.closest('tbody');
                            if (tbody.find('tr').length === 0) {
                                tbody.html('<tr><td colspan="4" class="text-center text-muted">No pending enrollments.</td></tr>');
                            }
                        });
                        alert('Enrollment rejected successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to reject enrollment.'));
                        btn.prop('disabled', false).html('<i class="bi bi-x-circle me-1"></i>Reject');
                    }
                }).fail(function() {
                    alert('Error: Failed to reject enrollment. Please try again.');
                    btn.prop('disabled', false).html('<i class="bi bi-x-circle me-1"></i>Reject');
                });
            });
        });
        </script>
    <?php endif; ?>
<?= $this->endSection() ?>


