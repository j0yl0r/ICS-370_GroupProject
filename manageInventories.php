<?php
    include("./force_login.php");
    global $user_id;
    include_once("./sqlInit.php");
    global $conn;
    include("./header.php");
?>

<div class="bg">

<?php

if(isset($_POST['update']) && isset($_POST['item_id']) && 
isset($_POST['name']) && isset($_POST['desc']) && 
isset($_POST['price']) && isset($_POST['stock'])){
    // If this page was loaded to update an item
    $query = "CALL `update_available_item`(".$_POST['item_id'].", '".$_POST["name"]."', '".
        $_POST["desc"]."', ".$_POST['price'].", ".$_POST['qty'].");";
    mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
    $result = mysqli_store_result($conn);
    while(mysqli_next_result($conn));
    echo "<h4>Updated Item: ".$_POST['name']."</h4>";

}
else if(isset ($_POST['delete']) && isset($_POST['item_id'])){
    // If this page was loaded to delete an item
    $query = "CALL `delete_available_item`(".$_POST['item_id'].");";
    mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
    $result = mysqli_store_result($conn);
    while(mysqli_next_result($conn));
    echo "<h4>Deleted Item: ".$_POST['name']."</h4>";

}
else if(isset ($_POST['add']) && isset($_POST['name']) &&
isset($_POST['desc']) && isset($_POST['price']) && 
isset($_POST['stock'])){
    // If this page was loaded to add an item
    $query = "CALL `insert_new_available_item`('".$_POST["name"]."', '".
        $_POST["desc"]."', ".$_POST['price'].", ".$_POST['stock'].");";
    mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
    $result = mysqli_store_result($conn);
    while(mysqli_next_result($conn));
    echo "<h4>Added Item: ".$_POST['name']."</h4>";
};
?>


<!-- background on the website-->
    
    <h3>Edit Inventories</h3>
    <table><tr><th>Item Name</th><th>Description</th><th>Price</th><th>Qty</th>
    <?php


        $query = "CALL `select_all_items`()";
        mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
        $result = mysqli_store_result($conn);
        while($row = mysqli_fetch_row($result)){
            $formID = "inventoryID" . $row[0];
            echo "<form id='".$formID."' action='./manageInventories.php' method='post'>
            <input type='hidden' id='item_id' name='item_id' value='".$row[0]."'>
            <tr>
                <td style='width: 150px;'>
                    <input type='text' id='name' name='name' value='".$row[1]."' required>
                </td>
                <td style='width: 250px;'>
                    <textarea id='desc' name='desc' form='".$formID."' required>".$row[2]."</textarea>
                </td>
                <td style='width: 50px;'>
                    <input type='text' id='price' name='price' value='".$row[3]."' required></td>
                <td>
                    <input style='width: 65px;'  min='0' max='999' type='number' name='qty' value='".$row[4]."' required>
                </td>
                <td>
                    <button type='submit' name='update' value=''>Update</button>
                </td>
                <td>
                    <button type='submit' name='delete' value=''>Delete</button>
                </td>
            </tr></form>";
        }
        mysqli_free_result($result);
        while (mysqli_next_result($conn));
    ?>
    </table><br>
    <h3>Add New Item</h3>
    <form id='add_item_form_id' action='manageInventories.php' method='post'> <tr>
        <table>
            <tr>
                <td>Item Name</td>
                <td><input type='text' name='name' required></td>
            </tr>
            <tr>
                <td>Description</td>
                <td><textarea id='desc' name='desc' form='add_item_form_id' required></textarea></td>
            </tr>
            <tr>
                <td>Price</td>
                <td><input type='text' name='price' required></td>
            </tr>
            <tr>
                <td>Qty</td>
                <td><input type='number' name='stock'  min='0' max='999' required></td>
            </tr>
        </table>
        <button type='submit' name='add' value=''>Add Item</button></td>
    </form>
</div>

<?php
    include("./footer.php");
?>