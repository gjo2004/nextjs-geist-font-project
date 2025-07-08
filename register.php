<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $course_code = filter_input(INPUT_POST, 'course_code', FILTER_SANITIZE_STRING);
    $semester_id = filter_input(INPUT_POST, 'semester_id', FILTER_VALIDATE_INT);

    if (!$student_id || !$course_code || !$semester_id) {
        die("Invalid input. Please go back and try again.");
    }

    try {
        // Check if student exists
        $stmt = $pdo->prepare("SELECT * FROM STUDENT WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$student) {
            die("Student ID not found.");
        }

        // Check if course exists and is active
        $stmt = $pdo->prepare("SELECT * FROM COURSE_CATALOG WHERE course_code = ? AND status = 'active'");
        $stmt->execute([$course_code]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$course) {
            die("Course not found or inactive.");
        }

        // Insert enrollment record (enrollment_id will auto-increment)
        $stmt = $pdo->prepare("INSERT INTO ENROLLMENT (student_id, course_code, semester_id, enrollment_date, status) VALUES (?, ?, ?, CURDATE(), 'enrolled')");
        $stmt->execute([$student_id, $course_code, $semester_id]);

        $message = "Registration successful for course " . htmlspecialchars($course_code) . ".";
    } catch (PDOException $e) {
        die("Error during registration: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registration Confirmation - EduPortal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-6 flex items-center justify-center">
    <div class="max-w-lg w-full bg-white p-8 rounded-lg shadow-sm border border-gray-200 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Registration Successful!</h1>
        <p class="text-gray-600 mb-8"><?= $message ?></p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="course_page.php" class="bg-gray-900 text-white py-3 px-6 rounded-lg hover:bg-gray-800 transition font-medium">
                Browse More Courses
            </a>
            <a href="index.php" class="border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition font-medium">
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
