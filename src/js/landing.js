import { supabase } from '../lib/supabase.js';
import { signIn, getCurrentUser } from '../lib/auth.js';

let currentUserId = null;

async function loadData() {
  const { data: profiles } = await supabase.from('profile').select('*').limit(1).maybeSingle();

  if (profiles) {
    currentUserId = profiles.user_id;
    document.getElementById('fullName').textContent = profiles.full_name || 'Your Name';
    document.getElementById('jobTitle').textContent = profiles.job_title || 'Professional Title';

    const imageContainer = document.getElementById('profileImage');
    if (profiles.profile_image) {
      imageContainer.innerHTML = `<img src="${profiles.profile_image}" alt="${profiles.full_name}" class="hero-image" onclick="openLoginModal()">`;
    } else {
      const initial = profiles.full_name ? profiles.full_name.charAt(0).toUpperCase() : 'R';
      imageContainer.innerHTML = `<div class="hero-image-placeholder" onclick="openLoginModal()">${initial}</div>`;
    }

    if (profiles.summary) {
      document.getElementById('summary').textContent = profiles.summary;
      document.getElementById('summarySection').style.display = 'block';
    }

    const contactGrid = document.getElementById('contactGrid');
    let contactHTML = '<div class="contact-row">';

    if (profiles.phone) {
      contactHTML += `
        <a href="tel:${profiles.phone}" class="contact-item">
          <div class="contact-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
          </div>
          <div class="contact-info">
            <div class="contact-label">Phone</div>
            <div class="contact-value">${profiles.phone}</div>
          </div>
        </a>`;
    }

    if (profiles.location) {
      contactHTML += `
        <div class="contact-item">
          <div class="contact-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
          </div>
          <div class="contact-info">
            <div class="contact-label">Location</div>
            <div class="contact-value">${profiles.location}</div>
          </div>
        </div>`;
    }

    contactHTML += '</div><div class="contact-row">';

    if (profiles.email) {
      contactHTML += `
        <a href="mailto:${profiles.email}" class="contact-item">
          <div class="contact-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
              <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
          </div>
          <div class="contact-info">
            <div class="contact-label">Email</div>
            <div class="contact-value">${profiles.email}</div>
          </div>
        </a>`;
    }

    if (profiles.linkedin_url) {
      contactHTML += `
        <a href="${profiles.linkedin_url}" target="_blank" class="contact-item">
          <div class="contact-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
              <rect x="2" y="9" width="4" height="12"></rect>
              <circle cx="4" cy="4" r="2"></circle>
            </svg>
          </div>
          <div class="contact-info">
            <div class="contact-label">LinkedIn</div>
            <div class="contact-value">View Profile</div>
          </div>
        </a>`;
    }

    if (profiles.website_url) {
      contactHTML += `
        <a href="${profiles.website_url}" target="_blank" class="contact-item">
          <div class="contact-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
          </div>
          <div class="contact-info">
            <div class="contact-label">GitHub</div>
            <div class="contact-value">View Profile</div>
          </div>
        </a>`;
    }

    contactHTML += '</div>';
    contactGrid.innerHTML = contactHTML;
  }

  const { count: expCount } = await supabase.from('experiences').select('*', { count: 'exact', head: true });
  const { count: eduCount } = await supabase.from('education').select('*', { count: 'exact', head: true });
  const { count: skillCount } = await supabase.from('skills').select('*', { count: 'exact', head: true });
  const { count: certCount } = await supabase.from('certifications').select('*', { count: 'exact', head: true });
  const { count: projectCount } = await supabase.from('projects').select('*', { count: 'exact', head: true });

  document.getElementById('expCount').textContent = expCount || 0;
  document.getElementById('eduCount').textContent = eduCount || 0;
  document.getElementById('skillCount').textContent = skillCount || 0;
  document.getElementById('certCount').textContent = certCount || 0;
  document.getElementById('projectCount').textContent = projectCount || 0;
}

window.openLoginModal = function() {
  document.getElementById('loginModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
};

window.closeLoginModal = function() {
  document.getElementById('loginModal').style.display = 'none';
  document.body.style.overflow = '';
  document.getElementById('loginError').style.display = 'none';
};

document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const errorDiv = document.getElementById('loginError');

  try {
    await signIn(email, password);
    window.location.href = '/admin/';
  } catch (error) {
    errorDiv.textContent = error.message || 'Invalid credentials';
    errorDiv.style.display = 'block';
  }
});

loadData();
