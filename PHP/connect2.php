<?php
$host = "localhost";
$username = "root";
$password = ""; 
$database = "library";
$con = mysqli_connect($host, $username, $password, $database);

if (!$con) {
    die("Can't connect to server");
}


if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    session_start();
    $_SESSION['username'] = $username;

    
    $query = "SELECT * FROM users WHERE username = '$username' AND pass = '$password' AND (activ = 1 OR activ=3);";
    $result = mysqli_query($con, $query);

   
    if ($result && mysqli_num_rows($result) > 0) {
        $userrole = mysqli_fetch_assoc($result);
        $userrole = $userrole['user_role'];

        
        if ($userrole == 'student' || $userrole == 'teacher'|| $userrole == 'principal') {
            header("Location: users2.php");
            exit();
        } elseif ($userrole == 'handler') {
            header("Location: handler.php");
            exit();
            
        } else {
            header("Location: manager.php");
            exit();
        }
    } else {
       
        $error = "Invalid username or password or inactive account";
    }
}

if (isset($_POST['signup'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $date_of_birth = $_POST['date_of_birth'];
    $user_role = $_POST['user_role'];
    $school_id = $_POST['school_id'];

    
    $query = "INSERT INTO users (first_name, last_name, username, pass, date_of_birth, user_role, school_id, activ)
                VALUES ('$first_name', '$last_name', '$username', '$password', '$date_of_birth', '$user_role', '$school_id', 2)";

    if (mysqli_query($con, $query)) {
        
        header("Location: connect2.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}


$schoolsQuery = "SELECT * FROM schools";
$schoolsResult = mysqli_query($con, $schoolsQuery);


mysqli_close($con);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f2f2f2;
        }
        h1 {
            margin-bottom: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        p.error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($error)) { ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php } ?>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        <input type="submit" name="submit" value="Login">
    </form>

    <h1>Sign Up</h1>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" required><br><br>
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" required><br><br>
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" name="date_of_birth" required><br><br>
        <label for="user_role">User Role:</label>
        <select name="user_role" required>
            <option value="">Select User Role</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
        </select><br><br>
        <label for="school_id">School:</label>
        <select name="school_id" required>
            <option value="">Select School</option>
            <?php while ($row = mysqli_fetch_assoc($schoolsResult)) { ?>
                <option value="<?php echo $row['school_id']; ?>"><?php echo $row['school_name']; ?></option>
            <?php } ?>
        </select><br><br>
        <input type="submit" name="signup" value="Sign Up">
    </form>
</body>
</html>