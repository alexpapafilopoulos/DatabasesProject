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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Library</title>
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
        h1 {
            font-size: 24px;
            margin: 10px 0;
        }
        h2 {
            font-size: 20px;
            margin: 10px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="number"],
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Library</h1>
            <h2>Logged in as: <?php echo $username; ?></h2>
        </div>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="submit" name="backup" value="Create Backup" class="button">
        <input type="submit" name="restore" value="Restore Backup" class="button">
        <input type="submit" name="hand" value="Pending Handler Requests" class="button">
            <div class="form-group">
                <h2>Check Number of Lendings per School</h2>
                <label for="month">Select Month (0 for all):</label>
                <input type="number" name="month" min="0" max="12">
                <label for="year">Select Year (0 for all):</label>
                <input type="number" name="year">
                <br>
                <input type="submit" name="lendings" value="Lendings Per School" class="button">
            </div>
            <div class="form-group">
                <h2>Info of Category</h2>
                <label for="categor">Category:</label>
                <select name="categor" id="categor">
                <?php
                $catQuery = "SELECT * FROM category;";
                $catResult = mysqli_query($connection, $catQuery);
                
    
        while ($catRow = mysqli_fetch_assoc($catResult)) {
        $catID = $catRow['category_name'];
        echo "<option value='$catID'>$catID</option>";

    }
    mysqli_data_seek($catResult, 0);
    echo "</select>";

                ?>
                <br>
                <input type="submit" name="catwrite" value="Writers Per Category" class="button">
                <input type="submit" name="catlend" value="Teacher Lenders Per Category" class="button">
            </div>
            <div class="form-group">
                <h2>Other Actions</h2>
                <input type="submit" name="teacherlend" value="Best Young Teacher Lenders" class="button">
                <br>
                <input type="submit" name="writernolend" value="Writers with No Lendings" class="button">
                <br>
                <input type="submit" name="samehandling" value="Handlers with Same Number of Lendings (>20)" class="button">
                <br>
                <input type="submit" name="catpairs" value="Best Category Pairs" class="button">
                <br>
                <input type="submit" name="littlewrite" value="Writers with Few Books (5 Less Than Max)" class="button">
            </div>
        </form>
    </div>
