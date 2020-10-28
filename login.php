<?php
    include_once("./sqlInit.php");
    global $conn;
    include("./header.php");
?>

<!-- background on the website-->
<div class="bg">
    <!-- border layout-->
    <div class="round">
        <?php
        // Used on login
        if(isset($_POST["uname"]) && isset($_POST["psw"])){
            $query = "CALL `attempt_login`('".$_POST["uname"]."', '".$_POST["psw"]."');";
            mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
            $result = mysqli_store_result($conn);
            $login_successful = mysqli_fetch_row($result)[0];
            mysqli_free_result($result);
            while (mysqli_next_result($conn));
            if(!$login_successful){
                echo "<h3>Failed to log in user: ".$_POST["uname"]."</h3>";
            } else {
                session_start();
                $_SESSION["username"] = $_POST["uname"];
                $_SESSION["password"] = $_POST["psw"];
                header("Location: ./index.php");
            }
        }
        ?>
        <form action="./login.php" method="post">
            <h3>Log In</h3>
            <label for="uname"><b>Username</b></label>
                <input type="text" placeholder="Enter Username" id="uname" name="uname" required>
            <br><br>
            <label for="psw"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" id="psw" name="psw" required>
            <br><br>
            <!--<span class="psw">Forgot <a href="forgotPassword.html">password?</a></span>
            <br><br>-->
            <button type="submit" id="submit_button">Login</button>
        </form>
        <br><br><br>
        <hr><br>
        New Customer? <button type="submit"><a href="createAccount.php">Create Account</a></button>
    </div>
</div>

<?php
    include("./footer.php");
?>