<?php @include "header.php" ?>
<?php session_start();
    require_once "connection.php"; ?>

<div class="welcome-wrap">
    <div class="welcometxt flex">
        <span class="welcome">Welcome to<br>Rotten Potatoes! <?php echo $_SESSION['user_id']; ?></span>
        <img src="../images/potato.png"> <br>
        <div class="greet">Elevate your movie life with Rotten Potatoes!</div>
    </div>
        
    <div class ="homebutton">
        <a href="home.php" class="hollow red">Go home</a>
        <a href="login.php" class="hollow blue">Sign in</a>
    </div>
</div>

<?php mysqli_close($conn); ?>