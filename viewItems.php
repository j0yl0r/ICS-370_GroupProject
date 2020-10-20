<?php
    include("./force_login.php");
    global $user_id;
    include_once("./sqlInit.php");
    global $conn;
    include("./header.php");
?>

<!-- background on the website-->
<div class="bg">
    
    <?php
        if(isset($_POST["item_id"]) && isset($_POST["qty"]) && $_POST["qty"]){
            $query = "CALL `relate_item_and_order`(".$user_id.", ".$_POST["item_id"].", ".$_POST["qty"].");";
            mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
            $result = mysqli_store_result($conn);
            while (mysqli_next_result($conn));
            echo "<h4>Added item to order</h4>";
        }
    ?>

    <p>View Items</p>
    
    <table><tr><th>Item Name</th><th>Description</th><th>Price</th><th>Order Qty</th><th>Actions</th></tr>
    <?php

        $query = "CALL `select_all_items`()";
        mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
        $result = mysqli_store_result($conn);
        while($row = mysqli_fetch_row($result)){
            echo "<tr>";
            echo "<td style='width: 200px;'>".$row[1]."</td>";
            echo "<td style='width: 250px;'>".$row[2]."</td>";
            echo "<td style='width: 75px;'>".$row[3]."</td>";
            echo "<form action='' method='post'>
                <td style='width: 50px;'>
                    <input type='number' name='qty' min='0' max='99'>
                </td>";
            echo "<td style='width: 120px;'>
                    <button type='submit' name='item_id' value=".$row[0].">Add To Order</button>
                </td>
            </form>";
            echo "</tr>";
        }
        mysqli_free_result($result);
        while (mysqli_next_result($conn));
        
    ?>
    </table>
    <br><br><br><br>
</div>
<?php
    include("./footer.php");
?>