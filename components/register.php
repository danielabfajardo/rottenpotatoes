<?php @include "header.php"; ?>
<?php @include "form.php"; ?>

<div class="user-box">
    <div class="user">
        <form method='POST' action='' class="user_register flex" id="signup">
            <p class="user_title">Sign Up</p>
            <input type="text" name="username" placeholder="Username" class="user_input" required/>
            <input type="text" name="email" placeholder="Email" class="user_input" required/>
            <input type="password" name="pwd" placeholder="Password" class="user_input" required/>
            <input type="submit" name="submit_signup" class="user_submit" value="Create">
            <div class="error"><?php if(!empty($fname_error)) { echo $fname_error; }if (!empty($email_error)) { echo $email_error; } if(!empty($passwd_error)) { echo $passwd_error; } if(!empty($email_login_error)) { echo $email_login_error; } if (!empty($username_login_error)) { echo $username_login_error; } ?></div>
            <p class="message">Already registered? <a href="login.php">Sign In</a></p>
        </form>
    </div>
</div>

<?php mysqli_close($conn); ?>