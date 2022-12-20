<?php @include "header.php"; 

if(isset($_GET['submit_search'])) { 
    $fsearch = $_GET['search'];	

    $sql = "SELECT m.movie_id, title, poster, avg_rating FROM movie AS m LEFT JOIN avg_ratings AS ar ON ar.movie_id = m.movie_id WHERE title LIKE CONCAT('%',?,'%') GROUP BY m.movie_id UNION SELECT m.movie_id, title, poster, avg_rating FROM movie_keyw_view AS mkv JOIN movie AS m ON m.movie_id = mkv.movie_id LEFT JOIN avg_ratings AS ar ON ar.movie_id = mkv.movie_id WHERE keyword LIKE CONCAT('%',?,'%') GROUP BY m.movie_id UNION SELECT m.movie_id, title, poster, avg_rating FROM movie_categ_view as mcv JOIN movie AS m ON m.movie_id = mcv.movie_id LEFT JOIN avg_ratings AS ar ON ar.movie_id = mcv.movie_id WHERE category LIKE CONCAT('%',?,'%') GROUP BY m.movie_id UNION SELECT m.movie_id, title, poster, avg_rating FROM movie_actors_view AS mav JOIN movie AS m ON m.movie_id = mav.movie_id LEFT JOIN avg_ratings AS ar ON ar.movie_id = mav.movie_id WHERE actor_name LIKE CONCAT('%',?,'%') GROUP BY m.movie_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $fsearch, $fsearch, $fsearch, $fsearch);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} ?>

<div class="list-box_half flex">
    <p class="label">Search Results</p>
    <div class="list search-results flex">
        <?php 
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
        ?>
        <div class="movie">
            <div class="movie_poster">
                <a href="movie.php?id=<?=$row["movie_id"]?>"><img class="mov" src="<?=$row['poster']?>"></a>
            </div>
            <div class="movie_info flex">
                <div class="movie_info_text">
                    <div class="title"><?=$row['title'];?></div>
                    <div class="rating">Rating: 
                        <?php 
                            for ($x = 1; $x <= round($row['avg_rating']); $x++) echo "★";
                            for ($y = 1; $y <= 5-round($row['avg_rating']); $y++) echo "☆";
                        ?>
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
                    <a href="#modal-one" class="like rate-btn" onclick="resultsRate(<?=$row['movie_id']?>); <?php if(!empty($rowi['rating'])){ echo 'modalDetails('.$rowi['rating'].');'; }?>"><?=$rowi['rating']?> ★</a>
                    <?php }} else { ?>
                    <a href="#modal-one" class="like rate-btn" onclick="resultsRate(<?=$row['movie_id']?>)"> ★</a>
                    <?php } ?>   
                    <form method="POST" action="mymovies.php?id=<?=$row['movie_id']?>&url=results.php" class="btns-form flex">
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
    <div id="modal-one" class="modal">
        <div class="modal-dialog">
            <div class="modal-header flex">
                <h2 id="modal-h">How would you rate this movie?</h2>
                <a href="#" id="close-x" class="btn-close" onclick="delDisappear()">×</a>
            </div>
            <form method="POST" action="/" id="modalResult">
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
