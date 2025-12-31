<?php
/**
 * Online Resume System - Admin Skills Management
 * CRUD with popup modal
 *
 * ULTRATHINK #256 - Modal Redesign
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireAuth();

$errors = [];

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'delete' && $id > 0) {
    if (deleteSkill($id)) {
        setFlash('success', 'Skill deleted successfully.');
    } else {
        setFlash('danger', 'Failed to delete skill.');
    }
    redirect('skills.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request.';
    } else {
        $data = [
            'skill_name' => trim($_POST['skill_name'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'proficiency_level' => $_POST['proficiency_level'] ?? 'Intermediate',
            'display_order' => (int)($_POST['display_order'] ?? 0),
        ];

        if (empty($data['skill_name'])) $errors[] = 'Skill name is required.';

        if (empty($errors)) {
            if (isset($_POST['id']) && $_POST['id'] > 0) {
                $data['id'] = (int)$_POST['id'];
                if (updateSkill($data)) {
                    setFlash('success', 'Skill updated successfully.');
                    redirect('skills.php');
                } else {
                    $errors[] = 'Failed to update skill.';
                }
            } else {
                if (createSkill($data)) {
                    setFlash('success', 'Skill added successfully.');
                    redirect('skills.php');
                } else {
                    $errors[] = 'Failed to add skill.';
                }
            }
        }
    }
}

$skills = getSkills();
$skillsByCategory = getSkillsByCategory();
$totalItems = count($skills);

// Check if editing
$editData = null;
if ($action === 'edit' && $id > 0) {
    $editData = getSkill($id);
}

$flash = getFlash();

$proficiencyLevels = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];
$categories = ['Programming', 'Framework', 'Database', 'Frontend', 'Backend', 'Tools', 'Soft Skills', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/dashboard.css">
    <style>
        .skill-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            padding: var(--space-2) var(--space-3);
            background: var(--gray-100);
            border-radius: var(--rounded-full);
            font-size: var(--text-sm);
            margin: var(--space-1);
        }
        .skill-badge .level {
            font-size: var(--text-xs);
            color: var(--gray-500);
        }
        .skill-badge .skill-actions {
            display: inline-flex;
            gap: var(--space-1);
            margin-left: var(--space-1);
        }
        .skill-badge .skill-actions button,
        .skill-badge .skill-actions a {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            display: inline-flex;
        }
        .category-group {
            margin-bottom: var(--space-6);
        }
        .category-title {
            font-size: var(--text-lg);
            font-weight: var(--font-semibold);
            color: var(--primary);
            margin-bottom: var(--space-3);
            padding-bottom: var(--space-2);
            border-bottom: 2px solid var(--primary);
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal {
            background: white;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: transform 0.3s;
            margin: var(--space-4);
        }
        .modal-overlay.active .modal {
            transform: scale(1);
        }
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-4) var(--space-6);
            border-bottom: 1px solid var(--gray-200);
        }
        .modal-title {
            font-size: var(--text-lg);
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
        }
        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: var(--space-2);
            color: var(--gray-500);
            border-radius: var(--radius);
            transition: background 0.2s, color 0.2s;
        }
        .modal-close:hover {
            background: var(--gray-100);
            color: var(--gray-700);
        }
        .modal-body {
            padding: var(--space-6);
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: var(--space-3);
            padding: var(--space-4) var(--space-6);
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
        }
    </style>
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
                    <h1 class="page-title">Skills</h1>
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
                        <?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="content-header">
                    <h3 class="content-title" style="font-size: var(--text-xl);">Your Skills (<?= $totalItems ?>)</h3>
                    <button class="btn btn-primary" onclick="openModal()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--space-2);">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add Skill
                    </button>
                </div>

                <?php if (empty($skills)): ?>
                    <div class="card">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            <p>No skills added yet.</p>
                            <button class="btn btn-primary" onclick="openModal()" style="margin-top: var(--space-4);">Add Your First Skill</button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
                            <div class="category-group">
                                <div class="category-title"><?= e($category) ?></div>
                                <div>
                                    <?php foreach ($categorySkills as $skill): ?>
                                        <span class="skill-badge">
                                            <?= e($skill['skill_name']) ?>
                                            <span class="level">(<?= e($skill['proficiency_level']) ?>)</span>
                                            <span class="skill-actions">
                                                <button type="button" onclick="editSkill(<?= htmlspecialchars(json_encode($skill), ENT_QUOTES, 'UTF-8') ?>)" style="color: var(--primary);" title="Edit">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </button>
                                                <a href="?action=delete&id=<?= $skill['id'] ?>" style="color: var(--danger);" onclick="return confirm('Delete this skill?')" title="Delete">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                </a>
                                            </span>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="skillModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Add Skill</h3>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form method="POST" id="skillForm">
                <div class="modal-body">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" id="skillId" value="">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Skill Name *</label>
                            <input type="text" name="skill_name" id="skillName" class="form-input" placeholder="e.g., JavaScript, Python, Communication" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">Select category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= e($cat) ?>"><?= e($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Proficiency Level</label>
                            <select name="proficiency_level" id="proficiencyLevel" class="form-select">
                                <?php foreach ($proficiencyLevels as $level): ?>
                                    <option value="<?= e($level) ?>" <?= $level === 'Intermediate' ? 'selected' : '' ?>><?= e($level) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" id="displayOrder" class="form-input" value="0" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Skill</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        function openModal() {
            document.getElementById('skillForm').reset();
            document.getElementById('skillId').value = '';
            document.getElementById('proficiencyLevel').value = 'Intermediate';
            document.getElementById('modalTitle').textContent = 'Add Skill';
            document.getElementById('submitBtn').textContent = 'Add Skill';
            document.getElementById('skillModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('skillModal').classList.remove('active');
        }

        function editSkill(data) {
            document.getElementById('skillId').value = data.id;
            document.getElementById('skillName').value = data.skill_name || '';
            document.getElementById('category').value = data.category || '';
            document.getElementById('proficiencyLevel').value = data.proficiency_level || 'Intermediate';
            document.getElementById('displayOrder').value = data.display_order || 0;

            document.getElementById('modalTitle').textContent = 'Edit Skill';
            document.getElementById('submitBtn').textContent = 'Update Skill';
            document.getElementById('skillModal').classList.add('active');
        }

        document.getElementById('skillModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        <?php if ($editData): ?>
        editSkill(<?= json_encode($editData) ?>);
        <?php endif; ?>

        console.log('%c Powered by Kiyo Software TechLab', 'color: #0047AB; font-size: 14px; font-weight: bold;');
    </script>
</body>
</html>
