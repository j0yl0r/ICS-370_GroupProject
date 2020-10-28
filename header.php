<!DOCTYPE html>
<html>
    <head>
        <title>Supply Chain Management Site</title>
        <link rel="stylesheet" href="SCMstyle.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>

    <body>
        <!-- header on the website-->
        <div class="header"></div>
        <?php
        global $user_id;
        if(isset($user_id)){
            $query = "CALL `select_user_role`('".$user_id."');";
            mysqli_multi_query($conn, $query) or die(mysqli_error($conn));
            $result = mysqli_store_result($conn);
            $user_role = mysqli_fetch_row($result)[0];
            mysqli_free_result($result);
            while (mysqli_next_result($conn));

            $navbar_html = "<!-- navigation bar on the website-->
                <div class='navbar'>";
            switch($user_role){
                case "administrator":
                    $navbar_html .= "
                        <a href='index.php'>Home</a>
                        <a href='manageInventories.php'>Manage Inventories</a>
                        <a href='manageOrders.php'>Manage Orders</a>
                        <a href='manageAccounts.php'>Manage Accounts</a>";
                    break;
                case "customer":
                    $navbar_html .= "
                        <a href='index.php'>Home</a>
                        <a href='viewItems.php'>View Items</a>
                        <a href='viewOrders.php'>View Orders</a>
                        <a href='editCustomerInfo.php'>Change Customer Details</a>";
                    break;
                case "transportation_associate":
                    $navbar_html .= "transportation_associate";
                    break;
                default:
                    $navbar_html .= "User role '".$user_role."' not recognized";
            }
            $navbar_html .= "<a href='logout.php' class='right'>
                Log Out 
                " . $_SESSION['username'] .
                "</a>
            </div>";
            echo $navbar_html;
        }
        ?>