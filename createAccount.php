<?php
    include_once("./sqlInit.php");
    global $conn;
    include("./header.php");
?>

<!-- background on the website-->
<div class="bg">
    <!-- border layout-->
    <div class="round2">
        <?php
        // Used on account creation
        if(isset($_POST["uname"]) && 
            isset($_POST["fname"]) && isset($_POST["lname"]) &&
            isset($_POST["address"]) && isset($_POST["city"]) &&
            isset($_POST["state"]) && isset($_POST["zipcode"]) &&
            isset($_POST["phone"]) && isset($_POST["email"]) &&
            isset($_POST["psw"]) && isset($_POST["card"])){
            $query = "CALL `insert_new_customer`('".$_POST["uname"]."', '".$_POST["psw"]."', 
            '".$_POST["email"]."', '".$_POST["phone"]."', 
            '".$_POST["fname"]."', '".$_POST["lname"]."', 
            '".$_POST["address"]."', '".$_POST["city"]."', 
            '".$_POST["state"]."', '".$_POST["zipcode"]."', '".$_POST["card"]."');";
            mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
            $result = mysqli_store_result($conn);
            $login_successful = mysqli_fetch_row($result)[0];
            mysqli_free_result($result);
            header("Location: ./login.php");
        }
        ?>
        <p><h3>Create An Account</h3>
        <form action="createAccount.php" method="post">
            <input type="text" placeholder="Username" id="uname" name="uname" required>
            <br><br>
            <input type="text" placeholder="First Name" id="fname" name="fname" required>
            <input type="text" placeholder="Last Name" id="lname" name="lname" required>
            <br><br>
            <input type="text" placeholder="Phone" id="phone" name="phone" required>
            <input type="text" placeholder="E-mail" id="email" name="email" required>
            <br><br>
            <input type="text" placeholder="Address" id="address" name="address" required>
            <input type="text" placeholder="City" id="city" name="city" required>
            <br><br>
            <input type="text" placeholder="State" id="state" name="state" required>
            <input type="text" placeholder="Zip Code" id="zipcode" name="zipcode" required>
            <br><br>
            <input type="password" placeholder="Password" id="psw" name="psw" required>
            <input type="password" placeholder="Re-Enter Password" id="psw" name="psw" required>
            <br><br>
            <input type="text" placeholder="Card Number" id="card" name="card" required>
            <br><br>
            <button type="submit">Create Now</button>
        </form>
        <br><br><br>
        <hr><br><br>
        Already a member? <button type="submit"><a href="login.php">Log In</a></button>
        </p>
    </div>
</div>

<?php
    include("./footer.php");
?>