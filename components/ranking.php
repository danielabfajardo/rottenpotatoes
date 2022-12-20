<?php @include "header.php"?>

<div class="list-box flex">
    <div class="showstats flex">
        <a href="statistics.php" class="showstats_btn">Learn More</a>
    </div>
    <div class="list-box_half flex top5-ranking" id="rarank-mov">
        <p class="label">Top 5 Best Rated Movies</p>
        <div class="top5 flex">
            <?php
            $sql = "SELECT ar.movie_id, title, poster, avg_rating, RANK() OVER (ORDER BY avg_rating DESC) AS rank FROM avg_ratings as ar JOIN movie as m ON ar.movie_id = m.movie_id GROUP BY ar.movie_id ORDER BY rank LIMIT 5";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close(); 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) { ?>
                <div class="movie">
                    <div class="movie_poster">
                        <a href="movie.php?id=<?=$row["movie_id"]?>" id="<?=$row["movie_id"]?>" onclick="movieInfo(this);"><img class="mov" src="<?php echo $row["poster"];?>"></a>
                    </div>
                    <div class="movie_info flex">
                        <div class="movie_info_text flex">
                            <div class="title"><?php echo $row["title"];?></div>
                            <div class="rating">Rating: 
                                <?php for ($x = 1; $x <= round($row["avg_rating"]); $x++) echo "★"; for ($y = 1; $y <= 5-round($row["avg_rating"]); $y++) echo "☆"; ?>
                            </div>
                        </div>
                        <div class="movie_info_btns flex">
                        <?php if(!empty($_SESSION['user_id'])) { 
                            $sqli = "SELECT rating FROM movie_rating WHERE movie_id=? AND user_id=?";
                            $stmti = $conn->prepare($sqli);
                            $stmti->bind_param('ii', $row['movie_id'], $_SESSION['user_id']);
                            $stmti->execute();
                            $resulti = $stmti->get_result();
                            $stmti->close(); 
                            if($resulti->num_rows > 0) { 
                                while($rowi = $resulti->fetch_assoc()) { ?>
                            <a href="#modal-one" class="like rate-btn" onclick="ranksRate(<?=$row['movie_id']?>, 'rarank-mov'); <?php if(!empty($rowi['rating'])){ echo 'modalDetails('.$rowi['rating'].');'; }?>"><?=$rowi['rating']?> ★</a>
                            <?php }} else { ?>
                            <a href="#modal-one" class="like rate-btn" onclick="ranksRate(<?=$row['movie_id']?>, 'rarank-mov')"> ★</a>
                            <?php } ?>
                            <form method="POST" action="mymovies.php?id=<?=$row['movie_id']?>&url=ranking.php#rarank-mov" class="btns-form flex">
                                <?php $iquery = "SELECT movie_id FROM watchlist WHERE movie_id=? AND user_id=?";
                                    $istmt = $conn->prepare($iquery);
                                    $istmt->bind_param('ii', $row["movie_id"], $_SESSION['user_id']);
                                    $istmt->execute();
                                    $iresult = $istmt->get_result();
                                    $istmt->close(); 
                                    if($iresult->num_rows>0){ ?>
                                        <input type="submit" name="del_btn_wl" class="like del" value="♥-"/>
                                    <?php } else { ?>
                                        <input type="submit" name="add_btn_wl" class="like add" value="♥+"/>
                                    <?php }  
                                    $iquery = "SELECT movie_id FROM watchedlist WHERE movie_id=? AND user_id=?";
                                    $istmt = $conn->prepare($iquery);
                                    $istmt->bind_param('ii', $row["movie_id"], $_SESSION['user_id']);
                                    $istmt->execute();
                                    $iresult = $istmt->get_result();
                                    $istmt->close(); 
                                    if($iresult->num_rows>0){ ?>
                                        <input type="submit" name="del_btn_wdl" class="like del" value="✓-"/>
                                    <?php } else { ?>
                                        <input type="submit" name="add_btn_wdl" class="like add" value="✓+"/>
                                <?php } ?>
                            </form>
                            <?php } else { ?>
                                <a href="login.php" class="like rate-btn">★</a>
                                <div class="btns-form flex">
                                    <a href="login.php" class="like add">♥+</a>
                                    <a href="login.php" class="like add">✓+</a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } } ?>
        </div>
    </div>
    <div class="list-box_half flex top5-ranking" id="rera-mov">
        <p class="label">Top 5 Most Reviewed Movies</p>
        <div class="top5 flex">
            <?php
                $sql = "SELECT mr.movie_id, title, poster, COUNT(review) AS reviews, RANK() OVER (ORDER BY reviews DESC) as rank FROM movie_review as mr JOIN movie as m ON mr.movie_id = m.movie_id GROUP BY mr.movie_id ORDER BY rank LIMIT 5";
                $stmt = $conn->prepare($sql);
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
                        <div class="movie_info_text flex">
                            <div class="title"><?php echo $row["title"];?></div>
                            <div class="rating">Reviews: <?php echo $row["reviews"];?></div>
                        </div>
                        <div class="movie_info_btns flex">
                        <?php if(!empty($_SESSION['user_id'])) {  
                            $sqli = "SELECT rating FROM movie_rating WHERE movie_id=? AND user_id=?";
                            $stmti = $conn->prepare($sqli);
                            $stmti->bind_param('ii', $row['movie_id'], $_SESSION['user_id']);
                            $stmti->execute();
                            $resulti = $stmti->get_result();
                            $stmti->close(); 
                            if($resulti->num_rows > 0) { 
                                while($rowi = $resulti->fetch_assoc()) { ?>
                            <a href="#modal-one" class="like rate-btn" onclick="ranksRate(<?=$row['movie_id']?>, 'rera-mov'); <?php if(!empty($rowi['rating'])){ echo 'modalDetails('.$rowi['rating'].');'; }?>"><?=$rowi['rating']?> ★</a>
                            <?php }} else { ?>
                            <a href="#modal-one" class="like rate-btn" onclick="ranksRate(<?=$row['movie_id']?>, 'rera-mov')"> ★</a>
                            <?php } ?>
                            <form method="POST" action="mymovies.php?id=<?=$row['movie_id']?>&url=ranking.php#rera-mov" class="btns-form flex">
                                <?php $iquery = "SELECT movie_id FROM watchlist WHERE movie_id=? AND user_id=?";
                                    $istmt = $conn->prepare($iquery);
                                    $istmt->bind_param('ii', $row["movie_id"], $_SESSION['user_id']);
                                    $istmt->execute();
                                    $iresult = $istmt->get_result();
                                    $istmt->close(); 
                                    if($iresult->num_rows>0){ ?>
                                        <input type="submit" name="del_btn_wl" class="like del" value="♥-"/>
                                    <?php } else { ?>
                                        <input type="submit" name="add_btn_wl" class="like add" value="♥+"/>
                                    <?php } ?>
                                    <?php 
                                    $iquery = "SELECT movie_id FROM watchedlist WHERE movie_id=? AND user_id=?";
                                    $istmt = $conn->prepare($iquery);
                                    $istmt->bind_param('ii', $row["movie_id"], $_SESSION['user_id']);
                                    $istmt->execute();
                                    $iresult = $istmt->get_result();
                                    $istmt->close(); 
                                    if($iresult->num_rows>0){ ?>
                                        <input type="submit" name="del_btn_wdl" class="like del" value="✓-"/>
                                    <?php } else { ?>
                                        <input type="submit" name="add_btn_wdl" class="like add" value="✓+"/>
                                <?php } ?>
                            </form>
                            <?php } else { ?>
                                <a href="login.php" class="like rate-btn">★</a>
                                <div class="btns-form flex">
                                    <a href="login.php" class="like add">♥+</a>
                                    <a href="login.php" class="like add">✓+</a>
                                </div>
                            <?php } ?>
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
            <form method="POST" action="/" id="modalRank">
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

<?php 
mysqli_free_result($result);
mysqli_free_result($iresult);
mysqli_free_result($resulti);
mysqli_close($conn); 
?>


