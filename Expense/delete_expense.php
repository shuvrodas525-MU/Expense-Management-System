<?php
include("./includes/header.php");
include("./includes/functions.php");
include("./includes/db_conn.php");


if(isset($_REQUEST['del_user_id'])){

$delete_id = $_REQUEST['del_user_id'];

$del_query = "DELETE FROM reg_users WHERE reg_id=$delete_id";
$run_del_query = mysqli_query($conn,$del_query);

if($run_del_query) {
    echo 1;
}else{
    echo 0;
     }
    
    mysqli_close($conn);
}

?>


 <?php
include("./includes/footer.php");
?>