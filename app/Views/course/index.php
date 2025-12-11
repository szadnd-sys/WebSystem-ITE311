<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-2 text-light">
                <i class="bi bi-journal-text me-2"></i>Course Listing
            </h1>
            <p class="text-secondary mb-0">Browse all available courses. Type in the search box for instant filtering.</p>
        </div>
    </div>

    <!-- Search Interface -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label for="courseSearchInput" class="form-label fw-semibold">
                        <i class="bi bi-search me-1"></i>Search Courses
                    </label>
                    <input 
                        type="text" 
                        class="form-control form-control-lg" 
                        id="courseSearchInput" 
                        placeholder="Enter course title, description, or instructor name..."
                        value="<?= esc($search ?? '') ?>"
                        autocomplete="off"
                    >
                    <small class="text-muted">Type to filter courses in real-time</small>
                </div>
                <div class="col-12 col-md-4">
                    <label for="sortSelect" class="form-label fw-semibold">
                        <i class="bi bi-sort-down me-1"></i>Sort By
                    </label>
                    <select id="sortSelect" class="form-select form-select-lg">
                        <option value="recent" selected>Most Recent</option>
                        <option value="title">Title (A-Z)</option>
                        <option value="instructor">Instructor Name</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Display -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Available Courses</strong>
            <span class="badge bg-primary" id="courseCount"><?= count($courses ?? []) ?> courses</span>
        </div>
        <div class="card-body">
            <!-- Courses Container -->
            <div id="coursesContainer">
                <?php if (!empty($courses) && is_array($courses)): ?>
                    <div class="row g-3" id="coursesList">
                        <?php foreach ($courses as $course): ?>
                            <?php 
                            $isEnrolled = in_array($course['id'], $enrolledCourseIds ?? []);
                            ?>
                            <div class="col-md-6 col-lg-4 course-item" 
                                 data-course-id="<?= esc($course['id']) ?>"
                                 data-title="<?= esc(strtolower($course['title'] ?? '')) ?>"
                                 data-description="<?= esc(strtolower($course['description'] ?? '')) ?>"
                                 data-instructor="<?= esc(strtolower($course['instructor_name'] ?? '')) ?>"
                                 data-created="<?= esc($course['created_at'] ?? '') ?>">
                                <div class="card h-100 border-0 shadow-sm course-card <?= $isEnrolled ? 'border-2 border-success' : '' ?>">
                                    <div class="card-body">
                                        <?php if ($isEnrolled): ?>
                                            <div class="badge bg-success mb-2">
                                                <i class="bi bi-check-circle me-1"></i>Enrolled
                                            </div>
                                        <?php endif; ?>
                                        <h5 class="card-title">
                                            <i class="bi bi-journal-text text-primary me-1"></i>
                                            <?= esc($course['title'] ?? 'Untitled Course') ?>
                                        </h5>
                                        <p class="card-text text-muted small">
                                            <?= esc(strlen($course['description'] ?? '') > 100 ? substr($course['description'], 0, 100) . '...' : ($course['description'] ?? 'No description')) ?>
                                        </p>
                                        <hr class="my-2">
                                        <div class="small">
                                            <div class="mb-2">
                                                <strong>Instructor:</strong><br>
                                                <?php if (!empty($course['instructor_name'])): ?>
                                                    <i class="bi bi-person me-1"></i><?= esc($course['instructor_name']) ?>
                                                    <?php if (!empty($course['instructor_email'])): ?>
                                                        <br><small class="text-muted"><?= esc($course['instructor_email']) ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-danger">No instructor assigned</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($course['created_at'] ?? now())) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-top">
                                        <?php if ($role === 'student'): ?>
                                            <?php if ($isEnrolled): ?>
                                                <button class="btn btn-success btn-sm w-100 enrolled-btn" disabled>
                                                    <i class="bi bi-check-circle me-1"></i>Already Enrolled
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-primary btn-sm w-100 enroll-btn" 
                                                        data-course-id="<?= esc($course['id']) ?>"
                                                        data-title="<?= esc($course['title']) ?>">
                                                    <i class="bi bi-plus-circle me-1"></i>Enroll Now
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center p-5">
                        <i class="bi bi-journal-x display-1 text-muted"></i>
                        <p class="text-muted mt-3">No courses available.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- No Results Message -->
            <div id="noCoursesResults" class="text-center p-5" style="display: none;">
                <i class="bi bi-search display-1 text-muted"></i>
                <p class="text-muted mt-3">No courses found matching your search.</p>
            </div>
        </div>
    </div>
</div>

