/*
  # Online Resume System - Database Schema
  
  ## Overview
  Creates the complete database schema for an online resume management system.
  This migration sets up all necessary tables for managing profile information,
  work experience, education, skills, certifications, and projects.
  
  ## New Tables
  
  ### 1. profile (Personal Information)
  - `id` (uuid, primary key)
  - `user_id` (uuid, foreign key to auth.users)
  - `full_name` (text) - Full name of the person
  - `job_title` (text) - Current job title
  - `email` (text) - Contact email
  - `phone` (text) - Contact phone number
  - `location` (text) - Current location
  - `linkedin_url` (text) - LinkedIn profile URL
  - `website_url` (text) - Personal website or GitHub URL
  - `profile_image` (text) - URL to profile image
  - `summary` (text) - Professional summary/bio
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)
  
  ### 2. experiences (Work History)
  - `id` (uuid, primary key)
  - `user_id` (uuid, foreign key to auth.users)
  - `company_name` (text) - Name of the company
  - `job_title` (text) - Job title held
  - `location` (text) - Work location
  - `start_date` (date) - Employment start date
  - `end_date` (date, nullable) - Employment end date
  - `is_current` (boolean) - Currently working here
  - `description` (text) - Job description and achievements
  - `display_order` (integer) - Sort order for display
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)
  
  ### 3. education (Academic Background)
  - `id` (uuid, primary key)
  - `user_id` (uuid, foreign key to auth.users)
  - `institution` (text) - Educational institution name
  - `degree` (text) - Degree obtained
  - `field_of_study` (text) - Major/field of study
  - `location` (text) - Institution location
  - `start_date` (date) - Start date
  - `end_date` (date, nullable) - End date
  - `description` (text) - Additional details
  - `display_order` (integer) - Sort order for display
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)
  
  ### 4. skills (Technical & Soft Skills)
  - `id` (uuid, primary key)
  - `user_id` (uuid, foreign key to auth.users)
  - `skill_name` (text) - Name of the skill
  - `category` (text) - Skill category
  - `proficiency_level` (text) - Beginner/Intermediate/Advanced/Expert
  - `display_order` (integer) - Sort order for display
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)
  
  ### 5. certifications (Professional Certifications)
  - `id` (uuid, primary key)
  - `user_id` (uuid, foreign key to auth.users)
  - `cert_name` (text) - Certification name
  - `issuing_org` (text) - Issuing organization
  - `issue_date` (date, nullable) - Issue date
  - `expiry_date` (date, nullable) - Expiry date
  - `credential_url` (text) - URL to credential
  - `display_order` (integer) - Sort order for display
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)
  
  ### 6. projects (Portfolio Projects)
  - `id` (uuid, primary key)
  - `user_id` (uuid, foreign key to auth.users)
  - `project_name` (text) - Project name
  - `description` (text) - Project description
  - `technologies_used` (text) - Technologies/tools used
  - `project_url` (text) - Project URL or repository
  - `start_date` (date, nullable) - Project start date
  - `end_date` (date, nullable) - Project end date
  - `display_order` (integer) - Sort order for display
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)
  
  ## Security
  
  ### Row Level Security (RLS)
  - All tables have RLS enabled
  - Users can only read, create, update, and delete their own data
  - Public can read all data (for resume viewing)
  
  ## Notes
  - Uses UUID for primary keys
  - All timestamps use timestamptz
  - Foreign keys reference auth.users(id) with CASCADE delete
  - Indexes added for performance on user_id and display_order columns
*/

-- =====================================================
-- Table: profile (Personal Info - Single Row Per User)
-- =====================================================
CREATE TABLE IF NOT EXISTS profile (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  full_name text NOT NULL,
  job_title text,
  email text,
  phone text,
  location text,
  linkedin_url text,
  website_url text,
  profile_image text,
  summary text,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now(),
  UNIQUE(user_id)
);

CREATE INDEX IF NOT EXISTS idx_profile_user_id ON profile(user_id);

ALTER TABLE profile ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can view profiles"
  ON profile
  FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Users can insert own profile"
  ON profile
  FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own profile"
  ON profile
  FOR UPDATE
  TO authenticated
  USING (auth.uid() = user_id)
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can delete own profile"
  ON profile
  FOR DELETE
  TO authenticated
  USING (auth.uid() = user_id);

