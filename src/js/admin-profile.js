import { supabase } from '../lib/supabase.js';
import { requireAuth, signOut } from '../lib/auth.js';

let currentUser = null;

async function loadProfile() {
  try {
    currentUser = await requireAuth();

    const { data: profile } = await supabase
      .from('profile')
      .select('*')
      .eq('user_id', currentUser.id)
      .maybeSingle();

    if (profile) {
      document.getElementById('full_name').value = profile.full_name || '';
      document.getElementById('job_title').value = profile.job_title || '';
      document.getElementById('email').value = profile.email || '';
      document.getElementById('phone').value = profile.phone || '';
      document.getElementById('location').value = profile.location || '';
      document.getElementById('linkedin_url').value = profile.linkedin_url || '';
      document.getElementById('website_url').value = profile.website_url || '';
      document.getElementById('profile_image').value = profile.profile_image || '';
      document.getElementById('summary').value = profile.summary || '';
    }
  } catch (error) {
    console.error('Error loading profile:', error);
  }
}

document.getElementById('profileForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const profileData = {
    user_id: currentUser.id,
    full_name: document.getElementById('full_name').value,
    job_title: document.getElementById('job_title').value,
    email: document.getElementById('email').value,
    phone: document.getElementById('phone').value,
    location: document.getElementById('location').value,
    linkedin_url: document.getElementById('linkedin_url').value,
    website_url: document.getElementById('website_url').value,
    profile_image: document.getElementById('profile_image').value,
    summary: document.getElementById('summary').value
  };

  try {
    const { data: existing } = await supabase
      .from('profile')
      .select('id')
      .eq('user_id', currentUser.id)
      .maybeSingle();

    if (existing) {
      await supabase
        .from('profile')
        .update(profileData)
        .eq('user_id', currentUser.id);
    } else {
      await supabase
        .from('profile')
        .insert(profileData);
    }

    alert('Profile saved successfully!');
  } catch (error) {
    console.error('Error saving profile:', error);
    alert('Error saving profile');
  }
});

window.handleLogout = async function() {
  try {
    await signOut();
    window.location.href = '/';
  } catch (error) {
    console.error('Error logging out:', error);
  }
};

loadProfile();
