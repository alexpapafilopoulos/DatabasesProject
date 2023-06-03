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

$userrole = mysqli_fetch_assoc($result);
$userrole = $userrole['user_role'];

if (isset($_POST['info'])) {
    
    if ($userrole == 'student'){
        header("Location: info.php");
        exit();
        }
    
        else if ($userrole == 'teacher'){
            header("Location: infocustom.php");
             exit();
    
        }
}

?>
<!DOCTYPE html>
<html lang="en">
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Home</title>
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
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group select {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            background-color: #4CAF50;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #45a049;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to the Library</h1>
            <h2>User: <?php echo $username; ?></h2>
        </div>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
                <input type="submit" name="option" value="My Lent Books" class="button">
            </div>
            <div class="form-group">
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="submit" value="User Info" name="info" class="button">
    </form>
</div>
            <div class="form-group">
                <label for="newpassword">Change Password:</label>
                <input type="text" name="newpassword" id="newpassword">
                <input type="submit" name="passw" value="Change" class="button">
            </div>
        </form>

        <h2>Search Books</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title">
            </div>
            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" name="author" id="author">
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category" id="category">
                <?php
                $catQuery = "SELECT * FROM category;";
                $catResult = mysqli_query($connection, $catQuery);
                echo "<option value='All'>All</option>";
    
        while ($catRow = mysqli_fetch_assoc($catResult)) {
        $catID = $catRow['category_name'];
        echo "<option value='$catID'>$catID</option>";

    }
    mysqli_data_seek($catResult, 0);
    echo "</select>";

                ?>
                
            </div>
            <div class="form-group">
                <input type="submit" value="Search" name="search" class="button">
            </div>
        </form>

        <?php
      
        if (isset($_POST['search'])) {
            $title = $_POST['title'];
            $author = $_POST['author'];
            $category = $_POST['category'];

            
            $query = "CALL SchoolBooks('$username');";
            mysqli_query($connection, $query);

            $query = "CALL FilteredSchoolBooks('$title','$author','$category');";
            $result = mysqli_query($connection, $query);

           
            echo "<h2>Available Books:</h2>";
            echo "<form action='book.php' method='POST'>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>Title</th><th>Author</th><th>Category</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
    echo "<td>".$row['title']."</td>";
    echo "<td>".$row['author_last_name']."</td>";
    echo "<td>".$row['category_name']."</td>";
    echo "<td><input type='radio' name='selected_book' value='".$row['title']."|".$row['author_last_name']."|".$row['category_name']."'></td>";
    echo "</tr>";
            }
            echo "</table>";
            echo "<button type='submit' name='submit'>See Book Details</button>";
            echo "</div>";
            echo "</form>";
        }

        if (isset($_POST['option'])) {
            $option = $_POST['option'];

        
            
            $query = "CALL MyLoans('$username')";
            $result = mysqli_query($connection, $query);

            
            echo "<h2>My Lent Books:</h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>Title</th><th>Lend Date</th><th>Return Date</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr><td>".$row['title']."</td><td>".$row['lend_date']."</td><td>".$row['return_date']."</td></tr>";
            }
            echo "</table>";
            echo "</div>";
        }

        if (isset($_POST['passw'])) {
            $newpass = $_POST['newpassword'];
            $query = "UPDATE users SET pass = '$newpass' WHERE username = '$username'";
            $result = mysqli_query($connection, $query);
            if ($result > 0) {
                echo "<p>Password changed successfully.</p>";
            } 
            else {
                $error = "Invalid new password.";
            }
        }
        ?>
        
    </div>
</body>
</html>

<?php
mysqli_close($connection);
?>