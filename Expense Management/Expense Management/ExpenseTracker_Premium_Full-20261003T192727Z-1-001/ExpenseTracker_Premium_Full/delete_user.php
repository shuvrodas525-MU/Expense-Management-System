<?php

/* =========================================================
   INITIALIZATION
   ========================================================= */

include('./includes/functions.php');

check_admin();

include('./includes/db_conn.php');


/* =========================================================
   GET USER ID
   ========================================================= */

$user_id = (int) (
    $_POST['del_user_id'] ?? 0
);


/* =========================================================
   PREVENT SELF DELETE
   ========================================================= */

if ($user_id === current_user_id()) {

    mysqli_close($conn);

    redirect_msg(
        './display_reg_users.php',
        'warning',
        'You cannot delete your own account.'
    );

    exit;
}


/* =========================================================
   DELETE USER
   ========================================================= */

$stmt = mysqli_prepare(
    $conn,
    'DELETE FROM reg_users
     WHERE reg_id = ?'
);


if (!$stmt) {

    mysqli_close($conn);

    redirect_msg(
        './display_reg_users.php',
        'danger',
        'Unable to delete user.'
    );

    exit;
}


mysqli_stmt_bind_param(
    $stmt,
    'i',
    $user_id
);


mysqli_stmt_execute($stmt);


/* Check whether user was deleted */

$deleted = mysqli_stmt_affected_rows($stmt) > 0;


mysqli_stmt_close($stmt);

mysqli_close($conn);


/* =========================================================
   REDIRECT
   ========================================================= */

if ($deleted) {

    redirect_msg(
        './display_reg_users.php',
        'success',
        'User deleted successfully.'
    );

} else {

    redirect_msg(
        './display_reg_users.php',
        'warning',
        'User not found.'
    );

}

?>