</body>
</html>
<?php
if (isset($_POST['hand'])) {
     
     $HandQuery ="SELECT first_name,last_name,school_id,user_id FROM users where activ=3;";
     $result= mysqli_query($connection, $HandQuery);
 
     echo "<h2>Pending Handler Requests : </h2>";
     echo "<form method='POST' action=''>";
     
             echo "<div class='table-container'>";
             echo "<table>";
             echo "<tr><th>Name</th><th>Last Name</th><th>School ID</th><th>Actions</th></tr>";
             while ($row = mysqli_fetch_assoc($result)) {
     echo "<tr>";
     echo "<td>".$row['first_name']."</td>";
     echo "<td>".$row['last_name']."</td>";
     echo "<td>".$row['school_id']."</td>";
     echo "<input type='hidden' name='us' value='".$row['user_id']."'>";
     echo "<td><input type='submit' name='Accept' value='Accept' class='button'>   <input type='submit' name='Reject' value='Reject' class='button'></td>";
     echo "<td>";
     echo "</tr>";
     
 }
 echo "</form>";
 echo "</table>";
}
if (isset($_POST['Accept'])) {
    $us = $_POST['us'];

    $haQuery ="UPDATE users SET activ=1 ,user_role='handler' where user_id='$us';";
    $result= mysqli_query($connection, $haQuery);

    if ($result!= 0) {
        echo 'Upgraded to handler successfully.';
    } else {
        echo 'Upgrade to handler failed.';
    }


    
    
}
if (isset($_POST['Reject'])) {
    $us = $_POST['us'];

    $haQuery ="UPDATE users SET activ=1 where user_id='$us';";
    $result= mysqli_query($connection, $haQuery);

    if ($result != 0) {
        echo 'Rejected successfully.';
    } else {
        echo 'Rejection failed.';
    }


    
    
}
if (isset($_POST['backup'])) {
    $backupFile = 'backup.sql';


$command = "C:\xampp\mysql\bin\mysqldump.exe --user={$username} --password={$password} --host={$host} --databases {$database} > {$backupFile}";


exec($command, $output, $returnVar);

if ($returnVar === 0) {
    echo 'Backup created successfully.';
} else {
    var_dump($output) ;
    echo 'Backup creation failed.';
}
    
    
}
if (isset($_POST['restore'])) {
    
$backupFile = 'backup.sql';


$command = "C:\xampp\mysql\bin\mysql --user={$username} --password={$password} --host={$host} {$database} < {$backupFile}";


exec($command, $output, $returnVar);

if ($returnVar === 0) {
    echo 'Database restored successfully.';
} else {
    echo 'Database restoration failed.';
}
    
    
}
if (isset($_POST['lendings'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];
    
    
    
    $LendQuery ="CALL SchoolLendings('$year','$month');";
    $result= mysqli_query($connection, $LendQuery);

    echo "<h2>Lendings Per School: </h2>";
    echo "Month : $month       Year : $year";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>School</th><th>ID</th><th>Number of lendings</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>".$row['school_name']."</td>";
    echo "<td>".$row['school_id']."</td>";
    echo "<td>".$row['cnt']."</td>";
    echo "<td>";
    echo "</tr>";
    
}
echo "</table>";
    
    
}
if (isset($_POST['catwrite'])) {
    $categor = $_POST['categor'];

    
    $CatQuery ="CALL CategoryWriters('$categor');";
    $result= mysqli_query($connection, $CatQuery);

    echo "<h2>Writers of category: </h2>";
    echo "Category : $categor";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>First Name</th><th>Last Name</th><th>ID</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>".$row['author_first_name']."</td>";
    echo "<td>".$row['author_last_name']."</td>";
    echo "<td>".$row['author_id']."</td>";
    echo "<td>";
    echo "</tr>";
    
}
echo "</table>";
    
    
}
if (isset($_POST['catlend'])) {
    $categor = $_POST['categor'];

    
    $CatQuery ="CALL CategoryLenders('$categor');";
    $result= mysqli_query($connection, $CatQuery);

    echo "<h2>Teachers that have lended from category in the past year : </h2>";
    echo "Category : $categor";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>First Name</th><th>Last Name</th><th>ID</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>".$row['first_name']."</td>";
    echo "<td>".$row['last_name']."</td>";
    echo "<td>".$row['user_id']."</td>";
    echo "<td>";
    echo "</tr>";
    
}
echo "</table>";
    
    
}
if (isset($_POST['teacherlend'])) {
   

    
    $teachQuery ="SELECT t.first_name,t.last_name,t.user_id, COUNT(t.user_id) AS 'lendings', DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),u.date_of_birth)), '%Y' ) + 0 AS 'age'
    FROM teacher_lends t INNER JOIN users u
    ON t.user_id = u.user_id AND DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),u.date_of_birth)), '%Y' ) + 0 < 40
    GROUP BY user_id
    ORDER BY lendings DESC
    LIMIT 3;";
    $result= mysqli_query($connection, $teachQuery);

    echo "<h2>Top 3 teachers with the most lendings under the age of 40: </h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>First Name</th><th>Last Name</th><th>ID</th><th>Lendings</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>".$row['first_name']."</td>";
    echo "<td>".$row['last_name']."</td>";
    echo "<td>".$row['user_id']."</td>";
    echo "<td>".$row['lendings']."</td>";
    echo "<td>";
    echo "</tr>";
    
}
echo "</table>";
    
    
}
if (isset($_POST['writernolend'])) {
   

    
    $nolendQuery ="SELECT a.author_first_name , a.author_last_name ,a.author_id
    FROM author as a
    LEFT JOIN lended_authors as lended
    ON lended.author_id = a.author_id
    WHERE lended.author_id IS NULL;";
    
    $result= mysqli_query($connection, $nolendQuery);

    echo "<h2>Writers with no lendings: </h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>First Name</th><th>Last Name</th><th>ID</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>".$row['author_first_name']."</td>";
    echo "<td>".$row['author_last_name']."</td>";
    echo "<td>".$row['author_id']."</td>";
    echo "<td>";
    echo "</tr>";
    
}
echo "</table>";
    
    
}
if (isset($_POST['samehandling'])) {
   

    
    $handleQuery ="SELECT  u.first_name,u.last_name,u.school_id,countsa,m.first_name as name2,m.last_name as lastname2,m.school_id as id2 FROM users u INNER JOIN(
        SELECT l.handler_id,count(l.handler_id) as countsa FROM lends l WHERE (DATEDIFF(NOW(),lend_date)<=365) GROUP BY l.handler_id HAVING (count(l.handler_id)>=20) 
        )as k
        ON k.handler_id=u.user_id INNER JOIN  (SELECT a.first_name,a.last_name,a.school_id,countsb FROM users a INNER JOIN(
        SELECT n.handler_id,count(n.handler_id) as countsb FROM lends n WHERE (DATEDIFF(NOW(),lend_date)<=365) GROUP BY n.handler_id HAVING (count(n.handler_id)>=20)) as h ON h.handler_id=a.user_id) as m
        ON countsa = countsb
        WHERE u.last_name != m.last_name
        group by countsa;";
    
    $result= mysqli_query($connection, $handleQuery);

    echo "<h2>Handlers with same amount of lending (more than 20) : </h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>First Name</th><th>Last Name</th><th>School ID</th><th>First Name</th><th>Last Name</th><th>School ID</th><th>Count</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>".$row['first_name']."</td>";
    echo "<td>".$row['last_name']."</td>";
    echo "<td>".$row['school_id']."</td>";
    echo "<td>".$row['name2']."</td>";
    echo "<td>".$row['lastname2']."</td>";
    echo "<td>".$row['id2']."</td>";
    echo "<td>".$row['countsa']."</td>";

    echo "<td>";
    echo "</tr>";
    
}
echo "</table>";
    
    
}
if (isset($_POST['littlewrite'])) {
   

    
    $littleQuery = "SELECT author_first_name,author_last_name , author_id ,written 
    FROM books_written 
    WHERE (SELECT MAX(written) FROM books_written)-written>=5;";
    
    $result= mysqli_query($connection, $littleQuery);

    echo "<h2>Writers with 5 or fewer books than writer with most books : </h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>First Name</th><th>Last Name</th><th>ID</th><th>Books Written</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>".$row['author_first_name']."</td>";
    echo "<td>".$row['author_last_name']."</td>";
    echo "<td>".$row['author_id']."</td>";
    echo "<td>".$row['written']."</td>";
    

    echo "<td>";
    echo "</tr>";
    
}
echo "</table>";
    
}

