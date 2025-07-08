-- Example users and data for testing with the correct database schema

-- Insert example students (additional to existing ones)
INSERT INTO STUDENT (student_id, first_name, last_name, email, phone, date_of_birth, address, enrollment_date, status, advisor_id)
VALUES 
(1006, 'Jane', 'Doe', 'jane.doe@example.com', '5551234567', '1999-05-15', '456 Oak Ave', CURDATE(), 'active', 2001),
(1007, 'Bob', 'Smith', 'bob.smith@example.com', '5559876543', '2001-03-22', '789 Pine St', CURDATE(), 'active', 2002);

-- Insert example advisors
INSERT INTO ADVISOR (advisor_id, first_name, last_name, email, phone, department, office_location, max_advisees, specialization)
VALUES 
(2001, 'Dr. Sarah', 'Johnson', 'advisor@example.com', '1112223333', 'Computer Science', 'Room 101', 50, 'AI/ML'),
(2002, 'Dr. Michael', 'Brown', 'michael.brown@example.com', '1114445555', 'Mathematics', 'Room 205', 40, 'Statistics');

-- Insert example instructors
INSERT INTO INSTRUCTOR (instructor_id, first_name, last_name, email, phone, department, office_location, hire_date, status)
VALUES 
(3001, 'Prof. Alice', 'Wilson', 'alice.wilson@example.com', '1116667777', 'Computer Science', 'Room 301', CURDATE(), 'active'),
(3002, 'Prof. David', 'Lee', 'david.lee@example.com', '1118889999', 'Mathematics', 'Room 302', CURDATE(), 'active');

-- Insert example courses
INSERT INTO COURSE_CATALOG (course_code, course_title, description, credit_hours, prerequisites, department, level, status)
VALUES 
('CS101', 'Introduction to Computer Science', 'Basic concepts of programming and computer science fundamentals', 3, NULL, 'Computer Science', 'undergraduate', 'active'),
('CS201', 'Data Structures and Algorithms', 'Advanced programming concepts including data structures and algorithms', 4, 'CS101', 'Computer Science', 'undergraduate', 'active'),
('MATH101', 'Calculus I', 'Introduction to differential and integral calculus', 4, NULL, 'Mathematics', 'undergraduate', 'active'),
('MATH201', 'Linear Algebra', 'Vector spaces, matrices, and linear transformations', 3, 'MATH101', 'Mathematics', 'undergraduate', 'active'),
('ENG101', 'English Composition', 'Academic writing and communication skills', 3, NULL, 'English', 'undergraduate', 'active'),
('PHYS101', 'General Physics I', 'Mechanics, waves, and thermodynamics', 4, 'MATH101', 'Physics', 'undergraduate', 'active'),
('CHEM101', 'General Chemistry', 'Basic principles of chemistry', 4, NULL, 'Chemistry', 'undergraduate', 'active'),
('BIO101', 'Introduction to Biology', 'Fundamentals of biological sciences', 3, NULL, 'Biology', 'undergraduate', 'active');

-- Insert example semesters
INSERT INTO SEMESTER (semester_id, semester_name, academic_year, start_date, end_date, registration_start, registration_end, status)
VALUES 
(1, 'Fall 2024', '2024-2025', '2024-08-26', '2024-12-15', '2024-04-01', '2024-08-20', 'active'),
(2, 'Spring 2025', '2024-2025', '2025-01-15', '2025-05-10', '2024-11-01', '2025-01-10', 'upcoming');

-- Insert example enrollments
INSERT INTO ENROLLMENT (enrollment_id, student_id, course_code, semester_id, instructor_id, enrollment_date, status, grade, grade_date)
VALUES 
(1, 1001, 'CS101', 1, 3001, '2024-08-15', 'enrolled', 'A', '2024-12-10'),
(2, 1001, 'MATH101', 1, 3002, '2024-08-15', 'enrolled', 'B+', '2024-12-10'),
(3, 1002, 'CS101', 1, 3001, '2024-08-16', 'enrolled', 'A-', '2024-12-10'),
(4, 1005, 'ENG101', 1, NULL, '2024-08-17', 'enrolled', NULL, NULL);

-- Insert example GPA records
INSERT INTO GPA (gpa_id, student_id, semester_id, semester_gpa, cumulative_gpa, total_credits, quality_points, calculated_date)
VALUES 
(1, 1001, 1, 3.65, 3.65, 7, 25, '2024-12-15'),
(2, 1002, 1, 3.70, 3.70, 3, 11, '2024-12-15');

-- Note: Passwords should be handled in a separate authentication table or system.
-- For testing, assume password is 'password123' for all users (student, admin, advisor).
-- 
-- Demo Credentials:
-- Student: student@example.com / password123
-- Admin: admin@example.com / password123  
-- Advisor: advisor@example.com / password123
