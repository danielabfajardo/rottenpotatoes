<?php @include "header.php"; ?>

<?php if(!empty($_SESSION['user_id'])) { ?>
<div class="list-box flex">
    <div class="list-box_half flex" id="wl-mov">
        <p class="label">My (To) Watchlist ♥</p>
        <div class="mylist flex">
            <?php 
                $sql = "SELECT w.movie_id, title, poster, avg_rating FROM watchlist as w JOIN movie as m ON w.movie_id = m.movie_id LEFT JOIN avg_ratings as ar ON ar.movie_id = w.movie_id WHERE w.user_id = ? GROUP BY w.movie_id ORDER BY timestmp DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) { ?>
                <div class="movie">
                    <div class="movie_poster">
                        <a href="movie.php?id=<?=$row["movie_id"]?>"><img class="mov" src="<?=$row["poster"]?>"></a>
                    </div>
                    <div class="movie_info flex">
                        <div class="movie_info_text">
                            <div class="title"><?php echo $row["title"];?></div>
                            <div class="rating">Rating: 
                                <?php for ($x = 1; $x <= round($row["avg_rating"]); $x++) echo "★"; for ($y = 1; $y <= 5-round($row["avg_rating"]); $y++) echo "☆"; ?>
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
                            <a href="#modal-one" class="like rate-btn" onclick="listsRate(<?=$row['movie_id']?>, 'wl-mov'); <?php if(!empty($rowi['rating'])){ echo 'modalDetails('.$rowi['rating'].');'; }?>"><?=$rowi['rating']?> ★</a>
                            <?php }} else { ?>
                            <a href="#modal-one" class="like rate-btn" onclick="listsRate(<?=$row['movie_id']?>, 'wl-mov')"> ★</a>
                            <?php } ?>
                            <form method="POST" action="mymovies.php?id=<?=$row['movie_id']?>&url=watchlist.php#wl-mov" class="btns-form flex">
                                <input type="submit" name="del_btn_wl" class="like del" value="♥-"/>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } } ?>
        </div>
    </div>
    <div class="list-box_half flex" id="wdl-mov">
        <p class="label">My Watchedlist ✓</p>
        <div class="top5 flex">
        <?php $sql = "SELECT w.movie_id, title, poster, avg_rating FROM watchedlist as w JOIN movie as m ON w.movie_id = m.movie_id LEFT JOIN avg_ratings as ar ON ar.movie_id = w.movie_id WHERE w.user_id = ? GROUP BY w.movie_id ORDER BY timestmp DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                    <div class="movie">
                        <div class="movie_poster">
                            <a href="movie.php?id=<?=$row["movie_id"]?>"><img class="mov" src="<?=$row["poster"]?>"></a>
                        </div>
                        <div class="movie_info flex">
                            <div class="movie_info_text">
                                <div class="title"><?php echo $row["title"];?></div>
                                <div class="rating">Rating: 
                                <?php for ($x = 1; $x <= round($row["avg_rating"]); $x++) echo "★"; for ($y = 1; $y <= 5-round($row["avg_rating"]); $y++) echo "☆";?>
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
                            <a href="#modal-one" class="like rate-btn" onclick="listsRate(<?=$row['movie_id']?>, 'wdl-mov'); <?php if(!empty($rowi['rating'])){ echo 'modalDetails('.$rowi['rating'].');'; }?>"><?=$rowi['rating']?> ★</a>
                            <?php }} else { ?>
                            <a href="#modal-one" class="like rate-btn" onclick="listsRate(<?=$row['movie_id']?>, 'wdl-mov')"> ★</a>
                            <?php } ?>
                                <form method="POST" action="mymovies.php?id=<?=$row['movie_id']?>&url=watchlist.php#wdl-mov" class="btns-form flex">
                                    <input type="submit" name="del_btn_wdl" class="like del" value="✓-"/>
                                </form>
                            </div>
                        </div>
                    </div>
            <?php } } ?>
        </div>
    </div>
    <div id="modal-one" class="modal">
        <div class="modal-dialog">
            <div class="modal-header flex">
                <h2 id="modal-h">How would you rate this movie?</h2>
                <a href="#" id="close-x" class="btn-close" onclick="delDisappear()">×</a>
            </div>
            <form method="POST" action="/" id="modalList">
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

<div class="footer flex">
    <p class="footer_text">Copyright © Rotten Potatoes. Developed by Daniela, Yoon Soo, and Jeffrey.</p>
</div>
<?php } else { header("location: login.php"); } ?>

<?php 
mysqli_free_result($result);
mysqli_free_result($resulti);
mysqli_close($conn); 
?>