if (isset($_POST['catpairs'])) {
   

    
    $pairQuery = "SELECT c1.category_name as cat1, c2.category_name as cat2, COUNT(*) AS pair_count
    FROM belongs b1
    INNER JOIN belongs b2 ON b1.book_id = b2.book_id AND b1.category_id < b2.category_id
    INNER JOIN category c1 ON b1.category_id = c1.category_id
    INNER JOIN category c2 ON b2.category_id = c2.category_id
    INNER JOIN lends l ON b1.book_id = l.book_id
    GROUP BY c1.category_name, c2.category_name
    ORDER BY pair_count DESC
    LIMIT 3;";
    
    $result= mysqli_query($connection, $pairQuery);

    echo "<h2>Top 3 most frequent category pairs in lendings : </h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>First Category</th><th>Second Category</th><th>Count</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>".$row['cat1']."</td>";
    echo "<td>".$row['cat2']."</td>";
    echo "<td>".$row['pair_count']."</td>";
    
    

    echo "<td>";
    echo "</tr>";
    
}
echo "</table>";
    
    
}

?>
<!DOCTYPE html>
<div id="addBook">
  <h3>Add School</h3>
  <form method="POST" action="">
    <input type="text" name="name" placeholder="School Name">
    <input type="text" name="street" placeholder="Street name">
    <input type="text" name="apt_number" placeholder="Apt_number">
    <input type="text" name="city" placeholder="City">
    <input type="text" name="state" placeholder="State">
    <input type="text" name="zip-code" placeholder="Zip Code">
    <input type="text" name="mail" placeholder="E-mail adress">
    <input type="submit" value="Add" name="Add" class="button">
    <br>
    <p>Phone Numbers : <p>
    <input type="text" name="phone1" placeholder="Phone Number">
    <input type="text" name="phone2" placeholder="Phone Number">

  </form>
  </html>

  <?php
  if (isset($_POST['Add'])) {
    $name = $_POST['name'];
    $street = $_POST['street'];
    $aptnumber = $_POST['apt_number'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipcode = $_POST['zip-code'];
    $mail = $_POST['mail'];
    $ph1 = $_POST['phone1'];
    $ph2 = $_POST['phone2'];

    $check = "SELECT * FROM schools WHERE school_name='$name';";
    $result = mysqli_query($connection, $check);
    
    if (mysqli_num_rows($result) > 0){
        echo "School Already Exists";
    }
    else {

    
    $AddQuery ="INSERT INTO schools (school_name,street_name,apt_number,city,state,zip_code,email_address)
    VALUES ('$name','$street','$aptnumber','$city','$state','$zipcode','$mail');";
    
    $result= mysqli_query($connection, $AddQuery);
    if ($result) {
        echo "School added successfully.";
      } else {
        echo "Error adding school: " . mysqli_error($connection);
      }
      $check = "SELECT * FROM schools WHERE school_name='$name';";
      $result = mysqli_query($connection, $check);
      $scid= mysqli_fetch_assoc($result);
      $scid = $scid['school_id'];

      $AddQuery ="INSERT INTO phone_number (school_id,ph_number) VALUES ('$scid','$ph1');";
      $result= mysqli_query($connection, $AddQuery);
      if ($ph2!=""){
        $AddQuery ="INSERT INTO phone_number (school_id,ph_number) VALUES ('$scid','$ph2');";
        $result= mysqli_query($connection, $AddQuery);
      }
    
            }   
}
?>