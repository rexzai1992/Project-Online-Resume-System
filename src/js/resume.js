import { supabase } from '../lib/supabase.js';

function formatDate(dateStr) {
  if (!dateStr) return 'Present';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
}

async function loadResume() {
  const { data: profile } = await supabase.from('profile').select('*').limit(1).maybeSingle();

  if (profile) {
    document.getElementById('fullName').textContent = profile.full_name || 'Your Name';
    document.getElementById('jobTitle').textContent = profile.job_title || 'Professional Title';

    if (profile.profile_image) {
      document.getElementById('profileImageContainer').innerHTML =
        `<img src="${profile.profile_image}" alt="${profile.full_name}" class="resume-profile-img">`;
    }

    let contactHTML = '';
    if (profile.email) contactHTML += `<div>${profile.email}</div>`;
    if (profile.phone) contactHTML += `<div>${profile.phone}</div>`;
    if (profile.location) contactHTML += `<div>${profile.location}</div>`;
    if (profile.linkedin_url) contactHTML += `<div><a href="${profile.linkedin_url}" target="_blank">LinkedIn</a></div>`;
    if (profile.website_url) contactHTML += `<div><a href="${profile.website_url}" target="_blank">Website</a></div>`;
    document.getElementById('contactInfo').innerHTML = contactHTML;

    if (profile.summary) {
      document.getElementById('summary').textContent = profile.summary;
      document.getElementById('summarySection').style.display = 'block';
    }
  }

  const { data: experiences } = await supabase.from('experiences').select('*').order('display_order', { ascending: true });
  if (experiences && experiences.length > 0) {
    const expHTML = experiences.map(exp => `
      <div class="resume-entry">
        <div class="resume-entry-header">
          <div>
            <h3 class="resume-entry-title">${exp.job_title}</h3>
            <p class="resume-entry-subtitle">${exp.company_name}${exp.location ? ' - ' + exp.location : ''}</p>
          </div>
          <div class="resume-entry-date">${formatDate(exp.start_date)} - ${exp.is_current ? 'Present' : formatDate(exp.end_date)}</div>
        </div>
        ${exp.description ? `<p class="resume-entry-description">${exp.description.replace(/\n/g, '<br>')}</p>` : ''}
      </div>
    `).join('');
    document.getElementById('experiencesList').innerHTML = expHTML;
    document.getElementById('experiencesSection').style.display = 'block';
  }

  const { data: education } = await supabase.from('education').select('*').order('display_order', { ascending: true });
  if (education && education.length > 0) {
    const eduHTML = education.map(edu => `
      <div class="resume-entry">
        <div class="resume-entry-header">
          <div>
            <h3 class="resume-entry-title">${edu.degree}${edu.field_of_study ? ' in ' + edu.field_of_study : ''}</h3>
            <p class="resume-entry-subtitle">${edu.institution}${edu.location ? ' - ' + edu.location : ''}</p>
          </div>
          <div class="resume-entry-date">${formatDate(edu.start_date)} - ${formatDate(edu.end_date)}</div>
        </div>
        ${edu.description ? `<p class="resume-entry-description">${edu.description}</p>` : ''}
      </div>
    `).join('');
    document.getElementById('educationList').innerHTML = eduHTML;
    document.getElementById('educationSection').style.display = 'block';
  }

  const { data: skills } = await supabase.from('skills').select('*').order('display_order', { ascending: true });
  if (skills && skills.length > 0) {
    const skillsByCategory = skills.reduce((acc, skill) => {
      const cat = skill.category || 'Other';
      if (!acc[cat]) acc[cat] = [];
      acc[cat].push(skill);
      return acc;
    }, {});

    const skillsHTML = Object.entries(skillsByCategory).map(([category, categorySkills]) => `
      <div class="skills-category">
        <h3 class="skills-category-title">${category}</h3>
        <div class="skills-list">
          ${categorySkills.map(skill => `<span class="skill-badge">${skill.skill_name}</span>`).join('')}
        </div>
      </div>
    `).join('');

    document.getElementById('skillsList').innerHTML = skillsHTML;
    document.getElementById('skillsSection').style.display = 'block';
  }

  const { data: certifications } = await supabase.from('certifications').select('*').order('display_order', { ascending: true });
  if (certifications && certifications.length > 0) {
    const certHTML = certifications.map(cert => `
      <div class="resume-entry">
        <div class="resume-entry-header">
          <div>
            <h3 class="resume-entry-title">${cert.cert_name}</h3>
            <p class="resume-entry-subtitle">${cert.issuing_org}</p>
          </div>
          <div class="resume-entry-date">${cert.issue_date ? formatDate(cert.issue_date) : ''}</div>
        </div>
        ${cert.credential_url ? `<p><a href="${cert.credential_url}" target="_blank">View Credential</a></p>` : ''}
      </div>
    `).join('');
    document.getElementById('certificationsList').innerHTML = certHTML;
    document.getElementById('certificationsSection').style.display = 'block';
  }

  const { data: projects } = await supabase.from('projects').select('*').order('display_order', { ascending: true });
  if (projects && projects.length > 0) {
    const projectsHTML = projects.map(project => `
      <div class="resume-entry">
        <div class="resume-entry-header">
          <div>
            <h3 class="resume-entry-title">${project.project_name}</h3>
            ${project.technologies_used ? `<p class="resume-entry-subtitle">${project.technologies_used}</p>` : ''}
          </div>
          ${project.start_date ? `<div class="resume-entry-date">${formatDate(project.start_date)} - ${formatDate(project.end_date)}</div>` : ''}
        </div>
        ${project.description ? `<p class="resume-entry-description">${project.description}</p>` : ''}
        ${project.project_url ? `<p><a href="${project.project_url}" target="_blank">View Project</a></p>` : ''}
      </div>
    `).join('');
    document.getElementById('projectsList').innerHTML = projectsHTML;
    document.getElementById('projectsSection').style.display = 'block';
  }
}

loadResume();
