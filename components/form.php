<?php session_start();
require_once "connection.php"; 

if(isset($_POST['submit_login'])) {
    $fname = $_POST['username'];	
    $passwd = hash('sha512', $_POST['pwd']);

    $conn->begin_transaction();

    try {
        $sql = "SELECT user_id, username, password FROM users WHERE username=? AND password=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $fname, $passwd);
        $stmt->execute();
        $stmt->bind_result($user_id, $username, $password);
        $result = $stmt->fetch();
        $stmt->close();

        if(!$result) { 
            $login_error = "Please enter the correct username or password.";
        } else {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['password'] = $_POST['pwd'];

            $sql = "UPDATE users SET status = TRUE WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $fname);
            $stmt->execute();
            $stmt->close();

            header("location: home.php");
        }

        $conn->commit();
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }	
}

if(isset($_POST['submit_signup'])) {
    $fname = $_POST['username'];	
    $email = $_POST['email'];	
    $passwd = hash('SHA512', $_POST['pwd']);

    $conn->begin_transaction();

    try {
        $result1 = "SELECT COUNT(*) FROM users WHERE email=?";
        $stmt1 = $conn->prepare($result1);
        $stmt1->bind_param('s', $email);
        $stmt1->execute();
        $stmt1->bind_result($count1);
        $stmt1->fetch();
        $stmt1->close();
        
        $result2 = "SELECT COUNT(*) FROM users WHERE username=?";
        $stmt2 = $conn->prepare($result2);
        $stmt2->bind_param('s', $fname);
        $stmt2->execute();
        $stmt2->bind_result($count2);
        $stmt2->fetch();
        $stmt2->close();

        if(preg_match("/[\'^£$%&*()}{!@#~?><>,|=+¬-]/", $fname)) {
            $fname_error = "Username must contain only alphabets, dot, underdash, and space.";
        } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Please enter a valid email.";
        } else if(strlen($_POST['pwd']) < 6) {
            $passwd_error = "Password must be minimum of 6 characters.";
        } else {      
            if($count1 > 0) {
                $email_login_error = "Email already in use. Please try with a different one.";
            } else if ($count2 > 0) {
                $username_login_error = "Username already in use. Please try with a different one.";
            } else { 
                $sql = "INSERT INTO users(username, email, password) VALUES (?,?,?)";
                $stmti = $conn->prepare($sql);
                $stmti->bind_param('sss', $fname, $email, $passwd);
                $stmti->execute();
                $stmti->close();

                header("location: welcome.php");
            }
        }

        $conn->commit();
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }	
}

mysqli_close($conn); ?>