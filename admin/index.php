<?php
/**
 * Online Resume System - Admin Dashboard Home
 * Overview with statistics cards
 *
 * ULTRATHINK #255 - New Year's Eve Build
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Require authentication
requireAuth();

// Get dashboard statistics
$stats = getDashboardStats();
$profile = getProfile();

// Get flash message
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <main class="main-content">
            <!-- Top Bar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" onclick="toggleSidebar()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <h1 class="page-title">Dashboard</h1>
                </div>
                <div class="topbar-right">
                    <a href="../resume.php" class="topbar-btn" target="_blank">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span>View Resume</span>
                    </a>
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

            <!-- Page Content -->
            <div class="page-content">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>">
                        <?= e($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <!-- Welcome Message -->
                <div class="content-header">
                    <div>
                        <h2 class="content-title">Welcome back<?= $profile ? ', ' . e($profile['full_name']) : '' ?>!</h2>
                        <p class="text-gray" style="margin: 0;">Here's an overview of your resume content.</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $stats['experiences'] ?></div>
                            <div class="stat-label">Work Experiences</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon success">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $stats['education'] ?></div>
                            <div class="stat-label">Education</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $stats['skills'] ?></div>
                            <div class="stat-label">Skills</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon info">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="8" r="7"></circle>
                                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $stats['certifications'] ?></div>
                            <div class="stat-label">Certifications</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $stats['projects'] ?></div>
                            <div class="stat-label">Projects</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="content-header" style="margin-top: var(--space-8);">
                    <h3 class="content-title" style="font-size: var(--text-xl);">Quick Actions</h3>
                </div>

                <div class="stats-grid">
                    <a href="profile.php" class="card" style="text-decoration: none; display: block;">
                        <h4 style="margin-bottom: var(--space-2); color: var(--gray-900);">Edit Profile</h4>
                        <p style="color: var(--gray-500); font-size: var(--text-sm); margin: 0;">Update your personal information and summary</p>
                    </a>
                    <a href="experiences.php" class="card" style="text-decoration: none; display: block;">
                        <h4 style="margin-bottom: var(--space-2); color: var(--gray-900);">Add Experience</h4>
                        <p style="color: var(--gray-500); font-size: var(--text-sm); margin: 0;">Add new work experience to your resume</p>
                    </a>
                    <a href="skills.php" class="card" style="text-decoration: none; display: block;">
                        <h4 style="margin-bottom: var(--space-2); color: var(--gray-900);">Manage Skills</h4>
                        <p style="color: var(--gray-500); font-size: var(--text-sm); margin: 0;">Add or update your professional skills</p>
                    </a>
                    <a href="../resume.php" target="_blank" class="card" style="text-decoration: none; display: block;">
                        <h4 style="margin-bottom: var(--space-2); color: var(--gray-900);">Preview Resume</h4>
                        <p style="color: var(--gray-500); font-size: var(--text-sm); margin: 0;">View your resume and save as PDF</p>
                    </a>
                </div>

                <!-- Profile Summary -->
                <?php if ($profile): ?>
                <div class="content-header" style="margin-top: var(--space-8);">
                    <h3 class="content-title" style="font-size: var(--text-xl);">Profile Summary</h3>
                    <a href="profile.php" class="btn btn-outline btn-sm">Edit Profile</a>
                </div>

                <div class="card">
                    <div class="flex items-start gap-6" style="flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 250px;">
                            <h4 style="margin-bottom: var(--space-1);"><?= e($profile['full_name']) ?></h4>
                            <p style="color: var(--primary); margin-bottom: var(--space-2);"><?= e($profile['job_title'] ?? 'No job title set') ?></p>
                            <p style="color: var(--gray-500); font-size: var(--text-sm); margin: 0;">
                                <?= e($profile['location'] ?? 'No location set') ?>
                            </p>
                        </div>
                        <div style="flex: 2; min-width: 300px;">
                            <p style="color: var(--gray-700); line-height: var(--leading-relaxed); margin: 0;">
                                <?= e($profile['summary'] ?? 'No professional summary added yet. Click "Edit Profile" to add one.') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuBtn = document.querySelector('.mobile-menu-btn');

            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(e.target) && !menuBtn.contains(e.target) && sidebar.classList.contains('open')) {
                    toggleSidebar();
                }
            }
        });

        // Easter egg
        console.log('%c Powered by Kiyo Software TechLab', 'color: #0047AB; font-size: 14px; font-weight: bold;');
    </script>
</body>
</html>
