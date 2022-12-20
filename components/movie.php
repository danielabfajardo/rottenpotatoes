<?php @include "header.php";
    $movieID = $_GET['id'];

    $sqli = "SELECT rating FROM movie_rating WHERE movie_id=? AND user_id=?";
    $stmti = $conn->prepare($sqli);
    $stmti->bind_param('ii', $movieID, $_SESSION['user_id']);
    $stmti->execute();
    $resulti = $stmti->get_result();
    $stmti->close(); 
    if($resulti->num_rows > 0) { 
        while($rowi = $resulti->fetch_assoc()) { 
            $starBtnVal = $rowi['rating']; 
        }
    }

    if(isset($_POST['review_submit'])) {
        $myReview = $_POST['myreview'];
        $myRating = $_POST['myrating'];

        $sql = "INSERT INTO movie_review VALUES(?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iis', $movieID, $_SESSION['user_id'], $myReview);
        $stmt->execute();
        $stmt->close();

        $sql = "SELECT rating FROM movie_rating WHERE user_id=? AND movie_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $_SESSION['user_id'], $movieID);
        $stmt->execute();
        $ratingResult = $stmt->get_result();
        $stmt->close();

        if($ratingResult->num_rows == 0) {
            $sql = "INSERT INTO movie_rating VALUES(?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iii', $movieID, $_SESSION['user_id'], $myRating);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql = "UPDATE movie_rating SET rating=? WHERE user_id=? AND movie_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iii', $myRating, $_SESSION['user_id'], $movieID);
            $stmt->execute();
            $stmt->close();
        } 
    } 

    if(isset($_POST['review_edit_submit'])) {
        $myReview = $_POST['myreview'];
        $myRating = $_POST['myrating'];

        $sql = "SELECT review, rating FROM movie_review AS mr JOIN movie_rating AS mra ON mra.user_id = mr.user_id AND mra.movie_id = mr.movie_id WHERE mr.user_id=? AND mr.movie_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $_SESSION['user_id'], $movieID);
        $stmt->execute();
        $stmt->bind_result($review, $rating);
        $result = $stmt->fetch();
        $stmt->close();

        if($myReview==="") {
            $review_error = "Cannot submit an empty review";
        } else if($myReview==$review && $myRating==$rating) {
            $review_error = "Nor the rating nor the review values changed.";
        } else {
            if($myRating!==$rating) {
                $sql = "UPDATE movie_rating SET rating=? WHERE user_id=? AND movie_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('iii', $myRating, $_SESSION['user_id'], $movieID);
                $stmt->execute();
                $stmt->close();
            }
            if($myReview!==$review) {
                $sql = "UPDATE movie_review SET review=? WHERE user_id=? AND movie_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sii', $myReview, $_SESSION['user_id'], $movieID);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    if(isset($_POST['del_review'])) {
        $sql = "DELETE FROM movie_rating WHERE user_id=? AND movie_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $_SESSION['user_id'], $movieID);
        $stmt->execute();
        $stmt->close();

        $sql = "DELETE FROM movie_review WHERE user_id=? AND movie_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $_SESSION['user_id'], $movieID);
        $stmt->execute();
        $stmt->close();
    }
?>

<div class="container flex">
    <?php $sql = "SELECT m.title, m.poster, m.description, AVG(rating) 
                FROM movie_rating as mr 
                JOIN movie as m 
                ON m.movie_id = mr.movie_id 
                WHERE mr.movie_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $movieID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { ?>
    <div class="label" id="moviephp-title"><?=$row["title"]?></div>
    <div class="moviephp-options flex">
        <div class="rating">Average Rating: 
            <?php for ($x = 1; $x <= round($row["AVG(rating)"]); $x++) echo "★ "; for ($y = 1; $y <= 5-round($row["AVG(rating)"]); $y++) echo "☆ ";?>
        </div>
        <div class="add-movie">
            <div class="flex add-movie_btns">
                <form method="POST" action="mymovies.php?id=<?=$movieID?>&url=movie.php?id=<?=$movieID?>" class="wl-wdl-wrap flex">
                    <?php if(!empty($_SESSION['user_id'])) {
                    $query = "SELECT movie_id FROM watchlist WHERE movie_id=? AND user_id=?";
                    $getQuery = $conn->prepare($query);
                    $getQuery->bind_param('ii', $movieID, $_SESSION['user_id']);
                    $getQuery->execute();
                    $queryResult = $getQuery->get_result();
                    $getQuery->close(); 
                    if($queryResult->num_rows>0){ ?>
                        <input type="submit" name="del_btn_wl" class="like del big" value="Watchlist ♥-"/>
                    <?php } else { ?>
                        <input type="submit" name="add_btn_wl" class="like add big" value="Watchlist ♥+"/>
                    <?php } 
                    $query = "SELECT movie_id FROM watchedlist WHERE movie_id=? AND user_id=?";
                    $getQuery = $conn->prepare($query);
                    $getQuery->bind_param('ii', $movieID, $_SESSION['user_id']);
                    $getQuery->execute();
                    $queryResult = $getQuery->get_result();
                    $getQuery->close(); 
                    if($queryResult->num_rows>0) { ?>
                        <input type="submit" name="del_btn_wdl" class="like del big" value="Watchedlist ✓-"/>
                    <?php } else { ?>
                        <input type="submit" name="add_btn_wdl" class="like add big" value="Watchedlist ✓+"/>
                    <?php } ?>
                </form>
                <a href="#modal-one" class="like rate-btn"><?php if(!empty($starBtnVal)) { echo $starBtnVal; }?> ★</a>
                <?php } else { ?>
                <a href="login.php" class="like add big">Watchlist ♥+</a>
                <a href="login.php" class="like add big">Watchedlist ✓+</a>
                <a href="login.php" class="like rate-btn">★</a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="movie flex">
        <div class="movie_poster flex">
            <img src="<?=$row['poster']?>"/>
        </div>
        <div class="movie_info flex">
            <div class="movie_info_desc"><?=$row["description"]?></div>
            <?php } } ?>
            <div class="movie_info_actors">
                <span class="cast">Cast:</span> 
                <?php $sql = "SELECT actor_name, role FROM actors as a LEFT JOIN movie_actors as ma ON a.actor_id = ma.actor_id WHERE movie_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $movieID);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) { ?>
                <?=$row['actor_name']?> <?php if($row['role']!==NULL){ echo "as ".$row['role']; } ?>, 
                <?php } } ?>
            </div>
        </div>
    </div>
    <div class="review">
        <div class="review_title">Reviews</div>
        <div class="review_mine">
            <?php if(empty($_SESSION['user_id'])) { ?>
                <div class="menu review-not-loggedin flex">
                    <div class="message">Sign in or set up an account to write a review</div>
                    <a href="login.php" class="menu_button flex menu_login">Sign in</a>
                </div>
            <?php } else { 
                $sql = "SELECT rating, review 
                FROM movie_review as mr 
                JOIN movie_rating as mra 
                ON mra.movie_id = mr.movie_id 
                AND mra.user_id = mr.user_id 
                WHERE mr.user_id=? AND mr.movie_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ii', $_SESSION['user_id'], $movieID);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        if(isset($_POST['edit_review'])) { ?>
                <div class="username">@<?=$_SESSION['username']?></div>
                <form method="POST">
                    <div class="input-box">
                        <textarea name="myreview" class="review-box"><?=$row['review']?></textarea>
                    </div>
                    <div class="options flex">
                        <div class="options_rate">
                            <label for="myrating" class="options_rate_label">My Rating:</label>
                            <select name="myrating" id="myrating" class="options_rate_box">
                                <?php for($x=1; $x<=5; $x++) {
                                    if($x == $row['rating']) { ?>
                                        <option value="<?=$row['rating']?>" selected><?=$row['rating']?> stars</option>
                                <?php } else { ?>
                                        <option value="<?=$x;?>"><?=$x;?> stars</option>
                                <?php } } ?> 
                            </select> 
                        </div>
                        <div class="options_submit">
                            <input type="submit" name="cancel_edit_submit" class="cancel_edit_btn" value="x"/>
                            <input type="submit" name="review_edit_submit" value="Submit"/>
                        </div>
                    </div>
                </form> 
                <?php } else { ?>
                <form method="POST">
                    <div class="top-info flex" id="non-edit-rev">
                        <div class="username">@<?=$_SESSION['username']?></div>
                        <div class="right flex">
                            <input type="submit" name="del_review" class="edit" value="Delete"/>
                            <input type="submit" name="edit_review" class="edit" value="Edit"/>
                            <div class="username"><?=$row['rating']?>/5 stars</div>
                        </div>
                    </div>
                    <div class="my-review"><?=$row['review']?></div>
                </form>
            <?php } } } else { ?>
                <div class="username">@<?=$_SESSION['username']?></div>               
                <form method="POST">
                    <div class="input-box">
                        <textarea name="myreview" class="review-box" placeholder="What did you think about the movie? ..." required></textarea>
                    </div>
                    <div class="options flex">
                        <div class="options_rate">
                            <label for="myrating" class="options_rate_label">My Rating:</label>
                            <?php if(empty($starBtnVal)) { ?>
                                <select name="myrating" id="myrating" class="options_rate_box">
                                    <option value="1" selected>1 stars</option>
                                    <option value="2">2 stars</option>
                                    <option value="3">3 stars</option>
                                    <option value="4">4 stars</option>
                                    <option value="5">5 stars</option>
                                </select>
                            <?php } else { ?>
                                <select name="myrating" id="myrating" class="options_rate_box">
                                    <?php for($x=1; $x<=5; $x++) {
                                        if($x == $starBtnVal) { ?>
                                            <option value="<?=$starBtnVal?>" selected><?=$starBtnVal?> stars</option>
                                    <?php } else { ?>
                                            <option value="<?=$x?>"><?=$x?> stars</option>
                                    <?php } } ?> 
                                </select> 
                            <?php } ?>
                        </div>
                        <div class="options_submit">
                            <input type="submit" name="review_submit" value="Submit"/>
                        </div>
                    </div>
                </form>
            <?php } } ?>
            <div class="error" id="err"><?php if(!empty($review_error)) { echo $review_error; }?></div>
        </div>
        <div class="review_all flex">
            <?php $sql = "SELECT u.username, mr.review, mra.rating FROM movie_review as mr JOIN users as u ON mr.user_id = u.user_id JOIN movie_rating as mra ON mr.user_id = mra.user_id AND mr.movie_id = mra.movie_id WHERE mr.movie_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $movieID);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        if($row['username'] == $_SESSION['username']) { continue; } else { ?>             
            <div class="review_all_box">
                <div class="title flex">
                    <div class="title_username">@<?=$row['username']?></div>
                    <div class="title_rating"><?=$row['rating']?>/5 stars</div>
                </div>
                <div class="text"><?=$row['review']?></div>
            </div>
            <?php } } } ?>
        </div>
    </div>
    <div id="modal-one" class="modal">
        <div class="modal-dialog">
            <div class="modal-header flex">
                <h2><?php if(!empty($starBtnVal)) { echo "Would you like to change your rating?"; } else { echo "How would you rate this movie?"; }?></h2>
                <a href="#" class="btn-close">×</a>
            </div>
            <form method="POST" action="mymovies.php?id=<?=$movieID?>&url=movie.php?id=<?=$movieID?>">
                <fieldset class="modal-body flex">
                    <span class="star-cb-group flex">
                        <?php for($i=1; $i<=5; $i++) { ?>
                            <input type="radio" name="rating" id="r<?=$i?>" value="<?=$i?>" <?php if($starBtnVal==$i){echo "checked='checked'";}?> required/><label for="r<?=$i?>"><?=$i?></label>
                        <?php } ?>                          
                    </span>
                </fieldset>
                <div class="modal-footer flex">
                    <?php if($result->num_rows == 0) {
                        if(!empty($starBtnVal)) { 
                            echo"<input type='submit' name='rate_del' class='modal-footer_btn delete' value='Delete'/>";
                            }
                        }
                    ?>
                    <input type="submit" name="rate_submit" class="modal-footer_btn" value="Submit"/>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
mysqli_free_result($result);
mysqli_free_result($queryResult);
mysqli_free_result($ratingResult);
mysqli_free_result($resulti);
mysqli_close($conn); 
?>

