<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-2 text-light">
                <i class="bi bi-search me-2"></i>Search Courses & Materials
            </h1>
            <p class="text-secondary mb-0">Use client-side filtering for instant results or server-side search for comprehensive database queries.</p>
        </div>
    </div>

    <!-- Search Interface -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-8">
                    <label for="searchInput" class="form-label fw-semibold">
                        <i class="bi bi-search me-1"></i>Search Query
                    </label>
                    <input 
                        type="text" 
                        class="form-control form-control-lg" 
                        id="searchInput" 
                        placeholder="Enter course title, description, or instructor name..."
                        autocomplete="off"
                    >
                </div>
                <div class="col-12 col-md-4">
                    <label for="searchType" class="form-label fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Search Type
                    </label>
                    <select class="form-select form-select-lg" id="searchType">
                        <option value="client-side">Client-Side (Instant)</option>
                        <option value="server-side">Server-Side (Comprehensive)</option>
                    </select>
                </div>
            </div>
            
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Client-Side:</strong> Filters loaded courses instantly using jQuery DOM manipulation.
                <strong>Server-Side:</strong> Searches entire database using SQL LIKE queries via AJAX.
            </div>
        </div>
    </div>

    <!-- Results Summary -->
    <div class="card border-0 shadow-sm mb-4" id="resultsSummary" style="display: none;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong id="resultsCount">0</strong> results found
                    <span class="text-muted" id="searchMode"></span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSearch">
                    <i class="bi bi-x-circle me-1"></i>Clear Search
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div class="text-center mb-4" id="loadingIndicator" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Searching database...</p>
    </div>

    <!-- Tabs for Courses and Materials -->
    <ul class="nav nav-tabs mb-3" id="searchTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab">
                <i class="bi bi-journal-bookmark me-1"></i>Courses
                <span class="badge bg-primary ms-2" id="coursesBadge"><?= count($courses ?? []) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab">
                <i class="bi bi-file-earmark me-1"></i>Materials
                <span class="badge bg-primary ms-2" id="materialsBadge">0</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="searchTabContent">
        <!-- Courses Tab -->
        <div class="tab-pane fade show active" id="courses" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div id="coursesContainer">
                        <?php if (!empty($courses) && is_array($courses)): ?>
                            <div class="row g-3 p-3" id="coursesList">
                                <?php foreach ($courses as $course): ?>
                                    <?php 
                                    $isEnrolled = in_array($course['id'], $enrolledCourseIds ?? []);
                                    ?>
                                    <div class="col-md-6 col-lg-4 course-item" 
                                         data-title="<?= esc(strtolower($course['title'] ?? '')) ?>"
                                         data-description="<?= esc(strtolower($course['description'] ?? '')) ?>"
                                         data-instructor="<?= esc(strtolower($course['instructor_name'] ?? '')) ?>">
                                        <div class="card h-100 border course-card">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <i class="bi bi-journal-text text-primary me-1"></i>
                                                    <?= esc($course['title'] ?? 'Untitled Course') ?>
                                                </h5>
                                                <p class="card-text text-muted small">
                                                    <?= esc(substr($course['description'] ?? '', 0, 100)) ?>
                                                    <?= strlen($course['description'] ?? '') > 100 ? '...' : '' ?>
                                                </p>
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>
                                                        <?= esc($course['instructor_name'] ?? 'Unknown Instructor') ?>
                                                    </small>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar me-1"></i>
                                                        <?= esc($course['created_at'] ?? '') ?>
                                                    </small>
                                                </div>
                                                <?php if ($role === 'student'): ?>
                                                    <?php if ($isEnrolled): ?>
                                                        <span class="badge bg-success mb-2">Enrolled</span>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-primary enroll-btn" 
                                                                data-course-id="<?= esc($course['id']) ?>"
                                                                data-title="<?= esc($course['title']) ?>"
                                                                data-description="<?= esc($course['description']) ?>">
                                                            <i class="bi bi-plus-circle me-1"></i>Enroll
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

        <!-- Materials Tab -->
        <div class="tab-pane fade" id="materials" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div id="materialsContainer">
                        <div class="text-center p-5">
                            <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                            <p class="text-muted mt-3">Enter a search query to find materials using server-side search.</p>
                        </div>
                    </div>
                    
                    <!-- No Results Message -->
                    <div id="noMaterialsResults" class="text-center p-5" style="display: none;">
                        <i class="bi bi-search display-1 text-muted"></i>
                        <p class="text-muted mt-3">No materials found matching your search.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var searchInput = $('#searchInput');
    var searchType = $('#searchType');
    var coursesList = $('#coursesList');
    var coursesContainer = $('#coursesContainer');
    var materialsContainer = $('#materialsContainer');
    var resultsSummary = $('#resultsSummary');
    var loadingIndicator = $('#loadingIndicator');
    var searchTimeout = null;
    
    // Store original courses HTML for client-side filtering
    var originalCoursesHtml = coursesList.html();
    var totalCourses = $('.course-item').length;
    
    // Client-side search function using jQuery
    function performClientSideSearch(query) {
        if (!query || query.trim() === '') {
            $('.course-item').show();
            $('#noCoursesResults').hide();
            $('#resultsCount').text(totalCourses);
            return;
        }
        
        var searchTerm = query.toLowerCase().trim();
        var visibleCount = 0;
        
        $('.course-item').each(function() {
            var $item = $(this);
            var title = $item.data('title') || $item.attr('data-title') || '';
            var description = $item.data('description') || $item.attr('data-description') || '';
            var instructor = $item.data('instructor') || $item.attr('data-instructor') || '';
            
            // Search in all fields using jQuery
            var matches = (title && title.indexOf(searchTerm) !== -1) ||
                         (description && description.indexOf(searchTerm) !== -1) ||
                         (instructor && instructor.indexOf(searchTerm) !== -1);
            
            if (matches) {
                $item.show();
                visibleCount++;
            } else {
                $item.hide();
            }
        });
        
        // Update UI
        if (visibleCount === 0) {
            $('#noCoursesResults').show();
        } else {
            $('#noCoursesResults').hide();
        }
        
        $('#coursesBadge').text(visibleCount);
        $('#resultsCount').text(visibleCount);
    }
    
    // Server-side search function using AJAX with jQuery
    function performServerSideSearch(query, type) {
        if (!query) {
            return;
        }

        // For very short queries (single character), fall back to client-side filtering
        // so users get immediate feedback while avoiding expensive DB calls.
        if (query.length < 2) {
            performClientSideSearch(query);
            return;
        }
        
        loadingIndicator.show();
        resultsSummary.show();
        $('#searchMode').text('(Server-side search using SQL LIKE queries)');
        
        if (type === 'courses' || type === 'all') {
            $.ajax({
                url: '<?= base_url('search/courses') ?>',
                method: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayCoursesResults(response.courses || []);
                        $('#coursesBadge').text(response.count || 0);
                        $('#resultsCount').text(response.count || 0);
                    } else {
                        showError('courses', response.message || 'Error searching courses.');
                    }
                    loadingIndicator.hide();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    showError('courses', 'An error occurred. Please try again.');
                    loadingIndicator.hide();
                }
            });
        }
        
        if (type === 'materials' || type === 'all') {
            $.ajax({
                url: '<?= base_url('search/materials') ?>',
                method: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayMaterialsResults(response.materials || []);
                        $('#materialsBadge').text(response.count || 0);
                    } else {
                        showError('materials', response.message || 'Error searching materials.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    showError('materials', 'An error occurred. Please try again.');
                }
            });
        }
    }
    
    // Display courses results from server-side search
    function displayCoursesResults(courses) {
        if (!courses || courses.length === 0) {
            coursesContainer.html('<div id="noCoursesResults" class="text-center p-5"><i class="bi bi-search display-1 text-muted"></i><p class="text-muted mt-3">No courses found matching your search.</p></div>');
            return;
        }
        
        var html = '<div class="row g-3 p-3" id="coursesList">';
        courses.forEach(function(course) {
            var isEnrolled = course.is_enrolled || false;
            html += '<div class="col-md-6 col-lg-4 course-item" data-title="' + escapeHtml(course.title || '').toLowerCase() + '" data-description="' + escapeHtml(course.description || '').toLowerCase() + '" data-instructor="' + escapeHtml(course.instructor_name || '').toLowerCase() + '">' +
                    '<div class="card h-100 border course-card">' +
                    '<div class="card-body">' +
                    '<h5 class="card-title"><i class="bi bi-journal-text text-primary me-1"></i>' + escapeHtml(course.title || 'Untitled Course') + '</h5>' +
                    '<p class="card-text text-muted small">' + escapeHtml((course.description || '').substring(0, 100)) + ((course.description || '').length > 100 ? '...' : '') + '</p>' +
                    '<div class="mb-2"><small class="text-muted"><i class="bi bi-person me-1"></i>' + escapeHtml(course.instructor_name || 'Unknown Instructor') + '</small></div>' +
                    '<div class="mb-2"><small class="text-muted"><i class="bi bi-calendar me-1"></i>' + escapeHtml(course.created_at || '') + '</small></div>';
            
            <?php if ($role === 'student'): ?>
            if (isEnrolled) {
                html += '<span class="badge bg-success mb-2">Enrolled</span>';
            } else {
                html += '<button class="btn btn-sm btn-primary enroll-btn" data-course-id="' + course.id + '" data-title="' + escapeHtml(course.title || '') + '" data-description="' + escapeHtml(course.description || '') + '"><i class="bi bi-plus-circle me-1"></i>Enroll</button>';
            }
            <?php endif; ?>
            
            html += '</div></div></div>';
        });
        html += '</div>';
        
        coursesContainer.html(html);
        initializeEnrollButtons();

        // Update counts and totalCourses after rendering server-side results
        var newCount = (courses && courses.length) ? courses.length : $('#coursesList .course-item').length;
        totalCourses = newCount;
        $('#coursesBadge').text(newCount);
        $('#resultsCount').text(newCount);
    }
    
    // Display materials results from server-side search
    function displayMaterialsResults(materials) {
        if (!materials || materials.length === 0) {
            $('#noMaterialsResults').show();
            materialsContainer.html('');
            return;
        }
        
        $('#noMaterialsResults').hide();
        var html = '<div class="table-responsive"><table class="table table-hover mb-0"><thead><tr>' +
                   '<th>File Name</th><th>Course</th><th>Uploaded</th><th class="text-end">Action</th>' +
                   '</tr></thead><tbody>';
        
        materials.forEach(function(material) {
            html += '<tr>' +
                    '<td><i class="bi bi-file-earmark me-1"></i>' + escapeHtml(material.original_name || material.file_name || 'Unknown') + '</td>' +
                    '<td>' + escapeHtml(material.course_title || 'Unknown Course') + '</td>' +
                    '<td>' + escapeHtml(material.created_at || '') + '</td>' +
                    '<td class="text-end"><a href="<?= base_url("materials/download/") ?>' + material.id + '" class="btn btn-sm btn-primary"><i class="bi bi-download me-1"></i>Download</a></td>' +
                    '</tr>';
        });
        
        html += '</tbody></table></div>';
        materialsContainer.html(html);
    }
    
    function showError(type, message) {
        var errorHtml = '<div class="alert alert-danger m-3"><i class="bi bi-exclamation-triangle me-2"></i>' + escapeHtml(message) + '</div>';
        if (type === 'courses') {
            coursesContainer.html(errorHtml);
        } else if (type === 'materials') {
            materialsContainer.html(errorHtml);
        }
    }
    
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text || '').replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Search on input
    searchInput.on('input', function() {
        var query = $(this).val().trim();
        
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        if (!query) {
            coursesList.html(originalCoursesHtml);
            initializeEnrollButtons();
            resultsSummary.hide();
            $('#noCoursesResults').hide();
            $('#coursesBadge').text(totalCourses);
            return;
        }
        
        resultsSummary.show();
        
        if (searchType.val() === 'client-side') {
            // Client-side: immediate filtering using jQuery
            performClientSideSearch(query);
            $('#searchMode').text('(Client-side filtering using jQuery DOM manipulation)');
        } else {
            // Server-side: wait for user to stop typing, then AJAX call
            $('#searchMode').text('(Server-side search using AJAX and SQL LIKE queries)');
            searchTimeout = setTimeout(function() {
                performServerSideSearch(query, 'all');
            }, 500);
        }
    });
    
    // Search type change
    searchType.on('change', function() {
        var query = searchInput.val().trim();
        if (query) {
            if ($(this).val() === 'client-side') {
                performClientSideSearch(query);
            } else {
                performServerSideSearch(query, 'all');
            }
        }
    });
    
    // Clear search
    $('#clearSearch').on('click', function() {
        searchInput.val('');
        coursesList.html(originalCoursesHtml);
        materialsContainer.html('<div class="text-center p-5"><i class="bi bi-file-earmark-text display-1 text-muted"></i><p class="text-muted mt-3">Enter a search query to find materials using server-side search.</p></div>');
        resultsSummary.hide();
        $('#noCoursesResults').hide();
        $('#noMaterialsResults').hide();
        $('#coursesBadge').text(totalCourses);
        $('#materialsBadge').text('0');
        initializeEnrollButtons();
    });
    
    // Tab change: perform search for materials if needed
    $('#materials-tab').on('shown.bs.tab', function() {
        var query = searchInput.val().trim();
        if (query && searchType.val() === 'server-side') {
            performServerSideSearch(query, 'materials');
        }
    });
    
    // Initialize enroll buttons
    function initializeEnrollButtons() {
        $('.enroll-btn').off('click').on('click', function(e) {
            e.preventDefault();
            var courseId = $(this).data('course-id');
            var title = $(this).data('title');
            var btn = $(this);
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Enrolling...');
            
            $.post('<?= base_url('course/enroll') ?>', { course_id: courseId }, function(response) {
                if (response.success) {
                    btn.replaceWith('<span class="badge bg-success">Enrolled</span>');
                    alert(response.message || 'Successfully enrolled in ' + title + '!');
                } else {
                    btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i>Enroll');
                    alert(response.message || 'Failed to enroll in the course.');
                }
            }, 'json').fail(function() {
                btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i>Enroll');
                alert('An error occurred. Please try again.');
            });
        });
    }
    
    // Initialize on page load
    initializeEnrollButtons();
    
    // If page loaded with a pre-filled query, run initial search according to selected type
    var initialQuery = searchInput.val().trim();
    if (initialQuery) {
        resultsSummary.show();
        if (searchType.val() === 'client-side') {
            // Perform client-side filtering immediately
            performClientSideSearch(initialQuery);
            $('#searchMode').text('(Client-side filtering using jQuery DOM manipulation)');
        } else {
            // Perform server-side search immediately
            $('#searchMode').text('(Server-side search using AJAX and SQL LIKE queries)');
            performServerSideSearch(initialQuery, 'all');
        }
    }
});
</script>
<?= $this->endSection() ?>
