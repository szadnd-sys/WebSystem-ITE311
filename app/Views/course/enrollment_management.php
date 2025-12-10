<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 text-light">Enrollment Management</h1>
            <div class="text-secondary">Manage student enrollment requests</div>
        </div>
        <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (strpos(session()->getFlashdata('error') ?? '', 'migration') !== false): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> 
            <strong>Migration Required!</strong>
            <p class="mb-0 mt-2">Please run the migration to enable enrollment approval features:</p>
            <code class="d-block mt-2 p-2 bg-light">php spark migrate</code>
            <p class="mb-0 mt-2">Or execute the SQL script: <code>add_enrollment_approval_fields.sql</code></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Pending Enrollments Section -->
    <div class="card border-0 shadow-sm mb-4 border-warning">
        <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
            <div>
                <strong><i class="bi bi-clock-history me-2"></i>Pending Enrollment Requests</strong>
                <span class="badge bg-warning text-dark ms-2"><?= count($pendingEnrollments ?? []) ?></span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshTable()">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($pendingEnrollments) && is_array($pendingEnrollments)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" id="pendingEnrollmentsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Requested Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingEnrollments as $enrollment): ?>
                                <tr id="enrollment-row-<?= esc($enrollment['id']) ?>">
                                    <td>
                                        <strong><?= esc($enrollment['student_name'] ?? '') ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= esc($enrollment['student_email'] ?? '') ?></small>
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
                                                <i class="bi bi-check-circle me-1"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-danger reject-btn" 
                                                    data-enrollment-id="<?= esc($enrollment['id']) ?>"
                                                    data-student-name="<?= esc($enrollment['student_name'] ?? '') ?>"
                                                    data-course-title="<?= esc($enrollment['course_title'] ?? '') ?>">
                                                <i class="bi bi-x-circle me-1"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-3">No pending enrollment requests.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- All Enrollments Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <strong><i class="bi bi-list-check me-2"></i>All Enrollments</strong>
                <span class="badge bg-primary ms-2"><?= count($allEnrollments ?? []) ?></span>
            </div>
            <div class="d-flex gap-2">
                <input type="text" id="enrollmentFilter" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
                <select id="statusFilter" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($allEnrollments) && is_array($allEnrollments)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" id="allEnrollmentsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Requested Date</th>
                                <th>Rejection Reason</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allEnrollments as $enrollment): ?>
                                <tr data-status="<?= esc($enrollment['status'] ?? 'pending') ?>">
                                    <td>
                                        <strong><?= esc($enrollment['student_name'] ?? '') ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= esc($enrollment['student_email'] ?? '') ?></small>
                                    </td>
                                    <td><?= esc($enrollment['course_title'] ?? '') ?></td>
                                    <td>
                                        <?php 
                                        $status = $enrollment['status'] ?? 'pending';
                                        if ($status === 'pending'): 
                                        ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock-history me-1"></i>Pending
                                            </span>
                                        <?php elseif ($status === 'approved'): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Approved
                                            </span>
                                        <?php elseif ($status === 'rejected'): ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Rejected
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= esc($enrollment['enrollment_date'] ?? '') ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($enrollment['rejection_reason'])): ?>
                                            <small class="text-danger"><?= esc($enrollment['rejection_reason']) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($status === 'pending'): ?>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-success approve-btn" 
                                                        data-enrollment-id="<?= esc($enrollment['id']) ?>"
                                                        data-student-name="<?= esc($enrollment['student_name'] ?? '') ?>">
                                                    <i class="bi bi-check-circle me-1"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-danger reject-btn" 
                                                        data-enrollment-id="<?= esc($enrollment['id']) ?>"
                                                        data-student-name="<?= esc($enrollment['student_name'] ?? '') ?>"
                                                        data-course-title="<?= esc($enrollment['course_title'] ?? '') ?>">
                                                    <i class="bi bi-x-circle me-1"></i> Reject
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-3">No enrollments found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Enrollment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please provide a reason for rejecting this enrollment request.</p>
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectionReason" rows="3" required placeholder="Enter the reason for rejection..."></textarea>
                        <small class="form-text text-muted">This reason will be shown to the student.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRejectBtn">Reject Enrollment</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentEnrollmentId = null;
        let currentStudentName = null;
        let currentCourseTitle = null;

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
                        // Remove from pending table
                        $('#enrollment-row-' + enrollmentId).fadeOut(300, function() {
                            $(this).remove();
                            updatePendingCount();
                        });
                        
                        // Update status in all enrollments table
                        var row = $('tr[data-status="pending"]').find('[data-enrollment-id="' + enrollmentId + '"]').closest('tr');
                        row.find('td:eq(3)').html('<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>');
                        row.attr('data-status', 'approved');
                        row.find('td:last').html('<span class="text-muted">-</span>');
                        
                        alert('Enrollment approved successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to approve enrollment.'));
                        btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Approve');
                    }
                }).fail(function() {
                    alert('Error: Failed to approve enrollment. Please try again.');
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Approve');
                });
            });
            
            // Reject enrollment - open modal
            $(document).on('click', '.reject-btn', function() {
                currentEnrollmentId = $(this).data('enrollment-id');
                currentStudentName = $(this).data('student-name');
                currentCourseTitle = $(this).data('course-title');
                
                $('#rejectModal').modal('show');
                $('#rejectionReason').val('');
            });
            
            // Confirm reject
            $('#confirmRejectBtn').on('click', function() {
                var reason = $('#rejectionReason').val().trim();
                
                if (reason === '') {
                    alert('Please provide a rejection reason.');
                    return;
                }
                
                if (!confirm('Reject enrollment for ' + currentStudentName + '?')) {
                    return;
                }
                
                var btn = $('#rejectModal').find('.reject-btn').first();
                if (btn.length === 0) {
                    btn = $('[data-enrollment-id="' + currentEnrollmentId + '"].reject-btn');
                }
                
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Rejecting...');
                
                $.post('<?= base_url('course/reject-enrollment') ?>', {
                    enrollment_id: currentEnrollmentId,
                    rejection_reason: reason
                }, function(response) {
                    $('#rejectModal').modal('hide');
                    
                    if (response.success) {
                        // Remove from pending table
                        $('#enrollment-row-' + currentEnrollmentId).fadeOut(300, function() {
                            $(this).remove();
                            updatePendingCount();
                        });
                        
                        // Update status in all enrollments table
                        var row = $('tr[data-status="pending"]').find('[data-enrollment-id="' + currentEnrollmentId + '"]').closest('tr');
                        row.find('td:eq(3)').html('<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>');
                        row.attr('data-status', 'rejected');
                        row.find('td:eq(5)').html('<small class="text-danger">' + escapeHtml(reason) + '</small>');
                        row.find('td:last').html('<span class="text-muted">-</span>');
                        
                        alert('Enrollment rejected successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to reject enrollment.'));
                        btn.prop('disabled', false).html('<i class="bi bi-x-circle me-1"></i> Reject');
                    }
                }).fail(function() {
                    $('#rejectModal').modal('hide');
                    alert('Error: Failed to reject enrollment. Please try again.');
                    btn.prop('disabled', false).html('<i class="bi bi-x-circle me-1"></i> Reject');
                });
            });
            
            // Filter enrollments
            $('#enrollmentFilter, #statusFilter').on('input change', function() {
                filterEnrollments();
            });
        });
        
        function updatePendingCount() {
            var count = $('#pendingEnrollmentsTable tbody tr').length;
            $('.card-header .badge').first().text(count);
            
            if (count === 0) {
                $('#pendingEnrollmentsTable tbody').html('<tr><td colspan="5" class="text-center text-muted py-5">No pending enrollment requests.</td></tr>');
            }
        }
        
        function filterEnrollments() {
            var searchText = $('#enrollmentFilter').val().toLowerCase();
            var statusFilter = $('#statusFilter').val();
            
            $('#allEnrollmentsTable tbody tr').each(function() {
                var row = $(this);
                var studentName = row.find('td:eq(0)').text().toLowerCase();
                var email = row.find('td:eq(1)').text().toLowerCase();
                var course = row.find('td:eq(2)').text().toLowerCase();
                var status = row.attr('data-status');
                
                var matchesSearch = !searchText || 
                    studentName.indexOf(searchText) !== -1 || 
                    email.indexOf(searchText) !== -1 || 
                    course.indexOf(searchText) !== -1;
                
                var matchesStatus = !statusFilter || status === statusFilter;
                
                row.toggle(matchesSearch && matchesStatus);
            });
        }
        
        function refreshTable() {
            location.reload();
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
<?= $this->endSection() ?>

