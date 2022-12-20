<?php @include "header.php"; ?>

<div class="categ-box list-box flex">
    <div class="categ flex">
        <form method="GET" class="categ_top-box flex" id="categ_btn_form">
            <?php $sql = "SELECT category FROM categories";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close(); 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) { ?>
            <input type="submit" class="categ_btn" name="category" value="<?=$row['category']?>" id="<?=$row['category']?>">
            <?php } } ?>
        </form>
    </div>
    <div class="list-box_half flex">
        <div class="label">Discover movies by Category</div>
        <div class="list flex">
        <?php if(isset($_GET['category'])) {
            $category = $_GET['category'];	

            $sql = "SELECT mcv.movie_id, title, poster, category, avg_rating FROM movie_categ_view AS mcv LEFT JOIN movie as m ON m.movie_id = mcv.movie_id LEFT JOIN ( SELECT m.movie_id, avg_rating FROM avg_ratings AS ar JOIN movie as m ON m.movie_id = ar.movie_id GROUP BY ar.movie_id ) AS mra ON mra.movie_id = mcv.movie_id WHERE mcv.category = ? GROUP BY mcv.movie_id";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $category);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close(); 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
        ?>
            <div class="movie">
                <div class="movie_poster">
                    <a href="movie.php?id=<?=$row["movie_id"]?>"><img class="mov" src="<?=$row['poster']?>"></a>
                </div>
                <div class="movie_info flex">
                    <div class="movie_info_text">
                        <div class="title"><?=$row['title']?></div>
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
                            <a href="#modal-one" class="like rate-btn" onclick="categRate(<?=$row['movie_id']?>, <?=$row['category']?>); <?php if(!empty($rowi['rating'])){ echo 'modalDetails('.$rowi['rating'].');'; }?>"><?=$rowi['rating']?> ★</a>
                            <?php }} else { ?>
                            <a href="#modal-one" class="like rate-btn" onclick="categRate(<?=$row['movie_id']?>, <?=$row['category']?>)"> ★</a>
                            <?php } ?>
                        <form method="POST" action="mymovies.php?id=<?=$row["movie_id"]?>&url=categories.php?category=<?=$category?>" class="btns-form flex">
                            <?php 
                                $iquery = "SELECT movie_id FROM watchlist WHERE movie_id=? AND user_id=?";
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
        <?php } } } ?>
        <div id="modal-one" class="modal">
            <div class="modal-dialog">
                <div class="modal-header flex">
                    <h2 id="modal-h">How would you rate this movie?</h2>
                    <a href="#" id="close-x" class="btn-close" onclick="delDisappear()">×</a>
                </div>
                <form method="POST" action="/" id="modalCateg">
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
mysqli_free_result($iresult);
mysqli_free_result($resulti);
mysqli_close($conn);
 ?>



