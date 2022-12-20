<?php @include "header.php"; ?>
<?php
    if(isset($_POST['submit_new_username'])) {
        $new_username = $_POST['new_username'];

        $conn->begin_transaction();

        try {
            if($_POST['new_username']==="") {
                $edit_error = "Cannot submit a blank username. Submit a valid username.";
            } else if(preg_match("/[\'^£$%&*()}{!@#~?><>,|=+¬-]/", $new_username)) {
                $edit_error = "Username must contain only alphabets, dot, underdash, and space.";
            } else if($new_username === $_SESSION['username']) {
                $edit_error = "The new and original usernames are the same.";
            } else {
                $sql = "SELECT username FROM users WHERE username=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $new_username);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if($result->num_rows > 0) {
                    $edit_error = "Username already associated with another account."; 
                } else {
                    $sql = "UPDATE users SET username=? WHERE username=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ss', $new_username, $_SESSION['username']);
                    $stmt->execute();
                    $stmt->close();
                    $_SESSION['username'] = $new_username;
                }
            }
            $conn->commit();
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }	
    }

    if(isset($_POST['submit_new_password'])) {
        $password = hash('sha512', $_POST['new_password']);
        $currentPassword = hash('sha512', $_SESSION['password']);

        $conn->begin_transaction();

        try {
            if($_POST['new_password']==="" || $_POST['confirm_new_password']==="") {
                $edit_error = "Cannot submit a blank password. Submit a valid password.";
            } else if($_POST['new_password'] !== $_POST['confirm_new_password']) {
                $edit_error = "The passwords don't match. Please try again.";
            } else if($_POST['new_password'] === $_SESSION['password']) { 
                $edit_error = "The new and original passwords are the same.";
            } else if(strlen($_POST['new_password']) < 6) {
                $edit_error = "Password must be minimum of 6 characters.";
            } else {
                $sql = "UPDATE users SET password=? WHERE username=? AND password=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sss', $password, $_SESSION['username'], $currentPassword);
                $stmt->execute();
                $stmt->close();
                $_SESSION['password'] = $_POST['new_password'];
            }

            $conn->commit();
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }	
    }

    if(isset($_GET['del_my_acc'])) {
        $pwd = hash('sha512', $_SESSION['password']);
        $conn->begin_transaction();

        try {
            $sql = "DELETE FROM users WHERE username=? AND password=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $_SESSION['username'], $pwd);
            $stmt->execute();
            $stmt->close();

            unset($_SESSION['username']);
            unset($_SESSION['user_id']);
            unset($_SESSION['password']);
            session_destroy();

            header("location: home.php#home-mov");
            $conn->commit();
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }	
    }
?>

<div class="user-box">
    <div class="mypage flex">
        <p class="user_title mypage_title">My Account</p>
        <form method='POST' action=''>
            <div class="mypage_opt flex">
                <div class="mypage_half flex">
                    <div class="mypage_display_info flex">
                        <p class="mypage_label">Username:</p>
                        <?php if(isset($_POST['change_username'])) {?>
                        <input type="text" name="new_username" value="<?=$_SESSION['username']?>" class="user_input"/>
                    </div>
                    <input type="submit" name="submit_new_username" class="mypage_submit_btns" value="Submit">
                    <input type="submit" name="cancel_username" class="mypage_cancel_btns" value="×">
                    <?php } else { ?>
                        <p class="mypage_label">@<?=$_SESSION['username']?></p>
                    </div>
                    <input type="submit" name="change_username" class="mypage_submit_btns" value="Change">
                    <?php } ?> 
                </div> 
                <div class="mypage_half flex">
                    <div class="mypage_display_info flex">
                        <p class="mypage_label">Password:</p>
                        <?php if(isset($_POST['edit_password'])) {?>
                        <div class="password-input-bars flex">
                            <input type="password" name="new_password" placeholder="Enter new password..." class="user_input"/>
                            <input type="password" name="confirm_new_password" placeholder="Confirm new password..." class="user_input"/>
                        </div>
                    </div>
                    <input type="submit" name="submit_new_password" class="mypage_submit_btns" value="Submit">
                    <input type="submit" name="cancel_password" class="mypage_cancel_btns" value="×">
                    <?php } else { ?>
                        <p class="mypage_label" id="eye-pwd">
                            <span id="pwd-bullet" class="mypage_label"><?php for($i=1; $i<=strlen($_SESSION['password']); $i++) { echo "●"; }?></span>
                            <span id="pwd-show" class="mypage_label hidden"><?=$_SESSION['password']?></span>
                        </p>
                        <button onmouseover="displayPwd()" onmouseout="hidePwd()"><i id="icon" class="fa fa-eye-slash" style="font-size: 1.1rem; margin-left: -1rem;"></i></button>
                    </div>
                    <input type="submit" name="edit_password" class="mypage_submit_btns" value="Change">
                </div>
                <?php } ?> 
            </div>  
        </form>
        <a href="#modal-one" class="mypage_cancel_btns mypage_delacc_btns">Delete My Account</a>
        <div class="error" id="err"><?php if(!empty($edit_error)) { echo $edit_error; }?></div>
    </div>
</div>
<div id="modal-one" class="modal">
    <div class="modal-dialog">
        <div class="modal-header flex">
            <h2>Are you sure you want to delete your account?</h2>
            <a href="#" class="btn-close">×</a>
        </div>
        <form method="GET" action="">
            <div class="modal-body flex">
                <input type="submit" name="del_my_acc" class="modal-footer_btn delete" value="Yes"/>
                <a href="#" class="modal-footer_btn">No</a>
            </div>
        </form>
        <div class="modal-footer"></div>
    </div>
</div>

<?php 
mysqli_free_result($result);
mysqli_close($conn); 
?>