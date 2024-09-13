<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['login']) == 0) {   
    header('location:index.php');
} else { 
    if(isset($_GET['del'])) {
        $id = $_GET['del'];
        try {
            $sql = "DELETE FROM tblbooks WHERE id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_STR);
            $query->execute();
            $_SESSION['delmsg'] = "Book deleted successfully";
            header('location:manage-books.php');
        } catch(PDOException $e) {
            $_SESSION['delmsg'] = "An error occurred: " . $e->getMessage();
            header('location:manage-books.php');
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
    <title>Online Library Management System | Manage Issued Books</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- DATATABLE STYLE  -->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Books</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- Search Form -->
                    <form method="GET" class="form-inline" style="margin-bottom: 15px;">
                        <div class="form-group">
                            <select class="form-control" name="search_item">
                                <option value="BookName">Book Name</option>
                                <option value="ISBNNumber">ISBN</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="search" class="form-control" placeholder="Search...">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                    <!-- End Search Form -->
                    <!-- Table Displaying Books -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Book Name</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                    <th>ISBN</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $search = isset($_GET['search']) ? $_GET['search'] : '';
                                $search_item = isset($_GET['search_item']) ? $_GET['search_item'] : 'BookName';
                                try {
                                    $sql="SELECT b.id, b.BookName, c.CategoryName, a.AuthorName, b.ISBNNumber 
                                          FROM tblbooks b 
                                          LEFT JOIN tblcategory c ON b.CatId = c.id 
                                          LEFT JOIN tblauthors a ON b.AuthorId = a.id 
                                          WHERE b.$search_item LIKE :search";

                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':search', $search, PDO::PARAM_STR);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt = 1;
                                    if($query->rowCount() > 0) {
                                        foreach($results as $result) {
                                            // Fetch rating and review for each book
                                            // $bookId = $result->ISBN;
                                            $ratingSql = "SELECT AVG(Rating) AS AvgRating, GROUP_CONCAT(Review) AS Reviews FROM tblrating WHERE ISBNNumber = :ISBNNumber";
                                            $ratingQuery = $dbh->prepare($ratingSql);
                                            $ratingQuery->bindParam(':ISBNNumber', $isbn, PDO::PARAM_INT);
                                            $ratingQuery->execute();
                                            $ratingResult = $ratingQuery->fetch(PDO::FETCH_ASSOC);
                                            ?>                                      
                                            <tr class="odd gradeX">
                                                <td class="center"><?php echo htmlentities($cnt);?></td>
                                                <td class="center"><?php echo htmlentities($result->BookName);?></td>
                                                <td class="center"><?php echo htmlentities($result->CategoryName);?></td>
                                                <td class="center"><?php echo htmlentities($result->AuthorName);?></td>
                                                <td class="center"><?php echo htmlentities($result->ISBN);?></td>
                                                <td class="center"><?php echo htmlentities($ratingResult['AvgRating']);?></td>
                                                <td class="center"><?php echo htmlentities($ratingResult['Reviews']);?></td>
                                            </tr>
                                            <?php 
                                            $cnt = $cnt + 1;
                                        }
                                    } 
                                } catch(PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                                ?>                                      
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table Displaying Books -->
                </div>
            </div>
        </div>
    </div>
    <!-- CONTENT-WRAPPER SECTION END-->
    <?php include('includes/footer.php');?>
    <!-- FOOTER SECTION END-->
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
    <!-- CORE JQUERY  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
    <!-- DATATABLE SCRIPTS  -->
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
</body>
</html>

<?php 
} 
?>
