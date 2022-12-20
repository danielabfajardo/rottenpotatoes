<?php 
require_once "connection.php";
session_start();

//GETs value for "url" and "id" parameters sent through the url
$url = $_GET['url'];
$movieID = $_GET['id'];

//ADD OR DELETE movie from user's WATCHLIST
//if no one is logged in, redirect to sign in page
if(isset($_POST['add_btn_wl']) || isset($_POST['del_btn_wl'])) {
    if(empty($_SESSION['user_id'])) {
        header("location: login.php");
    } else {
        $sql = "SELECT movie_id from watchlist WHERE movie_id=? AND user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $movieID, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if($result->num_rows > 0) {
            $sql = "DELETE FROM watchlist WHERE movie_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $movieID, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql = "INSERT INTO watchlist(movie_id, user_id) VALUES(?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $movieID, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
        }
    }
} 

//ADD or DELETE movie from user's WATCHEDLIST
//if no one is logged in, redirect to sign in page
if(isset($_POST['add_btn_wdl']) || isset($_POST['del_btn_wdl'])) {
    if(empty($_SESSION['user_id'])) {
        header("location: login.php");
    } else {
        $sql = "SELECT movie_id from watchedlist WHERE movie_id=? AND user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $movieID, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if($result->num_rows > 0) {
            $sql = "DELETE FROM watchedlist WHERE movie_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $movieID, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql = "INSERT INTO watchedlist(movie_id, user_id) VALUES(?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $movieID, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
        }
    }
} 

//UPDATE, INSERT, OR DELETE user's RATING for a movie
if(isset($_POST['rate_submit'])) {
    $myRating = $_POST['rating'];

    $sql = "SELECT rating FROM movie_rating WHERE user_id=? AND movie_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['user_id'], $movieID);
    $stmt->execute();
    $ratingResult = $stmt->get_result();
    $stmt->close();

    if($ratingResult->num_rows > 0) {
        $sql = "UPDATE movie_rating SET rating=? WHERE user_id=? AND movie_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iii', $myRating, $_SESSION['user_id'], $movieID);
        $stmt->execute();
        $stmt->close();
    } else {
        $sql = "INSERT INTO movie_rating VALUES(?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iii', $movieID, $_SESSION['user_id'], $myRating);
        $stmt->execute();
        $stmt->close();
    }
}

if(isset($_POST['rate_del'])) {
    $sql = "DELETE FROM movie_rating WHERE user_id=? AND movie_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['user_id'], $movieID);
    $stmt->execute();
    $stmt->close();
}

header("location: " .$url);
mysqli_free_result($result);
mysqli_free_result($ratingResult);
mysqli_close($conn);
?>