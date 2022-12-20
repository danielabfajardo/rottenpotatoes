<?php @include "header.php"; ?>
<?php @include "form.php"; ?>

<div class="user-box">
    <div class="user">
        <form method='POST' action='' class="user_register flex" id="signin">
            <p class="user_title">Sign in</p>
            <input type="text" name="username" placeholder="Username" class="user_input" required/>
            <input type="password" name="pwd" placeholder="Password" class="user_input" required/>
            <input type="submit" name="submit_login" class="user_submit" value="Sign in">
            <div class="error" id="err"><?php if(!empty($login_error)) { echo $login_error; };?></div>
            <p class="message">Not registered? <a href="register.php">Create an account</a></p>
        </form>
    </div>
</div>

<?php mysqli_close($conn); ?>
