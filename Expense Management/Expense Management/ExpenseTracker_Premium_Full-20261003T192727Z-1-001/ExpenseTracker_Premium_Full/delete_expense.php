<?php

/* =========================================================
   INITIALIZATION
   ========================================================= */

include('./includes/functions.php');

check_user();

include('./includes/db_conn.php');


/* =========================================================
   GET EXPENSE ID
   ========================================================= */

$expense_id = (int) (
    $_POST['del_expense_id'] ?? 0
);

$user_id = current_user_id();


/* =========================================================
   DELETE EXPENSE
   ========================================================= */

$stmt = mysqli_prepare(
    $conn,
    'DELETE FROM expense_info
     WHERE item_id = ?
     AND user_id = ?'
);


if (!$stmt) {

    mysqli_close($conn);

    redirect_msg(
        './all_expenses.php',
        'danger',
        'Unable to delete expense.'
    );

    exit;
}


mysqli_stmt_bind_param(
    $stmt,
    'ii',
    $expense_id,
    $user_id
);


mysqli_stmt_execute($stmt);


/* Check whether anything was deleted */

$deleted = mysqli_stmt_affected_rows($stmt) > 0;


mysqli_stmt_close($stmt);

mysqli_close($conn);


/* =========================================================
   REDIRECT
   ========================================================= */

if ($deleted) {

    redirect_msg(
        './all_expenses.php',
        'success',
        'Expense deleted successfully.'
    );

} else {

    redirect_msg(
        './all_expenses.php',
        'warning',
        'Expense not found.'
    );

}

?>
