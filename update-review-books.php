<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['login']) == 0) {   
    header('location:index.php');
    exit(); // Add exit() after header redirect
} else { 
    if(!isset($_GET['book_name'])) {
        header('location:index.php'); // Redirect if book information is not available
        exit();
    }

    // Retrieve book information from URL parameters
    $book_name = $_GET['book_name'];
    $isbn = $_GET['isbn'];
    $issued_date = $_GET['issued_date'];
    $return_date = $_GET['return_date'];
    $fine = $_GET['fine'];
    $student_id = $_SESSION['stdid']; // Change to session student ID

    // Check if review already exists for the book
    $sql = "SELECT rating, review FROM tblrating WHERE ISBNNumber = :isbn AND studentID = :student_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':isbn', $isbn, PDO::PARAM_INT);
    $query->bindParam(':student_id', $student_id, PDO::PARAM_STR);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    // Handle form submission
    if(isset($_POST['submit'])) {
        $rating = $_POST['rating'];
        $review = $_POST['review'];

        if($row) {
            // If review exists, update it
            $sql = "UPDATE tblrating SET rating = :rating, review = :review WHERE ISBNNumber = :isbn AND studentID = :student_id";
        } else {
            // If review does not exist, insert it
            $sql = "INSERT INTO tblrating (ISBNNumber, studentID, rating, review) VALUES (:isbn, :student_id, :rating, :review)";
        }

        $query = $dbh->prepare($sql);
        $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $query->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $query->bindParam(':rating', $rating, PDO::PARAM_INT);
        $query->bindParam(':review', $review, PDO::PARAM_STR);
        $query->execute();

        // Optionally, you can add a success message or redirect the user to a confirmation page
        $_SESSION['success_msg'] = "Review updated successfully";
        header("location: issued-books.php");
        exit(); // Ensure script execution stops after redirecting
    }
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Review Page</title>
    <!-- CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php');?>
    
    <div class="content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <h2>Leave/Update a Review for <?php echo htmlentities($book_name); ?></h2>
                    <!-- Display Book Information -->
                    <div class="form-group">
                        <label>Book Name:</label>
                        <input type="text" class="form-control" value="<?php echo htmlentities($book_name); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>ISBN:</label>
                        <input type="text" class="form-control" value="<?php echo htmlentities($isbn); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Issued Date:</label>
                        <input type="text" class="form-control" value="<?php echo htmlentities($issued_date); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Return Date:</label>
                        <input type="text" class="form-control" value="<?php echo htmlentities($return_date); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Fine (USD):</label>
                        <input type="text" class="form-control" value="<?php echo htmlentities($fine); ?>" readonly>
                    </div>
                    <!-- Review Form -->
                    <form role="form" method="post">
                        <div class="form-group">
                            <label>Rating:</label>
                            <select class="form-control" name="rating" required>
                                <option value="">Select Rating</option>
                                <option value="1" <?php if($row && $row['rating'] == 1) echo 'selected'; ?>>1 Star</option>
                                <option value="2" <?php if($row && $row['rating'] == 2) echo 'selected'; ?>>2 Stars</option>
                                <option value="3" <?php if($row && $row['rating'] == 3) echo 'selected'; ?>>3 Stars</option>
                                <option value="4" <?php if($row && $row['rating'] == 4) echo 'selected'; ?>>4 Stars</option>
                                <option value="5" <?php if($row && $row['rating'] == 5) echo 'selected'; ?>>5 Stars</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Review:</label>
                            <textarea class="form-control" rows="5" name="review" required><?php echo $row ? htmlentities($row['review']) : ''; ?></textarea>
                        </div>
                        <?php if($row): ?>
                        <div class="form-group">
                            <label>Current Rating:</label>
                            <input type="text" class="form-control" value="<?php echo htmlentities($row['rating']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Current Review:</label>
                            <textarea class="form-control" rows="5" readonly><?php echo htmlentities($row['review']); ?></textarea>
                        </div>
                        <?php endif; ?>
                        <button type="submit" name="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                    <!-- End Review Form -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include('includes/footer.php');?>
    
    <!-- JavaScript -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
