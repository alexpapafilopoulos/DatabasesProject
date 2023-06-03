<?php

$host = "localhost";
$username = "root";
$password = ""; 
$database = "library";


$connection = mysqli_connect($host, $username, $password, $database);


if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}


session_start();
$username = $_SESSION['username'];

if (isset($_POST['user_id'])) {
    
    $userId = $_POST['user_id'];
}
$query = "SELECT * FROM users WHERE user_id = '$userId';"; 
$result = mysqli_query($connection, $query);
$user = mysqli_fetch_assoc($result);
$username = $user['username'];

if (isset($_POST['delete'])) {
    $query = "UPDATE users SET activ=0 WHERE username = '$username';";
    mysqli_query($connection, $query);
    
    header("Location: handler.php");
    exit();
}

if (isset($_POST['save'])) {
    $user_id = $user['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $school_id = $_POST['school_id'];
    $username = $_POST['username'];
    $date_of_birth = $_POST['date_of_birth'];

    $query = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', school_id = '$school_id', username = '$username', date_of_birth = '$date_of_birth' WHERE user_id = '$user_id';";

    if (mysqli_query($connection, $query)) {
        
        header("Location: handler.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($connection);
    }
}

$query = "SELECT * FROM users WHERE username = '$username';"; 
$result = mysqli_query($connection, $query);
$user = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Information</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .user-info {
            margin-top: 20px;
        }
        .user-info table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .user-info th,
        .user-info td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .user-info th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Information</h1>
        <div class="user-info">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                <table>
                    <tr>
                        <th>User ID</th>
                        <td><?php echo $user['user_id']; ?></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><input type="text" name="username" value="<?php echo $user['username']; ?>"></td>
                    </tr>
                    <tr>
                        <th>First Name</th>
                        <td><input type="text" name="first_name" value="<?php echo $user['first_name']; ?>"></td>
                    </tr>
                    <tr>
                        <th>Last Name</th>
                        <td><input type="text" name="last_name" value="<?php echo $user['last_name']; ?>"></td>
                    </tr>
                    <tr>
                        <th>School ID</th>
                        <td><input type="text" name="school_id" value="<?php echo $user['school_id']; ?>"></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><?php echo $user['user_role']; ?></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td><input type="text" name="date_of_birth" value="<?php echo $user['date_of_birth']; ?>"></td>
                    </tr>
                </table>
                <input type="submit" value="Save Changes" name="save">
                <input type="submit" value="Delete User" name="delete">
            </form>
        </div>
    </div>
</body>
</html>
</html>