<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            margin-bottom: 10px;
        }

        img {
            max-width: 300px;
            margin-bottom: 10px;
        }

        p {
            margin-bottom: 5px;
        }

        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
        }

        input[type="number"] {
            width: 50px;
        }

        .form-buttons {
            margin-top: 10px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
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

    $quer = "SELECT * FROM users WHERE username='$username';";
    $user = mysqli_query($connection, $quer);
    $userrole = mysqli_fetch_assoc($user);
    $userrole = $userrole['user_role'];

    $quer = "SELECT * FROM users WHERE username='$username';";
    $user = mysqli_query($connection, $quer);
    $userid = mysqli_fetch_assoc($user);
    $userid = $userid['user_id'];


    if (isset($_POST['submit_review'])) {
        $bookId = intval($_POST['book_id']);
        $review_text = $_POST['review_text'];
        $review_rating = $_POST['review_rating'];

       
        $insertQuery = "INSERT into rates (user_id,book_id,review,likert,approved) VALUES ('$userid','$bookId','$review_text','$review_rating',0);";
        if (mysqli_query($connection, $insertQuery)) {
            echo "<p>Review submitted successfully!</p>";
            if($userrole=='teacher' OR $userrole=='student'){
            header("Location: users2.php");
            exit();
            }
            else{
                header("Location: handler.php");
            exit();
            }
        } else {
            echo "<p class='error-message'>Error: You have already reviewed this book!</p>";
        }
    }

    if (isset($_POST['show_reviews'])) {
        $bookId = intval($_POST['book_id']);
        
        $query = "SELECT review, likert FROM rates WHERE book_id = $bookId AND approved=1";
        $result = mysqli_query($connection, $query);

       
        if (mysqli_num_rows($result) > 0) {
            echo "<h3>Reviews</h3>";
            echo "<table class='results-table'>";
            echo "<tr><th>Review</th><th>Likert</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['review'] . "</td>";
                echo "<td>" . $row['likert'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No reviews available.</p>";
        }
    }

    if (isset($_POST['submit_booking'])) {
        $bookId = intval($_POST['book_id']);
        $userId = $userid;

    
        $insertQuery = "INSERT INTO bookings (user_id, book_id, booking_date) VALUES ('$userId', '$bookId', NOW())";

        
        if (mysqli_query($connection, $insertQuery)) {
            echo "<p>Booking made successfully!</p>";
            if($userrole=='teacher' OR $userrole=='student'){
                header("Location: users2.php");
                exit();
                }
                else{
                    header("Location: handler.php");
                exit();
                }
        } else {
            echo "<p class='error-message'>Error: Booking limit exceeded or Lended book overdue</p>";
        }
    }

    if (isset($_POST['submit'])) {
        if (isset($_POST['selected_book'])) {
            $selectedBook = $_POST['selected_book'];
            list($title, $author, $category) = explode("|", $selectedBook);

            $query = "SELECT ppu.publisher_name,b.book_id ,b.title,b.picture,b.isbn,b.pages,b.summary,b.book_language,aw.author_first_name,aw.author_last_name,cbe.category_name FROM books as b INNER JOIN ( 
                SELECT author_first_name,author_last_name,w.book_id FROM author as a INNER JOIN writes as w ON a.author_id = w.author_id) as aw ON b.book_id=aw.book_id
                INNER JOIN ( SELECT c.category_name,be.book_id FROM category as c INNER JOIN belongs as be ON c.category_id = be.category_id) as cbe ON cbe.book_id = b.book_id
                INNER JOIN ( SELECT p.publisher_name,pu.book_id FROM publisher as p INNER JOIN publishes as pu ON p.publisher_id = pu.publisher_id) as ppu ON ppu.book_id = b.book_id
                WHERE b.title='$title';";

            $result = mysqli_query($connection, $query);

            if (mysqli_num_rows($result) > 0) {
                $book = mysqli_fetch_assoc($result);
            }
            mysqli_data_seek($result, 0);

            $Categories = array();
            $Authors = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $Categories[] = $row['category_name'];
                $Authors[] = $row['author_last_name'];
                $Publishers[] = $row['publisher_name'];
            }
            mysqli_data_seek($result, 0);

            $Categories = array_unique($Categories);
            $Authors = array_unique($Authors);
            $Publishers = array_unique($Publishers);
        }

       
        echo "<h2>" . $book['title'] . "</h2>";
        echo "<img src='" . $book['picture'] . "' alt='Book Cover'>";
        echo "<p><strong>ISBN:</strong> " . $book['isbn'] . "</p>";
        echo "<p><strong>Pages:</strong> " . $book['pages'] . "</p>";
        echo "<p><strong>Summary:</strong> " . $book['summary'] . "</p>";
        echo "<p><strong>Language:</strong> " . $book['book_language'] . "</p>";
        echo "<p><strong>Authors:</strong> " . implode(', ', $Authors) . "</p>";
        echo "<p><strong>Categories:</strong> " . implode(', ', $Categories) . "</p>";
        echo "<p><strong>Publishers:</strong> " . implode(', ', $Publishers) . "</p>";

        
        echo "<h3>Review</h3>";
        echo "<form method='post'>";
        echo "<textarea name='review_text' placeholder='Enter your review'></textarea>";
        echo "<p>Rating (1-5): <input type='number' name='review_rating' min='1' max='5'></p>";
        echo "<input type='hidden' name='book_id' value='" . $book['book_id'] . "'>";
        echo "<div class='form-buttons'>";
        echo "<button type='submit' name='submit_review'>Submit Review</button>";
        echo "<button type='submit' name='submit_booking'>Make Booking</button>";
        echo "</div>";
        echo "</form>";

        echo "<form method='post'>";
    echo "<input type='hidden' name='book_id' value='". $book['book_id'] ."'>";
    echo "<button type='submit' name='show_reviews'>Show Reviews</button>";
    echo "</form>";



        
        

    }

   
    mysqli_close($connection);
    ?>
</body>

</html>