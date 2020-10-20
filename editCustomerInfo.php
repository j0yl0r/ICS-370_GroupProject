<?php
    include("./force_login.php");
    global $user_id;
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
            isset($_POST["card"])){
            $query = "CALL `update_customer_info`(".$user_id.", '".$_POST["uname"]."', 
            '".$_POST["email"]."', '".$_POST["phone"]."', 
            '".$_POST["fname"]."', '".$_POST["lname"]."', 
            '".$_POST["address"]."', '".$_POST["city"]."', 
            '".$_POST["state"]."', '".$_POST["zipcode"]."', 
            '".$_POST["card"]."');";
            mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
            $result = mysqli_store_result($conn);
            $login_successful = mysqli_fetch_row($result)[0];
            mysqli_free_result($result);
            
            header("Location: ./login.php");
        }
        ?>
        <p>
        <h3>Edit Customer Info</h3>
        <form action="editCustomerInfo.php" method="post">
            <?php
            
            $query = "CALL `select_customer_info`(".$user_id.")";
            mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
            $result = mysqli_store_result($conn);
            $row = mysqli_fetch_row($result);
            echo "
            <label for='uname'>Username</label>
            <input type='text' placeholder='Username' id='uname' name='uname' value='".$row[0]."' required>
            <br><br>
            <label for='fname'>First Name</label>
            <input type='text' placeholder='First Name' id='fname' name='fname' value='".$row[1]."' required>
            <br><br>
            <label for='lname'>Last Name</label>
            <input type='text' placeholder='Last Name' id='lname' name='lname' value='".$row[2]."' required>
            <br><br>
            <label for='phone'>Phone Number</label>
            <input type='text' placeholder='Phone' id='phone' name='phone' value='".$row[3]."' required>
            <br><br>
            <label for='email'>Email</label>
            <input type='text' placeholder='E-mail' id='email' name='email' value='".$row[4]."' required>
            <br><br>
            <label for='address'>Address</label>
            <input type='text' placeholder='Address' id='address' name='address' value='".$row[5]."' required>
            <br><br>
            <label for='city'>City</label>
            <input type='text' placeholder='City' id='city' name='city' value='".$row[6]."' required>
            <br><br>
            <label for='state'>State</label>
            <input type='text' placeholder='State' id='state' name='state' value='".$row[7]."' required>
            <br><br>
            <label for='zipcode'>Zip Code</label>
            <input type='text' placeholder='Zip Code' id='zipcode' name='zipcode' value='".$row[8]."' required>
            <br><br>
            <label for='card'>Card Number</label>
            <input type='text' placeholder='Card Number' id='card' name='card' value='".$row[9]."' required>
            <br><br>
            ";
            mysqli_free_result($result);
            while (mysqli_next_result($conn));
            ?>
            <button type="submit">Apply Changes</button>
        </form>
        <br><br><br>
        </p>
    </div>
</div>

<?php
    include("./footer.php");
?>