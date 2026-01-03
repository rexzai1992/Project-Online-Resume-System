import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  root: 'src',
  build: {
    outDir: '../dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/index.html'),
        resume: resolve(__dirname, 'src/resume.html'),
        admin: resolve(__dirname, 'src/admin/index.html'),
        profile: resolve(__dirname, 'src/admin/profile.html'),
        experiences: resolve(__dirname, 'src/admin/experiences.html'),
        education: resolve(__dirname, 'src/admin/education.html'),
        skills: resolve(__dirname, 'src/admin/skills.html'),
        certifications: resolve(__dirname, 'src/admin/certifications.html'),
        projects: resolve(__dirname, 'src/admin/projects.html'),
      }
    }
  }
});
