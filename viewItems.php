<?php
    include("./force_login.php");
    include_once("./sqlInit.php");
    global $conn;
    include("./header.php");
?>
<!-- background on the website-->
<div class="bg">
    <p>View Items</p>
    <?php 
        $query = "CALL `select_all_items`()";
        mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
        $result = mysqli_store_result($conn);
        $order_count = mysqli_num_rows($result);
        $order_ids = [];
        while($row = mysqli_fetch_row($result)){
            $order_ids[] = $row[0];
        }
        mysqli_free_result($result);
        while (mysqli_next_result($conn));

        echo "<table><tr><th>Item Name</th><th>Item Quantity</th><th>Item Price</th><th>Actions</th></tr>";

        $query = "CALL `select_order_info`(".$order_id.");";
        mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
        $result = mysqli_store_result($conn);
        while($row = mysqli_fetch_row($result)){
            echo "<tr>";
            echo "<td style='width: 250px;'>".$row[0]."</td>";
            echo "<td style='width: 150px;'>".$row[1]."</td>";
            echo "<td style='width: 100px;'>".$row[2]."</td>";
            echo "<td style='width: 200px;'>Actions TODO</td>";
            echo "</tr>";
        }
        mysqli_free_result($result);
        while (mysqli_next_result($conn));

        echo "</table></br></br>";
        
    ?>
    <br><br><br><br>
</div>
<?php
    include("./footer.php");
?>