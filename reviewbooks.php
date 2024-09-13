<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0) {   
    header('location:index.php');
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
    $student_id = $_GET['stdid']; 

    // Check if review already exists for the book
    $sql = "SELECT rating, review FROM tblrating WHERE ISBNNumber = :isbn AND studentID = :student_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':isbn', $isbn, PDO::PARAM_INT);
    $query->bindParam(':student_id', $student_id, PDO::PARAM_STR);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $existing_rating = '';
    $existing_review = '';
    if($row) {
        $existing_rating = $row['rating'];
        $existing_review = $row['review'];
    }
    
    // Handle form submission
    if(isset($_POST['submit'])) {
        $rating = $_POST['rating'];
        $review = $_POST['review'];
        $sql = "INSERT INTO tblrating (ISBNNumber, studentID, rating, review) VALUES (:ISBNNumber, :student_id, :rating, :review) ON DUPLICATE KEY UPDATE rating = :rating, review = :review";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ISBNNumber', $isbn, PDO::PARAM_INT);
        $query->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $query->bindParam(':rating', $rating, PDO::PARAM_INT);
        $query->bindParam(':review', $review, PDO::PARAM_STR);
        $query->execute();

        // Optionally, you can add a success message or redirect the user to a confirmation page
        $_SESSION['success_msg'] = "Review submitted successfully";
        header("location: issued-books.php");
        exit(); // Ensure script execution stops after redirecting
    }
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
                    <h2>Leave a Review for <?php echo htmlentities($book_name); ?></h2>
                    <!-- Display Existing Rating and Review -->
                    <?php if(!empty($existing_rating) && !empty($existing_review)): ?>
                        <div class="form-group">
                            <label>Existing Rating:</label>
                            <input type="text" class="form-control" value="<?php echo htmlentities($existing_rating); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Existing Review:</label>
                            <textarea class="form-control" rows="5" readonly><?php echo htmlentities($existing_review); ?></textarea>
                        </div>
                    <?php endif; ?>
                    <!-- End Display Existing Rating and Review -->
                    <!-- Review Form -->
                    <form role="form" method="post">
                        <div class="form-group">
                            <label>Rating:</label>
                            <select class="form-control" name="rating" required>
                                <option value="">Select Rating</option>
                                <option value="1">1 Star</option>
                                <option value="2">2 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Review:</label>
                            <textarea class="form-control" rows="5" name="review" required></textarea>
                        </div>
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
<?php  ?>
