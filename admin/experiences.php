<?php
/**
 * Online Resume System - Admin Experience Management
 * CRUD for work history
 *
 * ULTRATHINK #255 - New Year's Eve Build
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Require authentication
requireAuth();

$errors = [];
$editMode = false;
$editData = null;

// Handle actions
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle delete
if ($action === 'delete' && $id > 0) {
    if (deleteExperience($id)) {
        setFlash('success', 'Experience deleted successfully.');
    } else {
        setFlash('danger', 'Failed to delete experience.');
    }
    redirect('experiences.php');
}

// Handle edit mode
if ($action === 'edit' && $id > 0) {
    $editData = getExperience($id);
    if ($editData) {
        $editMode = true;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $data = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'job_title' => trim($_POST['job_title'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?: null,
            'is_current' => isset($_POST['is_current']) ? 1 : 0,
            'description' => trim($_POST['description'] ?? ''),
            'display_order' => (int)($_POST['display_order'] ?? 0),
        ];

        // Validate
        if (empty($data['company_name'])) $errors[] = 'Company name is required.';
        if (empty($data['job_title'])) $errors[] = 'Job title is required.';
        if (empty($data['start_date'])) $errors[] = 'Start date is required.';

        // Clear end_date if current job
        if ($data['is_current']) {
            $data['end_date'] = null;
        }

        if (empty($errors)) {
            if (isset($_POST['id']) && $_POST['id'] > 0) {
                // Update
                $data['id'] = (int)$_POST['id'];
                if (updateExperience($data)) {
                    setFlash('success', 'Experience updated successfully.');
                    redirect('experiences.php');
                } else {
                    $errors[] = 'Failed to update experience.';
                }
            } else {
                // Create
                if (createExperience($data)) {
                    setFlash('success', 'Experience added successfully.');
                    redirect('experiences.php');
                } else {
                    $errors[] = 'Failed to add experience.';
                }
            }
        }
    }
}

// Get all experiences
$experiences = getExperiences();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Experience - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" onclick="toggleSidebar()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <h1 class="page-title">Work Experience</h1>
                </div>
                <div class="topbar-right">
                    <a href="logout.php" class="topbar-btn logout-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </header>

            <div class="page-content">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?= e($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Add/Edit Form -->
                <div class="form-card" style="margin-bottom: var(--space-6);">
                    <h3 class="form-section-title"><?= $editMode ? 'Edit Experience' : 'Add New Experience' ?></h3>
                    <form method="POST">
                        <?= csrfField() ?>
                        <?php if ($editMode): ?>
                            <input type="hidden" name="id" value="<?= e($editData['id']) ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Company Name *</label>
                                <input type="text" name="company_name" class="form-input" value="<?= e($editData['company_name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Job Title *</label>
                                <input type="text" name="job_title" class="form-input" value="<?= e($editData['job_title'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-input" value="<?= e($editData['location'] ?? '') ?>" placeholder="City, Country">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" class="form-input" value="<?= e($editData['display_order'] ?? 0) ?>" min="0">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Start Date *</label>
                                <input type="date" name="start_date" class="form-input" value="<?= e($editData['start_date'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-input" value="<?= e($editData['end_date'] ?? '') ?>" id="end_date">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="display: flex; align-items: center; gap: var(--space-2);">
                                <input type="checkbox" name="is_current" value="1" <?= ($editData['is_current'] ?? 0) ? 'checked' : '' ?> onchange="toggleEndDate(this)">
                                I currently work here
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-textarea" rows="4" placeholder="Describe your responsibilities and achievements..."><?= e($editData['description'] ?? '') ?></textarea>
                            <small style="color: var(--gray-500);">Use bullet points with "- " or "* " for better formatting</small>
                        </div>

                        <div class="form-actions" style="border-top: none; padding-top: 0;">
                            <?php if ($editMode): ?>
                                <a href="experiences.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary"><?= $editMode ? 'Update' : 'Add' ?> Experience</button>
                        </div>
                    </form>
                </div>

                <!-- Experiences List -->
                <div class="content-header">
                    <h3 class="content-title" style="font-size: var(--text-xl);">Your Experience (<?= count($experiences) ?>)</h3>
                </div>

                <?php if (empty($experiences)): ?>
                    <div class="card">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            <p>No work experience added yet.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Position</th>
                                    <th>Duration</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($experiences as $exp): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($exp['company_name']) ?></strong>
                                            <br><small style="color: var(--gray-500);"><?= e($exp['location'] ?? '') ?></small>
                                        </td>
                                        <td><?= e($exp['job_title']) ?></td>
                                        <td>
                                            <?= formatDateRange($exp['start_date'], $exp['end_date'], $exp['is_current']) ?>
                                            <?php if ($exp['is_current']): ?>
                                                <span class="badge badge-success" style="margin-left: var(--space-2);">Current</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= e($exp['display_order']) ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="?action=edit&id=<?= $exp['id'] ?>" class="table-btn edit" title="Edit">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </a>
                                                <a href="?action=delete&id=<?= $exp['id'] ?>" class="table-btn delete" title="Delete" onclick="return confirm('Delete this experience?')">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        function toggleEndDate(checkbox) {
            document.getElementById('end_date').disabled = checkbox.checked;
            if (checkbox.checked) document.getElementById('end_date').value = '';
        }

        // Initialize end date state
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.querySelector('input[name="is_current"]');
            if (checkbox && checkbox.checked) {
                document.getElementById('end_date').disabled = true;
            }
        });

        console.log('%c Powered by Kiyo Software TechLab', 'color: #0047AB; font-size: 14px; font-weight: bold;');
    </script>
</body>
</html>
