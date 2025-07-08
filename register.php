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

        // Insert enrollment record
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
    <title>Registration Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-black min-h-screen p-6 flex items-center justify-center">
    <div class="max-w-lg w-full bg-gray-50 p-8 rounded-lg shadow text-center">
        <h1 class="text-3xl font-semibold mb-6">Registration Confirmation</h1>
        <p class="mb-6"><?= $message ?></p>
        <a href="index.php" class="inline-block bg-black text-white py-2 px-6 rounded hover:bg-gray-800 transition">Back to Courses</a>
    </div>
</body>
</html>
