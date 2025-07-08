<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$action = $_GET['action'] ?? '';
$entity = $_GET['entity'] ?? 'students';
$id = $_GET['id'] ?? '';
$message = '';
$error = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create') {
            switch ($entity) {
                case 'students':
                    $stmt = $pdo->prepare("INSERT INTO STUDENT (student_id, first_name, last_name, email, phone, date_of_birth, address, enrollment_date, status, advisor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_POST['student_id'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['date_of_birth'], $_POST['address'], $_POST['enrollment_date'], $_POST['status'], $_POST['advisor_id'] ?: null]);
                    $message = "Student created successfully!";
                    break;
                case 'advisors':
                    $stmt = $pdo->prepare("INSERT INTO ADVISOR (advisor_id, first_name, last_name, email, phone, department, office_location, max_advisees, specialization) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_POST['advisor_id'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['department'], $_POST['office_location'], $_POST['max_advisees'], $_POST['specialization']]);
                    $message = "Advisor created successfully!";
                    break;
                case 'instructors':
                    $stmt = $pdo->prepare("INSERT INTO INSTRUCTOR (instructor_id, first_name, last_name, email, phone, department, office_location, hire_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_POST['instructor_id'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['department'], $_POST['office_location'], $_POST['hire_date'], $_POST['status']]);
                    $message = "Instructor created successfully!";
                    break;
                case 'courses':
                    $stmt = $pdo->prepare("INSERT INTO COURSE_CATALOG (course_code, course_title, description, credit_hours, prerequisites, department, level, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_POST['course_code'], $_POST['course_title'], $_POST['description'], $_POST['credit_hours'], $_POST['prerequisites'], $_POST['department'], $_POST['level'], $_POST['status']]);
                    $message = "Course created successfully!";
                    break;
            }
        } elseif ($action === 'update') {
            switch ($entity) {
                case 'students':
                    $stmt = $pdo->prepare("UPDATE STUDENT SET first_name=?, last_name=?, email=?, phone=?, date_of_birth=?, address=?, status=?, advisor_id=? WHERE student_id=?");
                    $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['date_of_birth'], $_POST['address'], $_POST['status'], $_POST['advisor_id'] ?: null, $id]);
                    $message = "Student updated successfully!";
                    break;
                case 'advisors':
                    $stmt = $pdo->prepare("UPDATE ADVISOR SET first_name=?, last_name=?, email=?, phone=?, department=?, office_location=?, max_advisees=?, specialization=? WHERE advisor_id=?");
                    $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['department'], $_POST['office_location'], $_POST['max_advisees'], $_POST['specialization'], $id]);
                    $message = "Advisor updated successfully!";
                    break;
                case 'instructors':
                    $stmt = $pdo->prepare("UPDATE INSTRUCTOR SET first_name=?, last_name=?, email=?, phone=?, department=?, office_location=?, status=? WHERE instructor_id=?");
                    $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['department'], $_POST['office_location'], $_POST['status'], $id]);
                    $message = "Instructor updated successfully!";
                    break;
                case 'courses':
                    $stmt = $pdo->prepare("UPDATE COURSE_CATALOG SET course_title=?, description=?, credit_hours=?, prerequisites=?, department=?, level=?, status=? WHERE course_code=?");
                    $stmt->execute([$_POST['course_title'], $_POST['description'], $_POST['credit_hours'], $_POST['prerequisites'], $_POST['department'], $_POST['level'], $_POST['status'], $id]);
                    $message = "Course updated successfully!";
                    break;
            }
        } elseif ($action === 'delete') {
            switch ($entity) {
                case 'students':
                    $stmt = $pdo->prepare("DELETE FROM STUDENT WHERE student_id=?");
                    $stmt->execute([$id]);
                    $message = "Student deleted successfully!";
                    break;
                case 'advisors':
                    $stmt = $pdo->prepare("DELETE FROM ADVISOR WHERE advisor_id=?");
                    $stmt->execute([$id]);
                    $message = "Advisor deleted successfully!";
                    break;
                case 'instructors':
                    $stmt = $pdo->prepare("DELETE FROM INSTRUCTOR WHERE instructor_id=?");
                    $stmt->execute([$id]);
                    $message = "Instructor deleted successfully!";
                    break;
                case 'courses':
                    $stmt = $pdo->prepare("DELETE FROM COURSE_CATALOG WHERE course_code=?");
                    $stmt->execute([$id]);
                    $message = "Course deleted successfully!";
                    break;
            }
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch single item for editing
$editItem = null;
if ($action === 'edit' && $id) {
    try {
        switch ($entity) {
            case 'students':
                $stmt = $pdo->prepare("SELECT * FROM STUDENT WHERE student_id=?");
                $stmt->execute([$id]);
                $editItem = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
            case 'advisors':
                $stmt = $pdo->prepare("SELECT * FROM ADVISOR WHERE advisor_id=?");
                $stmt->execute([$id]);
                $editItem = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
            case 'instructors':
                $stmt = $pdo->prepare("SELECT * FROM INSTRUCTOR WHERE instructor_id=?");
                $stmt->execute([$id]);
                $editItem = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
            case 'courses':
                $stmt = $pdo->prepare("SELECT * FROM COURSE_CATALOG WHERE course_code=?");
                $stmt->execute([$id]);
                $editItem = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch data based on entity
try {
    switch ($entity) {
        case 'students':
            $stmt = $pdo->query("SELECT * FROM STUDENT ORDER BY student_id");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'advisors':
            $stmt = $pdo->query("SELECT * FROM ADVISOR ORDER BY advisor_id");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'instructors':
            $stmt = $pdo->query("SELECT * FROM INSTRUCTOR ORDER BY instructor_id");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'courses':
            $stmt = $pdo->query("SELECT * FROM COURSE_CATALOG ORDER BY course_code");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        default:
            $items = [];
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - EduPortal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal {
            display: none;
        }
        .modal.active {
            display: flex;
        }
        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Admin Panel</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, Admin!</span>
                    <a href="login.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Navigation -->
        <nav class="mb-8">
            <div class="flex justify-center space-x-1 bg-white rounded-lg p-1 shadow-sm">
                <a href="admin_page.php?entity=students" class="px-6 py-3 rounded-md font-medium transition <?= $entity === 'students' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">Students</a>
                <a href="admin_page.php?entity=advisors" class="px-6 py-3 rounded-md font-medium transition <?= $entity === 'advisors' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">Advisors</a>
                <a href="admin_page.php?entity=instructors" class="px-6 py-3 rounded-md font-medium transition <?= $entity === 'instructors' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">Instructors</a>
                <a href="admin_page.php?entity=courses" class="px-6 py-3 rounded-md font-medium transition <?= $entity === 'courses' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">Courses</a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-sm p-6 fade-in">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Manage <?= htmlspecialchars(ucfirst($entity)) ?></h2>
                <button onclick="openModal('createModal')" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add New <?= htmlspecialchars(ucfirst(substr($entity, 0, -1))) ?>
                </button>
            </div>

            <?php if (count($items) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <?php foreach (array_keys($items[0]) as $col): ?>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $col))) ?></th>
                                <?php endforeach; ?>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <?php foreach ($item as $value): ?>
                                        <td class="py-3 px-4 text-gray-900"><?= htmlspecialchars($value) ?></td>
                                    <?php endforeach; ?>
                                    <td class="py-3 px-4">
                                        <div class="flex space-x-2">
                                            <?php 
                                            $primaryKey = '';
                                            switch ($entity) {
                                                case 'students': $primaryKey = $item['student_id']; break;
                                                case 'advisors': $primaryKey = $item['advisor_id']; break;
                                                case 'instructors': $primaryKey = $item['instructor_id']; break;
                                                case 'courses': $primaryKey = $item['course_code']; break;
                                            }
                                            ?>
                                            <button onclick="editItem('<?= htmlspecialchars($primaryKey) ?>')" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                                            <button onclick="deleteItem('<?= htmlspecialchars($primaryKey) ?>')" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-4 4m0 0l-4-4m4 4V3"/>
                    </svg>
                    <p class="text-gray-500 text-lg">No <?= htmlspecialchars($entity) ?> found</p>
                    <p class="text-gray-400">Click "Add New" to create your first entry</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Add New <?= htmlspecialchars(ucfirst(substr($entity, 0, -1))) ?></h3>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="admin_page.php?entity=<?= $entity ?>&action=create" class="space-y-4">
                <?php include 'form_fields.php'; ?>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Edit <?= htmlspecialchars(ucfirst(substr($entity, 0, -1))) ?></h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            
            <form id="editForm" method="POST" class="space-y-4">
                <div id="editFormFields"></div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function editItem(id) {
            window.location.href = `admin_page.php?entity=<?= $entity ?>&action=edit&id=${id}`;
        }

        function deleteItem(id) {
            if (confirm('Are you sure you want to delete this item?')) {
                window.location.href = `admin_page.php?entity=<?= $entity ?>&action=delete&id=${id}`;
            }
        }

        <?php if ($action === 'edit' && $editItem): ?>
            // Auto-open edit modal if editing
            openModal('editModal');
            document.getElementById('editForm').action = 'admin_page.php?entity=<?= $entity ?>&action=update&id=<?= $id ?>';
            
            // Populate edit form
            <?php
            $editFormFields = '';
            switch ($entity) {
                case 'students':
                    $editFormFields = "
                        <div class='grid grid-cols-1 md:grid-cols-2 gap-4'>
                            <div><label class='block text-sm font-medium text-gray-700 mb-1'>Student ID</label><input type='number' name='student_id' value='{$editItem['student_id']}' readonly class='w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100'></div>
                            <div><label class='block text-sm font-medium text-gray-700 mb-1'>First Name</label><input type='text' name='first_name' value='{$editItem['first_name']}' required class='w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent'></div>
                            <div><label class='block text-sm font-medium text-gray-700 mb-1'>Last Name</label><input type='text' name='last_name' value='{$editItem['last_name']}' required class='w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent'></div>
                            <div><label class='block text-sm font-medium text-gray-700 mb-1'>Email</label><input type='email' name='email' value='{$editItem['email']}' required class='w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent'></div>
                            <div><label class='block text-sm font-medium text-gray-700 mb-1'>Phone</label><input type='text' name='phone' value='{$editItem['phone']}' class='w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent'></div>
                            <div><label class='block text-sm font-medium text-gray-700 mb-1'>Date of Birth</label><input type='date' name='date_of_birth' value='{$editItem['date_of_birth']}' class='w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent'></div>
                            <div class='md:col-span-2'><label class='block text-sm font-medium text-gray-700 mb-1'>Address</label><textarea name='address' class='w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent'>{$editItem['address']}</textarea></div>
                            <div><label class='block text-sm font-medium text-gray-700 mb-1'>Status</label><select name='status' class='w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent'><option value='active'" . ($editItem['status'] === 'active' ? ' selected' : '') . ">Active</option><option value='inactive'" . ($editItem['status'] === 'inactive' ? ' selected' : '') . ">Inactive</option></select></div>
                            <div><label class='block text-sm font-medium text-gray-700 mb-1'>Advisor ID</label><input type='number' name='advisor_id' value='{$editItem['advisor_id']}' class='w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent'></div>
                        </div>
                    ";
                    break;
                // Add other cases for advisors, instructors, courses...
            }
            ?>
            document.getElementById('editFormFields').innerHTML = `<?= $editFormFields ?>`;
        <?php endif; ?>
    </script>
</body>
</html>
