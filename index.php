<?php
require 'db.php';

// Fetch courses from COURSE_CATALOG
try {
    $stmt = $pdo->query("SELECT course_code, course_title, description, credit_hours FROM COURSE_CATALOG WHERE status = 'active'");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Course Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-black min-h-screen p-6">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-4xl font-semibold mb-8 text-center">Online Course Registration</h1>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Available Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($courses as $course): ?>
                    <div class="border border-gray-300 rounded-lg p-4 shadow-sm">
                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($course['course_title']) ?> (<?= htmlspecialchars($course['course_code']) ?>)</h3>
                        <p class="text-gray-700 mb-2"><?= htmlspecialchars($course['description']) ?></p>
                        <p class="font-medium">Credit Hours: <?= htmlspecialchars($course['credit_hours']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold mb-4">Register for a Course</h2>
            <form action="register.php" method="POST" class="max-w-lg mx-auto bg-gray-50 p-6 rounded-lg shadow">
                <div class="mb-4">
                    <label for="student_id" class="block mb-1 font-medium">Student ID</label>
                    <input type="number" id="student_id" name="student_id" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                </div>
                <div class="mb-4">
                    <label for="course_code" class="block mb-1 font-medium">Select Course</label>
                    <select id="course_code" name="course_code" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="" disabled selected>-- Choose a course --</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= htmlspecialchars($course['course_code']) ?>"><?= htmlspecialchars($course['course_title']) ?> (<?= htmlspecialchars($course['course_code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="semester_id" class="block mb-1 font-medium">Semester ID</label>
                    <input type="number" id="semester_id" name="semester_id" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                </div>
                <div class="mb-6">
                    <button type="submit" class="w-full bg-black text-white py-2 rounded hover:bg-gray-800 transition">Register</button>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