-- =====================================================
-- Table: experiences (Work History)
-- =====================================================
CREATE TABLE IF NOT EXISTS experiences (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  company_name text NOT NULL,
  job_title text NOT NULL,
  location text,
  start_date date NOT NULL,
  end_date date,
  is_current boolean DEFAULT false,
  description text,
  display_order integer DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_experiences_user_id ON experiences(user_id);
CREATE INDEX IF NOT EXISTS idx_experiences_display_order ON experiences(display_order);

ALTER TABLE experiences ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can view experiences"
  ON experiences
  FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Users can insert own experiences"
  ON experiences
  FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own experiences"
  ON experiences
  FOR UPDATE
  TO authenticated
  USING (auth.uid() = user_id)
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can delete own experiences"
  ON experiences
  FOR DELETE
  TO authenticated
  USING (auth.uid() = user_id);

-- =====================================================
-- Table: education (Academic Background)
-- =====================================================
CREATE TABLE IF NOT EXISTS education (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  institution text NOT NULL,
  degree text NOT NULL,
  field_of_study text,
  location text,
  start_date date NOT NULL,
  end_date date,
  description text,
  display_order integer DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_education_user_id ON education(user_id);
CREATE INDEX IF NOT EXISTS idx_education_display_order ON education(display_order);

ALTER TABLE education ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can view education"
  ON education
  FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Users can insert own education"
  ON education
  FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own education"
  ON education
  FOR UPDATE
  TO authenticated
  USING (auth.uid() = user_id)
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can delete own education"
  ON education
  FOR DELETE
  TO authenticated
  USING (auth.uid() = user_id);

-- =====================================================
-- Table: skills (Technical & Soft Skills)
-- =====================================================
CREATE TABLE IF NOT EXISTS skills (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  skill_name text NOT NULL,
  category text,
  proficiency_level text DEFAULT 'Intermediate',
  display_order integer DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_skills_user_id ON skills(user_id);
CREATE INDEX IF NOT EXISTS idx_skills_display_order ON skills(display_order);

ALTER TABLE skills ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can view skills"
  ON skills
  FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Users can insert own skills"
  ON skills
  FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own skills"
  ON skills
  FOR UPDATE
  TO authenticated
  USING (auth.uid() = user_id)
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can delete own skills"
  ON skills
  FOR DELETE
  TO authenticated
  USING (auth.uid() = user_id);

-- =====================================================
-- Table: certifications (Professional Certifications)
-- =====================================================
CREATE TABLE IF NOT EXISTS certifications (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  cert_name text NOT NULL,
  issuing_org text NOT NULL,
  issue_date date,
  expiry_date date,
  credential_url text,
  display_order integer DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_certifications_user_id ON certifications(user_id);
CREATE INDEX IF NOT EXISTS idx_certifications_display_order ON certifications(display_order);

ALTER TABLE certifications ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can view certifications"
  ON certifications
  FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Users can insert own certifications"
  ON certifications
  FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own certifications"
  ON certifications
  FOR UPDATE
  TO authenticated
  USING (auth.uid() = user_id)
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can delete own certifications"
  ON certifications
  FOR DELETE
  TO authenticated
  USING (auth.uid() = user_id);

-- =====================================================
-- Table: projects (Portfolio Projects)
-- =====================================================
CREATE TABLE IF NOT EXISTS projects (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  project_name text NOT NULL,
  description text,
  technologies_used text,
  project_url text,
  start_date date,
  end_date date,
  display_order integer DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_projects_user_id ON projects(user_id);
CREATE INDEX IF NOT EXISTS idx_projects_display_order ON projects(display_order);

ALTER TABLE projects ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can view projects"
  ON projects
  FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Users can insert own projects"
  ON projects
  FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own projects"
  ON projects
  FOR UPDATE
  TO authenticated
  USING (auth.uid() = user_id)
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can delete own projects"
  ON projects
  FOR DELETE
  TO authenticated
  USING (auth.uid() = user_id);

-- =====================================================
-- Function: Update updated_at timestamp
-- =====================================================
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Apply trigger to all tables
CREATE TRIGGER update_profile_updated_at
  BEFORE UPDATE ON profile
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_experiences_updated_at
  BEFORE UPDATE ON experiences
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_education_updated_at
  BEFORE UPDATE ON education
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_skills_updated_at
  BEFORE UPDATE ON skills
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_certifications_updated_at
  BEFORE UPDATE ON certifications
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_projects_updated_at
  BEFORE UPDATE ON projects
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();
