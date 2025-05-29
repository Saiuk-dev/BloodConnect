<?php
session_start();
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donation";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0)
     {
        $admin = $result->fetch_assoc();
        // In the login handling section after setting session variables
if (password_verify($admin_password, $admin['password'])) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $admin_username;
    header("Location: admin.php"); // Add this line
    exit(); // Add this line
}

}




// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Handle donor deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM donors WHERE id = $id");
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Blood Donation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f2f5;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Login Form */
        .login-form {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2b0000;
        }

        /* Admin Dashboard */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: #2b0000;
            color: white;
            padding: 20px;
            border-radius: 10px;
        }

        .donors-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .donors-table th,
        .donors-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .donors-table th {
            background: #420909;
            color: white;
        }

        .donors-table tr:hover {
            background-color: #f5f5f5;
        }

        .action-buttons a {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .edit-btn {
            background: #ffc107;
            color: black;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php if (!isset($_SESSION['admin_logged_in'])): ?>
        <div class="login-form">
            <h2>Admin Login</h2>
            <?php if (isset($login_error)): ?>
                <div class="alert error"><?= $login_error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div style="margin-bottom: 20px;">
                    <label>Username</label>
                    <input type="text" name="username" required style="width: 100%; padding: 10px; margin-top: 5px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label>Password</label>
                    <input type="password" name="password" required style="width: 100%; padding: 10px; margin-top: 5px;">
                </div>
                <button type="submit" name="login" style="width: 100%; padding: 10px; background: #2b0000; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Login
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="admin-container">
            <div class="dashboard-header">
                <h2>Blood Donation Admin Panel</h2>
                <div>
                    Welcome, <?= $_SESSION['admin_username'] ?>! 
                    <a href="?logout" style="color: white; margin-left: 20px;">Logout</a>
                </div>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Donors</h3>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM donors");
                    echo $result->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="stat-card">
                    <h3>Upcoming Appointments</h3>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM donors WHERE appointment > NOW()");
                    echo $result->fetch_assoc()['total'];
                    ?>
                </div>
            </div>

            <table class="donors-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Blood Type</th>
                        <th>Appointment</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM donors ORDER BY created_at DESC");
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= $row['age'] ?></td>
                        <td><?= $row['blood_type'] ?></td>
                        <td><?= date('M j, Y H:i', strtotime($row['appointment'])) ?></td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td class="action-buttons">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                            <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>