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

$query = "SELECT * FROM users WHERE username = '$username';"; 
$result = mysqli_query($connection, $query);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['delete'])) {
    $query = "UPDATE users SET activ=0 WHERE username = '$username';";
    mysqli_query($connection, $query);
   
    header("Location: connect2.php");
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Information</title>
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
        <h1>Student Information</h1>
        <div class="user-info">
            <table>
                <tr>
                    <th>User ID</th>
                    <td><?php echo $user['user_id']; ?></td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td><?php echo $user['username']; ?></td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td><?php echo $user['first_name']; ?></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><?php echo $user['last_name']; ?></td>
                </tr>
                <tr>
                    <th>School ID</th>
                    <td><?php echo $user['school_id']; ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><?php echo $user['user_role']; ?></td>
                </tr>
                <tr>
                    <th>Date of Birth</th>
                    <td><?php echo $user['date_of_birth']; ?></td>
                </tr>
            </table>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="submit" value="Delete User" name="delete">
    </form>
        </div>
    </div>
</body>
</html>