<!-- Enroll Modal (if needed) -->
<div class="modal fade" id="enrollModal" tabindex="-1" aria-labelledby="enrollModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollModalLabel">Confirm Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to enroll in <strong id="enrollCourseName"></strong>?</p>
                <p class="small text-muted">Your enrollment request will be submitted and will require teacher approval.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmEnrollBtn">Enroll</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const courseSearchInput = $('#courseSearchInput');
    const sortSelect = $('#sortSelect');
    const coursesList = $('#coursesList');
    const courseCount = $('#courseCount');
    const noCoursesResults = $('#noCoursesResults');
    const coursesContainer = $('#coursesContainer');

    // Client-side filtering function
    function filterCourses() {
        const searchTerm = courseSearchInput.val().toLowerCase().trim();
        let visibleCount = 0;

        $('.course-item').each(function() {
            const title = $(this).data('title');
            const description = $(this).data('description');
            const instructor = $(this).data('instructor');

            const matches = title.includes(searchTerm) || 
                          description.includes(searchTerm) || 
                          instructor.includes(searchTerm) ||
                          searchTerm === '';

            if (matches && searchTerm === '') {
                $(this).fadeIn(200);
                visibleCount++;
            } else if (matches && searchTerm !== '') {
                $(this).fadeIn(200);
                visibleCount++;
            } else {
                $(this).fadeOut(200);
            }
        });

        // Update course count
        courseCount.text(visibleCount + ' course' + (visibleCount !== 1 ? 's' : ''));

        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '') {
            coursesList.hide();
            noCoursesResults.show();
        } else {
            coursesList.show();
            noCoursesResults.hide();
        }
    }

    // Sort function
    function sortCourses() {
        const sortBy = sortSelect.val();
        const courseItems = $('.course-item').get();

        courseItems.sort(function(a, b) {
            if (sortBy === 'title') {
                const titleA = $(a).data('title');
                const titleB = $(b).data('title');
                return titleA.localeCompare(titleB);
            } else if (sortBy === 'instructor') {
                const instructorA = $(a).data('instructor');
                const instructorB = $(b).data('instructor');
                return instructorA.localeCompare(instructorB);
            } else if (sortBy === 'recent') {
                const dateA = new Date($(a).data('created'));
                const dateB = new Date($(b).data('created'));
                return dateB - dateA;
            }
            return 0;
        });

        coursesList.html('');
        $(courseItems).each(function() {
            coursesList.append($(this));
        });
    }

    // Event listeners for client-side filtering
    courseSearchInput.on('input', filterCourses);
    sortSelect.on('change', function() {
        sortCourses();
        filterCourses();
    });

    // Enroll button click handler
    $(document).on('click', '.enroll-btn', function(e) {
        e.preventDefault();
        const courseId = $(this).data('course-id');
        const courseTitle = $(this).data('title');

        $('#enrollCourseName').text(courseTitle);
        
        const modal = new bootstrap.Modal(document.getElementById('enrollModal'));
        modal.show();

        $('#confirmEnrollBtn').off('click').on('click', function() {
            enrollCourse(courseId, courseTitle);
            modal.hide();
        });
    });

    // Enroll course function
    function enrollCourse(courseId, courseTitle) {
        const btn = $('button[data-course-id="' + courseId + '"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Enrolling...');

        $.ajax({
            url: '<?= base_url('course/enroll') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                course_id: courseId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="bi bi-check-circle me-1"></i>' + response.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';
                    $('form').before(alertHtml);

                    // Update button UI
                    const courseItem = $('[data-course-id="' + courseId + '"]').closest('.course-item');
                    courseItem.find('.card-footer').html(
                        '<button class="btn btn-success btn-sm w-100 enrolled-btn" disabled>' +
                        '<i class="bi bi-check-circle me-1"></i>Already Enrolled' +
                        '</button>'
                    );

                    // Add enrolled badge if not present
                    if (!courseItem.find('.badge-success').length) {
                        courseItem.find('.card-body').prepend(
                            '<div class="badge bg-success mb-2">' +
                            '<i class="bi bi-check-circle me-1"></i>Enrolled' +
                            '</div>'
                        );
                    }
                } else {
                    alert('Error: ' + response.message);
                    btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i>Enroll Now');
                }
            },
            error: function(xhr, status, error) {
                console.error('Enrollment error:', error);
                alert('An error occurred. Please try again.');
                btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i>Enroll Now');
            }
        });
    }

    // Initial load - trigger filter if search term exists in URL
    if (courseSearchInput.val()) {
        filterCourses();
    }
});
</script>
<?= $this->endSection() ?>
