-- =====================================================
-- EduBoard Digital Notice Board System
-- MySQL Database Schema
-- =====================================================

CREATE DATABASE IF NOT EXISTS eduboard;
USE eduboard;

-- -----------------------------------------------------
-- Table: users
-- -----------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,       -- bcrypt hashed
    role ENUM('admin', 'faculty', 'student') NOT NULL,
    department VARCHAR(50) DEFAULT 'General',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------------
-- Table: departments
-- -----------------------------------------------------
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(20) NOT NULL UNIQUE
);

-- -----------------------------------------------------
-- Table: notices
-- -----------------------------------------------------
CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    department VARCHAR(50) NOT NULL DEFAULT 'General',
    category ENUM('Academic','Event','Exam','Urgent','Holiday','Announcement') NOT NULL DEFAULT 'Academic',
    priority ENUM('High','Medium','Low') NOT NULL DEFAULT 'Medium',
    status ENUM('Active','Expired') NOT NULL DEFAULT 'Active',
    expiry_date DATE NOT NULL,
    posted_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE
);

-- -----------------------------------------------------
-- Seed: Departments
-- -----------------------------------------------------
INSERT INTO departments (name, code) VALUES
('General', 'GEN'),
('Computer Science & Engineering', 'CSE'),
('Electronics & Communication', 'ECE'),
('Mechanical Engineering', 'MECH');

-- -----------------------------------------------------
-- Seed: Users (passwords are bcrypt of 'admin123', 'faculty123')
-- -----------------------------------------------------
INSERT INTO users (name, email, password, role, department) VALUES
('Admin User',      'admin@college.edu',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',   'General'),
('Dr. Priya Rajan', 'faculty@college.edu', '$2y$10$TKh8H1.PkWvnV4nq3B2E5O3YOFwB1sTK3M3Ge.fNKT/fdsS6YQjC', 'faculty', 'CSE');

-- Note: The above hashes are for demo. Regenerate with:
-- password_hash('admin123', PASSWORD_BCRYPT)

-- -----------------------------------------------------
-- Seed: Sample Notices
-- -----------------------------------------------------
INSERT INTO notices (title, content, department, category, priority, expiry_date, posted_by) VALUES
('Semester Exam Schedule 2025', 'The semester examinations will commence from December 1st, 2025. Students must download hall tickets from the student portal by November 25th. Mobile phones are strictly prohibited during exams.', 'CSE', 'Exam', 'High', '2025-12-30', 1),
('Annual Sports Day Registration', 'The college is organising its Annual Sports Day on November 20th, 2025. Students interested in Track & Field, Basketball, Cricket, or Chess must register through their class representatives by November 10th.', 'General', 'Event', 'Medium', '2025-11-15', 1),
('Placement Drive — TCS', 'Tata Consultancy Services will be conducting an on-campus placement drive on November 18th. All final year students with CGPA above 7.0 are eligible. Registration through the T&P Office is mandatory before November 15th.', 'General', 'Urgent', 'High', '2025-11-20', 1),
('Lab Assignment Submission', 'All students of the 5th semester CSE are reminded to submit their Operating Systems Lab assignments to the lab instructor before October 25th. Late submissions will be awarded zero marks.', 'CSE', 'Academic', 'High', '2025-10-25', 2),
('Circuit Design Workshop', 'A two-day hands-on workshop on Circuit Design using LTspice and Multisim will be conducted on November 4th and 5th in ECE Lab 2. Registration is free and limited to 60 students.', 'ECE', 'Event', 'Medium', '2025-11-05', 1),
('Diwali Holidays Notification', 'The college will remain closed from October 29th to November 2nd on account of Diwali celebrations. Classes will resume normally from November 3rd.', 'General', 'Holiday', 'Low', '2025-11-01', 1);
