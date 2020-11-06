<?php
    include("./force_login.php");
    global $user_id;
    include_once("./sqlInit.php");
    global $conn;
    include("./header.php");
?>


<!-- background on the website-->
<div class="bg">
    <center>
    <?php

        if(isset($_POST['update']) && isset($_POST['userid']) && 
           isset($_POST['fname']) && isset($_POST['lname']) && 
           isset($_POST['email']) && isset($_POST['phone']) && 
           isset($_POST['address']) && isset($_POST['city']) && 
           isset($_POST['state']) && isset($_POST['zipcode']) && 
           isset($_POST['card'])&& isset($_POST['typeAcc'])){

            // If this page was loaded to update an customer info
            $query = "UPDATE users SET id= '$_POST[userid]', role ='$_POST[typeAcc]', first_name ='$_POST[fname]', last_name ='$_POST[lname]', email ='$_POST[email]', phone_number ='$_POST[phone]'
                      WHERE id ='$_POST[hidden]'";
            $query2= "UPDATE customer_info SET street_address ='$_POST[address]', city ='$_POST[city]', state ='$_POST[state]', zip_code ='$_POST[zipcode]', card_number ='$_POST[card]'
                      WHERE customer_id ='$_POST[hidden]'";
            
            mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
            mysqli_multi_query($conn, $query2) or die(mysqli_error($conn));
            $result = mysqli_store_result($conn);
            while(mysqli_next_result($conn));
            echo "<h4>Updated Customer ID ".$_POST['userid']. ": ".$_POST['fname']. " " .$_POST['lname']. "</h4>";


        }else if(isset($_POST['update']) && isset($_POST['userid']) && 
                 isset($_POST['fname']) && isset($_POST['lname']) && 
                 isset($_POST['email'])){

            // If this page was loaded to update an admin/ driver info
            $query = "UPDATE users SET id= '$_POST[userid]', role ='$_POST[typeAcc]', first_name ='$_POST[fname]', last_name ='$_POST[lname]', email ='$_POST[email]'
                      WHERE id ='$_POST[hidden]'";

            mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
            $result = mysqli_store_result($conn);
            while(mysqli_next_result($conn));
            echo "<h4>Updated Account ID ".$_POST['userid']. ": ".$_POST['fname']. " " .$_POST['lname']. "</h4>";


        }else if(isset ($_POST['delete']) && isset($_POST['hidden'])){

            // If this page was loaded to delete account
            $query = "DELETE FROM users
                      WHERE id='$_POST[hidden]'";
            mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
            $result = mysqli_store_result($conn);
            while(mysqli_next_result($conn));
            echo "<h4>Deleted Account ID ".$_POST['userid']. ": ".$_POST['fname']. " " . $_POST['lname' ]. "</h4>";
        } 

    ?>

    <p>

    <h3>Manage Accounts</h3>
    <button type="submit"><a href="registerNewDriver.php">Register New Driver</a></button>
    &nbsp;
    <button type="submit"><a href="createAdminAccount.php">Register New Administrator</a></button>
    <hr><br>
    </p>

    <!-- Be able to view and edit all accounts, create new admin and transportation associate accounts -->
    <?php
        $query = "SELECT  u.*, c.*
                FROM users u JOIN customer_info c 
                WHERE u.id = c.customer_id
                ORDER BY u.id DESC";

        mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
        $result = mysqli_store_result($conn);
        while($row = mysqli_fetch_row($result)){

            $formID = "userID" . $row[0];
            echo "<table>
                <form id='".$formID."' action='./manageAccount.php' method='post'>
                <input type='hidden' id='hidden' name='hidden' value='".$row[0]. "' >
                <tr>
                    <td style='width: 150px;'><b>Account ID:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='userid' name='userid' value= '".$row[0]. "'  required> 
                    </td>
                </tr>
                <tr>
                    <td><b>Account Type:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='typeAcc' name='typeAcc' value= '".$row[5]. "' required>
                    </td>
                </tr>   
                <tr>
                    <td><b>First Name:</b></td>
                    <td> 
                        <input style='width: 300px;' type='text' id='fname' name='fname' value= '".$row[6]. "'  required>
                    </td>
                </tr>
                <tr>
                    <td><b>Last Name:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='lname' name='lname' value= '".$row[7]. "'  required>
                    </td>
                </tr>
                <tr>
                    <td><b>Email:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='email' name='email' value= '".$row[3]. "'  required>
                    </td>
                </tr>
                <tr>
                    <td><b>Phone:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='phone' name='phone' value= '".$row[4]. "' >
                    </td>
                </tr>                    
                <tr>
                    <td><b>Address:</b></td>
                    <td >
                        <input style='width: 300px;' type='text' id='address' name='address' value= '" .$row[10]. "' required>
                    </td>
                </tr>
                <tr>
                    <td><b>City:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='city' name='city' value= '" .$row[11]."' required>
                    </td>
                </tr>
                <tr>
                    <td><b>State:</b></td>
                    <td >
                        <input style='width: 300px;' type='text' id='state' name='state' value= '" .$row[12]. "' required>
                    </td>  
                </tr>
                <tr>
                    <td><b>Zip Code:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='zipcode' name='zipcode' value= '" .$row[13]. "' required>
                    </td>
                </tr>
                <tr>
                    <td><b>Card Payment:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='card' name='card' value= '".$row[14]. "' required>
                    </td>
                </tr>             
                <tr>
                    <td></td>
                    <td>
                        <button type='submit' name='update' value=''>Update</button>
                        <button type='submit' name='delete' value=''>Delete</button>
                    </td>
                </tr>
                </table>
                </form>
                <br><br>";
        }
        echo "<hr><br><br>";

        
        $query2 = "SELECT  * FROM users
                   WHERE role = 'administrator' OR role = 'transportation_associate'
                   ORDER BY id DESC";

        mysqli_multi_query($conn, $query2) or die(mysqli_error($conn));
        $result = mysqli_store_result($conn);
        while($row = mysqli_fetch_row($result)){
            $formID = "userID" . $row[0];
            echo "<table>
                <form id='".$formID."' action='./manageAccount.php' method='post'>
                <input type='hidden' id='hidden' name='hidden' value='".$row[0]. "' >
                <tr>
                    <td style='width: 150px;'><b>Account ID:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='userid' name='userid' value= '".$row[0]. "'  required> 
                    </td>
                </tr>
                <tr>
                    <td><b>Account Type:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='typeAcc' name='typeAcc' value= '".$row[5]. "' required>
                    </td>
                </tr>   
                <tr>
                    <td><b>First Name:</b></td>
                    <td> 
                        <input style='width: 300px;' type='text' id='fname' name='fname' value= '".$row[6]. "'  required>
                    </td>
                </tr>
                <tr>
                    <td><b>Last Name:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='lname' name='lname' value= '".$row[7]. "'  required>
                    </td>
                </tr>
                <tr>
                    <td><b>Email:</b></td>
                    <td>
                        <input style='width: 300px;' type='text' id='email' name='email' value= '".$row[3]. "'  required>
                    </td>
                </tr> 
            <tr>
                <td></td>
                <td>
                    <button type='submit' name='update' value=''>Update</button>
                    <button type='submit' name='delete' value=''>Delete</button>
                </td>
            </tr>
            </table>
            </form>
            <br><br>";
        }
        echo "<hr>";

        mysqli_free_result($result);
        while (mysqli_next_result($conn));
    ?>
    </center>
</div>
          
<?php
    include("./footer.php");
?>
