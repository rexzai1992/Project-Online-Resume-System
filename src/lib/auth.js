import { supabase } from './supabase.js';

export async function getCurrentUser() {
  const { data: { user } } = await supabase.auth.getUser();
  return user;
}

export async function signIn(email, password) {
  const { data, error } = await supabase.auth.signInWithPassword({
    email,
    password
  });

  if (error) throw error;
  return data;
}

export async function signOut() {
  const { error } = await supabase.auth.signOut();
  if (error) throw error;
}

export async function requireAuth() {
  const user = await getCurrentUser();
  if (!user) {
    window.location.href = '/';
    throw new Error('Not authenticated');
  }
  return user;
}

export function onAuthStateChange(callback) {
  return supabase.auth.onAuthStateChange((event, session) => {
    (() => {
      callback(event, session);
    })();
  });
}
