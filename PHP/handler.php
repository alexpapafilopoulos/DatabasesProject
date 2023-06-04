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

$schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
            $schoolResult = mysqli_query($connection, $schoolQuery);
            $schoolRow = mysqli_fetch_assoc($schoolResult);
            $schoolID = $schoolRow['school_id'];

             
             $usersQuery = "SELECT * FROM users WHERE school_id = '$schoolID' AND activ=1;";
             $usersResult = mysqli_query($connection, $usersQuery);

if (isset($_POST['info'])) {
    
    if ($userrole == 'student'){
        header("Location: info.php");
        exit();
    } else if ($userrole == 'teacher'){
        header("Location: infocustom.php");
        exit();
    }

}

if (isset($_POST['return'])) {
    $userID = $_POST['user_id'];
    $BookID = $_POST['book_id'];
    
    $ReturnQuery = "IF (SELECT return_date from lends WHERE user_id = '$userID' AND book_id='$BookID') IS NULL THEN UPDATE lends SET return_date = NOW() WHERE user_id = '$userID' AND book_id='$BookID'; 
    UPDATE has SET quantity=quantity+1 where school_id = '$schoolID' AND book_id='$BookID'; END IF;";
    mysqli_query($connection, $ReturnQuery);
    
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['lend'])) {
    $schoolhandler = "SELECT user_id FROM users WHERE username = '$username';";
    $handlerid = mysqli_query($connection, $schoolhandler);
    $handlerrow = mysqli_fetch_assoc($handlerid);
    $handlerid = $handlerrow['user_id'];
    $bookID = $_POST['book_id'];
    $userID = $_POST['user_id'];
    $ApproveBookingQuery = "INSERT INTO lends (user_id, book_id, lend_date, return_date, handler_id) VALUES ('$userID','$bookID',NOW(), NULL, '$handlerid');";
    mysqli_query($connection, $ApproveBookingQuery);
    $deleteBookingQuery = "DELETE FROM bookings WHERE book_id = '$bookID' AND user_id = '$userID';";
    mysqli_query($connection, $deleteBookingQuery);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['Approve'])) {
    $bookID = $_POST['book_id'];
    $userID = $_POST['user_id'];
    $ApproveRatingQuery = "UPDATE rates SET approved = 1 WHERE user_id='$userID' AND book_id='$bookID'; ";
    mysqli_query($connection, $ApproveRatingQuery);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['delete'])) {
    $bookID = $_POST['book_id'];
    $userID = $_POST['user_id'];
    
    $deleteBookingQuery = "DELETE FROM bookings WHERE book_id = '$bookID' AND user_id = '$userID';";
    mysqli_query($connection, $deleteBookingQuery);
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['Deny'])) {
    $bookID = $_POST['book_id'];
    $userID = $_POST['user_id'];
    
    $deleteBookingQuery = "DELETE FROM rates WHERE book_id = '$bookID' AND user_id = '$userID';";
    mysqli_query($connection, $deleteBookingQuery);
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['lend_book'])) {
    $userforlend = $_POST['userforlend'];
    $selectedBook = $_POST['lend_book'];
    $schoolhandler = "SELECT user_id FROM users WHERE username = '$username';";
    $handlerid = mysqli_query($connection, $schoolhandler);
    $handlerrow = mysqli_fetch_assoc($handlerid);
    $handlerid = $handlerrow['user_id'];
    $findid = "SELECT book_id FROM books WHERE title = '$selectedBook';";
    $bookid = mysqli_query($connection, $findid);
    $selectedid = mysqli_fetch_assoc($bookid);
    $selectedid = $selectedid['book_id'];
    $LendQuery = "INSERT INTO lends (user_id, book_id, lend_date, return_date, handler_id) VALUES ('$userforlend','$selectedid',NOW(), NULL, '$handlerid');";
    mysqli_query($connection, $LendQuery);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();

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
            <h2>Logged in as: <?php echo $username; ?></h2>
        </div>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
                <input type="submit" name="school_users" value="School Users" class="button">
                <input type="submit" name="pending_registrations" value="Pending Registrations" class="button">
                <input type="submit" name="pending_ratings" value="Pending Ratings" class="button">
                <input type="submit" name="pending_bookings" value="Pending Bookings" class="button">
                <br>
                <label for="uname">Search User Lendings (Leave blank for all):</label>
                <input type="text" name="uname" id="uname">
                <input type="submit" name="all_lendings" value="All lendings" class="button">
                <input type="submit" name="overdue_lendings" value="Delayed lendings" class="button">
            </div>
            <div class="form-group">
                <label for="newpassword">Change Password:</label>
                <input type="text" name="newpassword" id="newpassword">
                <input type="submit" name="passw" value="Change" class="button">
    </div>
    <div class="form-group">
                <label for="categor">Category:</label>
                <?php
                $catQuery = "SELECT * FROM category;";
    $catResult = mysqli_query($connection, $catQuery);
    echo "<select name='categor' id='categor'>";
    while ($catRow = mysqli_fetch_assoc($catResult)) {
        $catID = $catRow['category_name'];
        echo "<option value='$catID'>$catID</option>";

    }
    mysqli_data_seek($catResult, 0);
    echo "</select>";

                ?>
                <input type="submit" name="categ" value="Category Avg Rating" class="button">
                <label for="usrid">User ID:</label>
                <?php
                echo "<select name='usrid'>";
                while ($userRow = mysqli_fetch_assoc($usersResult)) {
                $userID = $userRow['user_id'];
                echo "<option value='$userID'>$userID</option>";
                }
                ?>
                <input type="submit" name="usr" value="User Avg Rating" class="button">
                
            </div>
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
                <label for="category">Availability:</label>
                <select name="availability" id="availability">
                    <option value="Everything">Everything</option>
                    <option value="Available">Available</option>

                </select>
            </div>
            <div>
                <input type="submit" value="Search" name="search" class="button">
            </div>
            <h2>Add Books</h2>
<div id="addAuthor">
  <h3>Add Author</h3>
  <form method="POST" action="">
    <input type="text" name="firstName" placeholder="First Name">
    <input type="text" name="lastName" placeholder="Last Name">
    <input type="submit" value="Add" name="author" class="button">
  </form>
</div>
<div id="addPublisher">
  <h3>Add Publisher</h3>
  <form method="POST" action="">
    <input type="text" name="name" placeholder="Publisher">
    <input type="submit" value="Add" name="publisher" class="button">
  </form>
</div>
<div id="addCategory">
  <h3>Add Category</h3>
  <form method="POST" action="">
    <input type="text" name="cate" placeholder="Category">
    <input type="submit" value="Add" name="ctg" class="button">
  </form>
</div>



<div id="addBook">
  <h3>Add Book</h3>
  <form method="POST" action="">
    <input type="text" name="title" placeholder="title">
    <input type="text" name="isbn" placeholder="13-digit isbn">
    <input type="text" name="pages" placeholder="pages">
    <input type="text" name="summary" placeholder="summary">
    <input type="text" name="language" placeholder="language">
    <input type="text" name="picture" placeholder="picture url">
    <?php

    
     
    echo "<br>";
    echo "Author(s) : ";
    $AuthorQuery = "SELECT * FROM author;";
    $AuthorResult = mysqli_query($connection, $AuthorQuery);
    echo "<select name='author1'>";
    echo "<option value='-'>-</option>";
    while ($AuthorRow = mysqli_fetch_assoc($AuthorResult)) {
        $AuthorID = $AuthorRow['author_last_name'];
        echo "<option value='$AuthorID'>$AuthorID</option>";

    }
    mysqli_data_seek($AuthorResult, 0);
    echo "</select>";
    echo "<select name='author2'>";
    echo "<option value='-'>-</option>";
    while ($AuthorRow = mysqli_fetch_assoc($AuthorResult)) {
        $AuthorID = $AuthorRow['author_last_name'];
        echo "<option value='$AuthorID'>$AuthorID</option>";

    }
    mysqli_data_seek($AuthorResult, 0);
    echo "</select>";
    echo "<select name='author3'>";
    echo "<option value='-'>-</option>";
    while ($AuthorRow = mysqli_fetch_assoc($AuthorResult)) {
        $AuthorID = $AuthorRow['author_last_name'];
        echo "<option value='$AuthorID'>$AuthorID</option>";

    }
    
    mysqli_data_seek($AuthorResult, 0);
    echo "</select>";
    
    echo "<br>";
    echo "Categories : ";
    $catQuery = "SELECT * FROM category;";
    $catResult = mysqli_query($connection, $catQuery);
    echo "<select name='category1'>";
    echo "<option value='-'>-</option>";
    while ($catRow = mysqli_fetch_assoc($catResult)) {
        $catID = $catRow['category_name'];
        echo "<option value='$catID'>$catID</option>";

    }
    mysqli_data_seek($catResult, 0);
    echo "</select>";
    echo "<select name='category2'>";
    echo "<option value='-'>-</option>";
    while ($catRow = mysqli_fetch_assoc($catResult)) {
        $catID = $catRow['category_name'];
        echo "<option value='$catID'>$catID</option>";

    }
    mysqli_data_seek($catResult, 0);
    echo "</select>";
    echo "<select name='category3'>";
    echo "<option value='-'>-</option>";
    while ($catRow = mysqli_fetch_assoc($catResult)) {
        $catID = $catRow['category_name'];
        echo "<option value='$catID'>$catID</option>";

    }
    
    mysqli_data_seek($catResult, 0);
    echo "</select>";
    
    echo "<br>";
    echo "Publisher(s) : ";
    $PubQuery = "SELECT * FROM publisher;";
    $PubResult = mysqli_query($connection, $PubQuery);
    echo "<select name='pub1'>";
    echo "<option value='-'>-</option>";
    while ($PubRow = mysqli_fetch_assoc($PubResult)) {
        $PubID = $PubRow['publisher_name'];
        echo "<option value='$PubID'>$PubID</option>";

    }
    mysqli_data_seek($PubResult, 0);
    echo "</select>";
    echo "<select name='pub2'>";
    echo "<option value='-'>-</option>";
    while ($PubRow = mysqli_fetch_assoc($PubResult)) {
        $PubID = $PubRow['publisher_name'];
        echo "<option value='$PubID'>$PubID</option>";

    }
    mysqli_data_seek($PubResult, 0);
    echo "</select>";
    echo "<select name='pub3'>";
    echo "<option value='-'>-</option>";
    while ($PubRow = mysqli_fetch_assoc($PubResult)) {
        $PubID = $PubRow['publisher_name'];
        echo "<option value='$PubID'>$PubID</option>";

    }
    mysqli_data_seek($PubResult, 0);
    echo "</select>";
    ?>
    <input type="submit" value="Add" name="book" class="button">
  </form>
</div>

<div id="DeleteBook">
  <h3>Delete Book From Database</h3>
  <form method="POST" action="">
    <input type="text" name="title1" placeholder="title">
    <input type="submit" value="Delete" name="del" class="button">
    
        </form>



        <?php
      
    if (isset($_POST['author'])) { 
      $firstname = $_POST["firstName"];
      $lastname = $_POST["lastName"];
if($firstname=="" or $lastname==""){}
else{

  
  $check = "SELECT * FROM author WHERE author_first_name='$firstname' AND author_last_name='$lastname';";
  $result = mysqli_query($connection, $check);

  if (mysqli_num_rows($result) > 0){
    echo "Author already exists";
  }
  else{

  
  $query = "INSERT INTO  author (author_first_name, author_last_name) VALUES ('$firstname', '$lastname')";
  $result = mysqli_query($connection, $query);

  if ($result) {
    echo "Author added succesfully";
  } else {
    echo "Error adding author";
    
  }
}
}

}
if (isset($_POST['ctg'])) { 
    $cat = $_POST["cate"];
    
if($cat==""){}
else{


$check = "SELECT * FROM category WHERE category_name='$cat';";
$result = mysqli_query($connection, $check);

if (mysqli_num_rows($result) > 0){
  echo "Category already exists";
}
else{


$query = "INSERT INTO  category (category_name) VALUES ('$cat');";
$result = mysqli_query($connection, $query);

if ($result) {
  echo "Category added succesfully";
} else {
  echo "Error adding category";
  
}
}
}

}
    

    if (isset($_POST['del'])) { 
        $t = $_POST["title1"];
  
    
    $check = "DELETE FROM books WHERE title='$t';";
    $result = mysqli_query($connection, $check);
  
    if ($result) {
      echo "Book deleted successfully.";
    } else {
      echo "Error deleting book: " . mysqli_error($connection);
    }
  }
      

    
if (isset($_POST['publisher'])) { 
    $firstname = $_POST["name"];
    if($firstname==""){}
else{


$check = "SELECT * FROM publisher WHERE publisher_name='$firstname';";
$result = mysqli_query($connection, $check);

if (mysqli_num_rows($result) > 0){
  echo "Publisher already exists";
}
else{

$query = "INSERT INTO  publisher (publisher_name) VALUES ('$firstname')";
$result = mysqli_query($connection, $query);

if ($result) {
  echo "Publisher added successfully.";
} else {
  echo "Error adding publisher: " . mysqli_error($connection);
}

}
}



    }

    if (isset($_POST['book'])) { 
        $title= $_POST["title"];
        $isbn= $_POST["isbn"];
        $pages= $_POST["pages"];
        $summary= $_POST["summary"];
        $language= $_POST["language"];
        $picture= $_POST["picture"];
    
    $check = "SELECT * FROM books WHERE title='$title' OR isbn='$isbn'";
    $result = mysqli_query($connection, $check);
    
    if (mysqli_num_rows($result) > 0){
        $check = "SELECT DISTINCT * FROM books WHERE title='$title' OR isbn='$isbn';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
        $schoolResult = mysqli_query($connection, $schoolQuery);
        $schoolRow = mysqli_fetch_assoc($schoolResult);
        $schoolID = $schoolRow['school_id'];

        $check = "SELECT * FROM has WHERE book_id='$boid' AND school_id='$schoolID';";
        $result = mysqli_query($connection, $check);

        if (mysqli_num_rows($result) > 0){
        $quan = "UPDATE has SET quantity=quantity+1 WHERE book_id='$boid' AND school_id='$schoolID';";
        $result = mysqli_query($connection, $quan);

        }
        else{
            $quan = "INSERT INTO has (book_id,school_id,quantity) VALUES ('$boid','$schoolID',1);";
            $result = mysqli_query($connection, $quan);

        }

      echo "Book already exists , added a copy to this school";

    }
    else{
    
    $query = "INSERT INTO  books (title,isbn,pages,summary,book_language,picture) VALUES ('$title','$isbn','$pages','$summary','$language','$picture');";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
        $schoolResult = mysqli_query($connection, $schoolQuery);
        $schoolRow = mysqli_fetch_assoc($schoolResult);
        $schoolID = $schoolRow['school_id'];

        $check = "SELECT * FROM has WHERE book_id='$boid' AND school_id='$schoolID';";
        $result = mysqli_query($connection, $check);

        if (mysqli_num_rows($result) > 0){
        $quan = "UPDATE has SET quantity=quantity+1 WHERE book_id='$boid' AND school_id='$schoolID';";
        $result = mysqli_query($connection, $quan);

        }
        else{
            $quan = "INSERT INTO has (book_id,school_id,quantity) VALUES ('$boid','$schoolID',1);";
            $result = mysqli_query($connection, $quan);

        }
      echo "Book added successfully.";
    } else {
      echo "Error adding book: " . mysqli_error($connection);
    }
    $auth1=$_POST["author1"];
    $auth2=$_POST["author2"];
    $auth3=$_POST["author3"];
    if($auth1!="-"){
        $check = "SELECT * FROM author WHERE author_last_name='$auth1';";
        $result = mysqli_query($connection, $check);
        $authid= mysqli_fetch_assoc($result);
        $authid = $authid['author_id'];

        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $query = "INSERT INTO  writes (author_id,book_id) VALUES ('$authid','$boid');";
        $result = mysqli_query($connection, $query);
    }
    if($auth2!="-"){
        $check = "SELECT * FROM author WHERE author_last_name='$auth2';";
        $result = mysqli_query($connection, $check);
        $authid= mysqli_fetch_assoc($result);
        $authid = $authid['author_id'];

        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $query = "INSERT INTO  writes (author_id,book_id) VALUES ('$authid','$boid');";
        $result = mysqli_query($connection, $query);
    }
    if($auth3!="-"){
        $check = "SELECT * FROM author WHERE author_last_name='$auth3';";
        $result = mysqli_query($connection, $check);
        $authid= mysqli_fetch_assoc($result);
        $authid = $authid['author_id'];

        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $query = "INSERT INTO  writes (author_id,book_id) VALUES ('$authid','$boid');";
        $result = mysqli_query($connection, $query);
    }
    $c1=$_POST["category1"];
    $c2=$_POST["category2"];
    $c3=$_POST["category3"];
    if($c1!="-"){
        $check = "SELECT * FROM category WHERE category_name='$c1';";
        $result = mysqli_query($connection, $check);
        $cid= mysqli_fetch_assoc($result);
        $cid = $cid['category_id'];

        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $query = "INSERT INTO  belongs (category_id,book_id) VALUES ('$cid','$boid');";
        $result = mysqli_query($connection, $query);
    }
    if($c2!="-"){
        $check = "SELECT * FROM category WHERE category_name='$c2';";
        $result = mysqli_query($connection, $check);
        $cid= mysqli_fetch_assoc($result);
        $cid = $cid['category_id'];

        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $query = "INSERT INTO  belongs (category_id,book_id) VALUES ('$cid','$boid');";
        $result = mysqli_query($connection, $query);
    }
    if($c3!="-"){
        $check = "SELECT * FROM category WHERE category_name='$c3';";
        $result = mysqli_query($connection, $check);
        $cid= mysqli_fetch_assoc($result);
        $cid = $cid['category_id'];

        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $query = "INSERT INTO  belongs (category_id,book_id) VALUES ('$cid','$boid');";
        $result = mysqli_query($connection, $query);
    }
    $p1=$_POST["pub1"];
    $p2=$_POST["pub2"];
    $p3=$_POST["pub3"];
    if($p1!="-"){
        $check = "SELECT * FROM publisher WHERE publisher_name='$p1';";
        $result = mysqli_query($connection, $check);
        $pid= mysqli_fetch_assoc($result);
        $pid = $pid['publisher_id'];

        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $query = "INSERT INTO  publishes (publisher_id,book_id) VALUES ('$pid','$boid');";
        $result = mysqli_query($connection, $query);
    }
    if($p2!="-"){
        $check = "SELECT * FROM publisher WHERE publisher_name='$p2';";
        $result = mysqli_query($connection, $check);
        $pid= mysqli_fetch_assoc($result);
        $pid = $pid['publisher_id'];

        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $query = "INSERT INTO  publishes (publisher_id,book_id) VALUES ('$pid','$boid');";
        $result = mysqli_query($connection, $query);
    }
    if($p3!="-"){
        $check = "SELECT * FROM publisher WHERE publisher_name='$p3';";
        $result = mysqli_query($connection, $check);
        $pid= mysqli_fetch_assoc($result);
        $pid = $pid['publisher_id'];

        $check = "SELECT * FROM books WHERE title='$title';";
        $result = mysqli_query($connection, $check);
        $boid= mysqli_fetch_assoc($result);
        $boid = $boid['book_id'];

        $query = "INSERT INTO  publishes (publisher_id,book_id) VALUES ('$pid','$boid');";
        $result = mysqli_query($connection, $query);
    }


    }
    
    
    
        }
      
      
      
      
      if (isset($_POST['search'])) {
            $title = $_POST['title'];
            $author = $_POST['author'];
            $category = $_POST['category'];
            $availability = $_POST['availability'];

            $schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
            $schoolResult = mysqli_query($connection, $schoolQuery);
            $schoolRow = mysqli_fetch_assoc($schoolResult);
            $schoolID = $schoolRow['school_id'];

             
             $usersQuery = "SELECT * FROM users WHERE school_id = '$schoolID' AND activ=1;";
             $usersResult = mysqli_query($connection, $usersQuery);

        
            $query = "CALL SchoolBooks('$username');";
            mysqli_query($connection, $query);

            $query = "CALL AvailableFilteredSchoolBooks('$title','$author','$category','$availability');";
            $result = mysqli_query($connection, $query);

            

           

            
            echo "<h2>Available Books:</h2>";
            echo "<form action='bookcustom.php' method='POST'>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>Title</th><th>Author</th><th>Category</th><th>Quantity</th><th>Select Book</th><th>Book Details</th><th>Select User</th><th>Lend</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
    echo "<td>".$row['title']."</td>";
    echo "<td>".$row['author_last_name']."</td>";
    echo "<td>".$row['category_name']."</td>";
    echo "<td>".$row['quantity']."</td>";
    echo "<td><input type='radio' name='selected_book' value='".$row['title']."|".$row['author_last_name']."|".$row['category_name']."'></td>";
    echo "<td><button type='submit' name='submit'>See Book Details</button></td>";
    echo "<td>";
    echo "</form>";
    echo "<form action='handler.php' method='POST'>";
    echo "<select name='userforlend'>";
    while ($userRow = mysqli_fetch_assoc($usersResult)) {
        $userID = $userRow['user_id'];
        echo "<option value='$userID'>$userID</option>";

    }
    mysqli_data_seek($usersResult, 0);
    echo "</select>";
    echo "</td>";
    echo "<td><button type='submit' name='lend_book' value='".$row['title']."'>Lend</button></td>";
    echo "</tr>";
    echo "</tr>";
    echo "</form>";
            }
    echo "<form action='bookcustom.php' method='POST'>";
            
            echo "</table>";
            echo "<button type='submit' name='submit'>See Book Details</button>";
            echo "</div>";
            echo "</form>";
        }
    ?>
            </div>
        </form>

        <?php
        if (isset($_POST['pending_bookings'])) {
            
            $schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
            $schoolResult = mysqli_query($connection, $schoolQuery);
            $schoolRow = mysqli_fetch_assoc($schoolResult);
            $schoolID = $schoolRow['school_id'];
        
            
            $pendingBookingsQuery = "SELECT user_id, book_id, booking_date FROM bookings WHERE user_id IN (SELECT user_id FROM users WHERE school_id = '$schoolID');";
            $pendingBookingsResult = mysqli_query($connection, $pendingBookingsQuery);
        
            echo "<h2>Pending Bookings:</h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>User ID</th><th>Book ID</th><th>Booking Date</th><th>Action</th></tr>";
            while ($row = mysqli_fetch_assoc($pendingBookingsResult)) {
                echo "<tr>";
                echo "<td>".$row['user_id']."</td>";
                echo "<td>".$row['book_id']."</td>";
                echo "<td>".$row['booking_date']."</td>";
                echo "<td>";
                echo "<form method='POST' action='".$_SERVER['PHP_SELF']."'>";
                echo "<input type='hidden' name='book_id' value='".$row['book_id']."'>";
                echo "<input type='hidden' name='user_id' value='".$row['user_id']."'>";
                echo "<input type='submit' name='lend' value='Lend' class='button'>";
                echo "<input type='submit' name='delete' value='Delete' class='button'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
                
            }
            echo "</table>";
            echo "</div>";

            
        }
        if (isset($_POST['pending_ratings'])) {
            
            $schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
            $schoolResult = mysqli_query($connection, $schoolQuery);
            $schoolRow = mysqli_fetch_assoc($schoolResult);
            $schoolID = $schoolRow['school_id'];
        
            
            $pendingRatingsQuery = "SELECT user_id, book_id, review, likert FROM rates WHERE approved = 0 AND user_id IN (SELECT user_id FROM users WHERE school_id = '$schoolID');";
            $pendingRatingsResult = mysqli_query($connection, $pendingRatingsQuery);
        
            echo "<h2>Pending Ratings:</h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>User ID</th><th>Book ID</th><th>Review</th><th>Score</th><th>Action</th></tr>";
            while ($row = mysqli_fetch_assoc($pendingRatingsResult)) {
                echo "<tr>";
                echo "<td>".$row['user_id']."</td>";
                echo "<td>".$row['book_id']."</td>";
                echo "<td>".$row['review']."</td>";
                echo "<td>".$row['likert']."</td>";
                echo "<td>";
                echo "<form method='POST' action='".$_SERVER['PHP_SELF']."'>";
                echo "<input type='hidden' name='book_id' value='".$row['book_id']."'>";
                echo "<input type='hidden' name='user_id' value='".$row['user_id']."'>";
                echo "<input type='submit' name='Approve' value='Approve' class='button'>";
                echo "<input type='submit' name='Deny' value='Deny' class='button'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
                
            }
            echo "</table>";
            echo "</div>";

            
        }
        





        if (isset($_POST['school_users'])) {
            
            $schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
            $schoolResult = mysqli_query($connection, $schoolQuery);
            $schoolRow = mysqli_fetch_assoc($schoolResult);
            $schoolID = $schoolRow['school_id'];

           
            $usersQuery = "SELECT * FROM users WHERE school_id = '$schoolID' AND activ=1;";
            $usersResult = mysqli_query($connection, $usersQuery);

            echo "<h2>School Users:</h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>Username</th><th>First Name</th><th>Last Name</th><th>Actions</th></tr>";
            while ($row = mysqli_fetch_assoc($usersResult)) {
                echo "<tr>";
                echo "<td>".$row['username']."</td>";
                echo "<td>".$row['first_name']."</td>";
                echo "<td>".$row['last_name']."</td>";
                echo "<td>";
                echo "<form method='POST' action='infoother.php'>";
                echo "<input type='hidden' name='user_id' value='".$row['user_id']."'>";
                echo "<input type='submit' name='info' value='User Info' class='button'>";
                echo "</form>";
                echo "<form method='POST' action='".$_SERVER['PHP_SELF']."'>";
                echo "<input type='hidden' name='user_id' value='".$row['user_id']."'>";
                echo "<input type='submit' name='deactivate' value='Deactivate' class='button'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        }

        if (isset($_POST['pending_registrations'])) {
            $schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
            $schoolResult = mysqli_query($connection, $schoolQuery);
            $schoolRow = mysqli_fetch_assoc($schoolResult);
            $schoolID = $schoolRow['school_id'];
            
            $pendingQuery = "SELECT * FROM users WHERE school_id = '$schoolID' AND activ = 2;";
            $pendingResult = mysqli_query($connection, $pendingQuery);

            echo "<h2>Pending Registrations:</h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>Username</th><th>First Name</th><th>Last Name</th><th>Actions</th></tr>";
            while ($row = mysqli_fetch_assoc($pendingResult)) {
                echo "<tr>";
                echo "<td>".$row['username']."</td>";
                echo "<td>".$row['first_name']."</td>";
                echo "<td>".$row['last_name']."</td>";
                echo "<td>";
                echo "<form method='POST' action='".$_SERVER['PHP_SELF']."'>";
                echo "<input type='hidden' name='user_id' value='".$row['user_id']."'>";
                echo "<input type='submit' name='activate' value='Activate' class='button'>";
                echo "<input type='submit' name='reject' value='Reject' class='button'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        }

        if (isset($_POST['all_lendings'])) {
            $uname = $_POST['uname'];
            $schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
            $schoolResult = mysqli_query($connection, $schoolQuery);
            $schoolRow = mysqli_fetch_assoc($schoolResult);
            $schoolID = $schoolRow['school_id'];
            
            If($uname==""){
            $LendingQuery = "SELECT k.lend_date,k.return_date ,u.user_id,u.first_name,u.last_name,k.book_id FROM users as u INNER JOIN(
                SELECT l.user_id,l.lend_date,l.book_id,l.return_date FROM lends as l
                )AS k
                ON k.user_id=u.user_id
                INNER JOIN users ub ON ub.school_id=u.school_id WHERE ub.username='$username' ;";
            }
            else{
                $LendingQuery = "SELECT k.lend_date,k.return_date ,u.user_id,u.first_name,u.last_name,k.book_id FROM users as u INNER JOIN(
                    SELECT l.user_id,l.lend_date,l.book_id,l.return_date FROM lends as l
                    )AS k
                    ON k.user_id=u.user_id
                    INNER JOIN users ub ON ub.school_id=u.school_id AND u.username='$uname' WHERE ub.username='$username' ;";


            }
            $LendingResult = mysqli_query($connection, $LendingQuery);

            echo "<h2>All lendings:</h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>First Name</th><th>Last Name</th><th>Book ID</th><th>Lend Date</th><th>Return Date</th><th>Action</th></tr>";
            while ($row = mysqli_fetch_assoc($LendingResult)) {
                
                echo "<tr>";
                echo "<td>".$row['first_name']."</td>";
                echo "<td>".$row['last_name']."</td>";
                echo "<td>".$row['book_id']."</td>";
                echo "<td>".$row['lend_date']."</td>";
                echo "<td>".$row['return_date']."</td>";
                echo "<td>";
                echo "<form method='POST' action='".$_SERVER['PHP_SELF']."'>";
                echo "<input type='hidden' name='user_id' value='".$row['user_id']."'>";
                echo "<input type='hidden' name='book_id' value='".$row['book_id']."'>";
                echo "<input type='submit' name='return' value='Returned' class='button'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        }


        if (isset($_POST['overdue_lendings'])) {
            $uname = $_POST['uname'];
            $schoolQuery = "SELECT school_id FROM users WHERE username = '$username';";
            $schoolResult = mysqli_query($connection, $schoolQuery);
            $schoolRow = mysqli_fetch_assoc($schoolResult);
            $schoolID = $schoolRow['school_id'];
            
            $LendingQuery = "CALL delayeded('$username','$uname');";
            $LendingResult = mysqli_query($connection, $LendingQuery);

            echo "<h2>Delayed lendings:</h2>";
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th>First Name</th><th>Last Name</th><th>Book ID</th><th>Days Delayed</th><th>Action</th></tr>";
            while ($row = mysqli_fetch_assoc($LendingResult)) {
        
                echo "<tr>";
                echo "<td>".$row['first_name']."</td>";
                echo "<td>".$row['last_name']."</td>";
                echo "<td>".$row['book_id']."</td>";
                echo "<td>".$row['Delay']."</td>";
                echo "<td>";
                echo "<form method='POST' action='".$_SERVER['PHP_SELF']."'>";
                echo "<input type='hidden' name='user_id' value='".$row['user_id']."'>";
                echo "<input type='hidden' name='book_id' value='".$row['book_id']."'>";
                echo "<input type='submit' name='return' value='Returned' class='button'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        }

       


        if (isset($_POST['deactivate'])) {
            $userID = $_POST['user_id'];
          
            $deactivateQuery = "UPDATE users SET activ = 0 WHERE user_id = '$userID';";
            mysqli_query($connection, $deactivateQuery);
           
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }

        if (isset($_POST['activate'])) {
            $userID = $_POST['user_id'];
            
            $activateQuery = "UPDATE users SET activ = 1 WHERE user_id = '$userID';";
            mysqli_query($connection, $activateQuery);
           
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }

        if (isset($_POST['reject'])) {
            $userID = $_POST['user_id'];
            
            $activateQuery = "DELETE FROM users  WHERE user_id = '$userID';";
            mysqli_query($connection, $activateQuery);
            
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }

        if (isset($_POST['passw'])) {
            $newpassword = $_POST['newpassword'];
            
            $passwordQuery = "UPDATE users SET pass = '$newpassword' WHERE username = '$username';";
            mysqli_query($connection, $passwordQuery);
            
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }

        if (isset($_POST['categ'])) {
            $cat = $_POST['categor'];
            
            $CatQuery = "CALL Category_Average_Rating('$cat');";
            $catavg = mysqli_query($connection, $CatQuery);
            $catav = mysqli_fetch_assoc($catavg);
            $catav = number_format((float)$catav['AVG(r.likert)'],2,'.','');
            
            echo "<h2>$cat Average Rating: $catav</h2>";
        }

        if (isset($_POST['usr'])) {
            $usr = $_POST['usrid'];
            $rating = "CALL User_Average_Rating('$usr');";
            $userrate = mysqli_query($connection, $rating);
            $rate = mysqli_fetch_assoc($userrate);
            $rate = number_format((float)$rate['AVG(likert)'],2,'.','');
            
            echo "<h2>User with ID : $usr Average Rating: $rate</h2>";
        }

        
        mysqli_close($connection);
        ?>
    </div>
</body>
</html>
