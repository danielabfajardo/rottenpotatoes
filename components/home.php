<?php @include "header.php"; ?>

<div class="wrapper">
    <p class="top-title">Explore</p>
    <div class="movie-box flex">
        <div class="movie-box_half flex">
            <p class="small-label">Most Watched Movie This Week</p>
            <div class="big-movie flex">
                <?php $sql = "SELECT COUNT(w.user_id), m.movie_id, title, poster, avg_rating FROM watchedlist AS w JOIN movie as m ON m.movie_id = w.movie_id LEFT JOIN ( SELECT movie_id, avg_rating FROM avg_ratings GROUP BY movie_id ) AS t ON t.movie_id = w.movie_id WHERE timestmp >= DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) GROUP BY title ORDER BY COUNT(w.user_id) DESC LIMIT 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close(); 
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $movieName = $row["title"];
                            $moviePoster = $row["poster"];
                            $movieId = $row["movie_id"];
                            $movieRating = $row["avg_rating"];
                        }
                    } ?>
                <div class="big-movie_poster">
                    <a href="movie.php"><img src="<?=$moviePoster?>"></a>
                </div>
                <div class = "big-movie_info flex" onclick="location.href='movie.php?id=<?=$movieId?>'">
                    <div class="title"><?php echo $movieName;?></div>
                    <div class="rating">Rating: 
                        <?php for ($x = 1; $x <= floor($movieRating); $x++) echo "★"; for ($y = 1; $y <= 5-floor($movieRating); $y++) echo "☆";?>
                    </div>
                </div>
            </div>
        </div>
        <div class="movie-box_half flex">
            <p class="small-label">Newest Movie</p>
            <div class="big-movie flex">
                <div class="big-movie_poster">
                    <?php $sql = "SELECT m.movie_id, title, poster, avg_rating FROM movie AS m LEFT JOIN avg_ratings AS ar ON ar.movie_id = m.movie_id GROUP BY movie_id ORDER BY release_date DESC LIMIT 1";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $movieName = $row["title"];
                                $moviePoster = $row["poster"];
                                $movieId = $row["movie_id"];
                                $movieRating = $row["avg_rating"];
                            }
                        } ?>
                    <img src="<?=$moviePoster?>">
                </div>
                <div class="big-movie_info flex" onclick="location.href='movie.php?id=<?=$movieId?>'">
                    <div class="title"><?php echo $movieName; ?></div>
                    <div class="rating">Rating: 
                        <?php for ($x = 1; $x <= floor($movieRating); $x++) echo "★"; for ($y = 1; $y <= 5-floor($movieRating); $y++) echo "☆"; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="list-box_half flex" id="home-mov">
        <p class="label home-list-label">Discover New Movies</p>
        <div class="explore flex">
        <?php if(!empty($_SESSION['username'])) {
        $sql = "SELECT m.movie_id, title, poster, AVG(rating) FROM movie as m LEFT JOIN movie_rating as mra ON mra.movie_id = m.movie_id WHERE m.movie_id NOT IN ( SELECT w.movie_id FROM watchlist as w WHERE w.user_id = ? UNION SELECT wd.movie_id FROM watchedlist as wd WHERE wd.user_id = ? ) GROUP BY m.movie_id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $_SESSION['user_id'], $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { ?>
                <div class="movie">
                    <div class="movie_poster">
                        <a href="movie.php?id=<?=$row["movie_id"]?>"><img class="mov" src="<?=$row['poster']?>"></a>
                    </div>
                    <div class="movie_info flex">
                        <div class="movie_info_text">
                            <div class="title"><?=$row['title']?></div>
                            <div class="rating">Rating: 
                                <?php for ($x = 1; $x <= round($row["AVG(rating)"]); $x++) echo "★"; for ($y = 1; $y <= 5-round($row["AVG(rating)"]); $y++) echo "☆";?>
                            </div>
                        </div>
                        <div class="movie_info_btns flex"> 
                        <?php $sqli = "SELECT rating FROM movie_rating WHERE movie_id=? AND user_id=?";
                            $stmti = $conn->prepare($sqli);
                            $stmti->bind_param('ii', $row['movie_id'], $_SESSION['user_id']);
                            $stmti->execute();
                            $resulti = $stmti->get_result();
                            $stmti->close(); 
                            if($resulti->num_rows > 0) { 
                                while($rowi = $resulti->fetch_assoc()) { ?>
                            <a href="#modal-one" class="like rate-btn" onclick="homeRate(<?=$row['movie_id']?>, 'home-mov'); <?php if(!empty($rowi['rating'])){ echo 'modalDetails('.$rowi['rating'].');'; }?>"><?=$rowi['rating']?> ★</a>
                            <?php }} else { ?>
                            <a href="#modal-one" class="like rate-btn" onclick="homeRate(<?=$row['movie_id']?>, 'home-mov')"> ★</a>
                            <?php } ?>
                            <form method="POST" action="mymovies.php?id=<?=$row['movie_id']?>&url=home.php#home-mov" class="btns-form flex">
                                <input type="submit" name="add_btn_wl" class="like add" value="♥+"/>
                                <input type="submit" name="add_btn_wdl" class="like add" value="✓+"/>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } } ?>
            <?php } else {
            $sql = "SELECT m.movie_id, title, poster, AVG(rating) FROM (SELECT movie_id, title, poster FROM movie ORDER BY rand() LIMIT 15) as m LEFT JOIN movie_rating as mr ON mr.movie_id = m.movie_id GROUP BY m.movie_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) { ?>
                    <div class="movie">
                        <div class="movie_poster">
                            <a href="movie.php?id=<?=$row["movie_id"]?>"><img class="mov" src="<?=$row['poster']?>"></a>
                        </div>
                        <div class="movie_info flex">
                            <div class="movie_info_text">
                                <div class="title"><?php echo $row['title']; ?></div>
                                <div class="rating">Rating: 
                                    <?php for ($x = 1; $x <= round($row["AVG(rating)"]); $x++) echo "★"; for ($y = 1; $y <= 5-round($row["AVG(rating)"]); $y++) echo "☆"; ?>
                                </div>
                            </div>
                            <div class="movie_info_btns flex">
                                <a href="login.php" class="like rate-btn">★</a>
                                <div class="btns-form flex">
                                    <a href="login.php" class="like add">♥+</a>
                                    <a href="login.php" class="like add">✓+</a>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php } } } ?>
        </div>
        <div id="modal-one" class="modal">
            <div class="modal-dialog">
                <div class="modal-header flex">
                    <h2 id="modal-h">How would you rate this movie?</h2>
                    <a href="#home-mov" class="btn-close" onclick="delDisappear()">×</a>
                </div>
                <form method="POST" action="/" id="modalHome">
                    <fieldset class="modal-body flex">
                        <span class="star-cb-group flex">
                            <?php for($i=1; $i<=5; $i++) { ?>
                                <input type="radio" name="rating" id="r<?=$i?>" value="<?=$i?>" required/><label for="r<?=$i?>"><?=$i?></label>
                            <?php } ?>                          
                        </span>
                    </fieldset>
                    <div class="modal-footer flex">
                        <div id="del-appear"></div>
                        <input type="submit" name="rate_submit" class="modal-footer_btn" value="Submit" onclick="delDisappear()"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="footer flex">
    <p class="footer_text">Copyright © Rotten Potatoes. Developed by Daniela, Yoon Soo, and Jeffrey.</p>
</div>

<?php 
mysqli_free_result($result);
mysqli_free_result($resulti);
mysqli_close($conn); 
?>



