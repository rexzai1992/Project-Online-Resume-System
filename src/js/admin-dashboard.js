import { supabase } from '../lib/supabase.js';
import { requireAuth, signOut } from '../lib/auth.js';

async function loadDashboard() {
  try {
    const user = await requireAuth();

    const { data: profile } = await supabase
      .from('profile')
      .select('*')
      .eq('user_id', user.id)
      .maybeSingle();

    if (profile) {
      document.getElementById('userName').textContent = `, ${profile.full_name}`;
    }

    const { count: expCount } = await supabase.from('experiences').select('*', { count: 'exact', head: true }).eq('user_id', user.id);
    const { count: eduCount } = await supabase.from('education').select('*', { count: 'exact', head: true }).eq('user_id', user.id);
    const { count: skillCount } = await supabase.from('skills').select('*', { count: 'exact', head: true }).eq('user_id', user.id);
    const { count: certCount } = await supabase.from('certifications').select('*', { count: 'exact', head: true }).eq('user_id', user.id);
    const { count: projectCount } = await supabase.from('projects').select('*', { count: 'exact', head: true }).eq('user_id', user.id);

    document.getElementById('expCount').textContent = expCount || 0;
    document.getElementById('eduCount').textContent = eduCount || 0;
    document.getElementById('skillCount').textContent = skillCount || 0;
    document.getElementById('certCount').textContent = certCount || 0;
    document.getElementById('projectCount').textContent = projectCount || 0;
  } catch (error) {
    console.error('Error loading dashboard:', error);
  }
}

window.handleLogout = async function() {
  try {
    await signOut();
    window.location.href = '/';
  } catch (error) {
    console.error('Error logging out:', error);
  }
};

loadDashboard();
