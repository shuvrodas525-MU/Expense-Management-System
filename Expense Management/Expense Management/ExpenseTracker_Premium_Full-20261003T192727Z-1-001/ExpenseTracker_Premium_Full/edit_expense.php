<?php

/* =========================================================
   INITIALIZATION
   ========================================================= */

include('./includes/functions.php');

check_user();

include('./includes/db_conn.php');


/* =========================================================
   CURRENT USER & EXPENSE ID
   ========================================================= */

$user_id = current_user_id();

$expense_id = (int) (
    $_GET['edit_expense_id']
    ?? $_POST['item_id']
    ?? 0
);


/* =========================================================
   FETCH EXPENSE
   ========================================================= */

$stmt = mysqli_prepare(
    $conn,
    'SELECT
        item_name,
        item_price,
        item_date,
        item_details
     FROM expense_info
     WHERE item_id = ?
     AND user_id = ?'
);


if (!$stmt) {

    mysqli_close($conn);

    redirect_msg(
        './all_expenses.php',
        'danger',
        'Unable to load expense.'
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


$result = mysqli_stmt_get_result($stmt);

$expense = mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


/* =========================================================
   EXPENSE NOT FOUND
   ========================================================= */

if (!$expense) {

    mysqli_close($conn);

    redirect_msg(
        './all_expenses.php',
        'danger',
        'Expense not found.'
    );

    exit;
}


/* =========================================================
   UPDATE EXPENSE
   ========================================================= */

if (isset($_POST['update'])) {

    $name = trim(
        $_POST['item_name'] ?? ''
    );

    $price = (float) (
        $_POST['item_price'] ?? 0
    );

    $date = $_POST['item_date']
        ?? date('Y-m-d');

    $details = trim(
        $_POST['item_details'] ?? ''
    );


    /* -----------------------------------------------------
       Validation
       ----------------------------------------------------- */

    if (
        $name === '' ||
        $price < 0 ||
        !preg_match(
            '/^\d{4}-\d{2}-\d{2}$/',
            $date
        )
    ) {

        $error = 'Please enter valid expense information.';

    } else {

        /* -------------------------------------------------
           Update database
           ------------------------------------------------- */

        $stmt = mysqli_prepare(
            $conn,
            'UPDATE expense_info
             SET
                item_name = ?,
                item_price = ?,
                item_date = ?,
                item_details = ?
             WHERE item_id = ?
             AND user_id = ?'
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                'sdssii',
                $name,
                $price,
                $date,
                $details,
                $expense_id,
                $user_id
            );


            $updated = mysqli_stmt_execute($stmt);


            mysqli_stmt_close($stmt);


            if ($updated) {

                mysqli_close($conn);

                redirect_msg(
                    './all_expenses.php',
                    'success',
                    'Expense updated successfully.'
                );

                exit;

            } else {

                $error = 'Could not update the expense.';

            }

        } else {

            $error = 'Database error while updating expense.';

        }
    }
}


/* =========================================================
   HEADER
   ========================================================= */

include('./includes/header.php');

?>


<!-- =======================================================
     UPDATE EXPENSE PAGE
======================================================== -->

<div class="shell">


    <!-- PAGE HEADER -->

    <div class="page-head reveal">

        <div>

            <div class="eyebrow">
                Edit transaction
            </div>

            <h1>
                Update expense
            </h1>

            <p>
                Make a correction to this saved transaction.
            </p>

        </div>

    </div>


    <!-- ERROR MESSAGE -->

    <?php if (isset($error)): ?>

        <div
            class="toast-alert danger"
            style="position:static;margin-bottom:16px;"
        >

            <i class="bi bi-exclamation-circle"></i>

            <?php echo e($error); ?>

        </div>

    <?php endif; ?>


    <!-- ===================================================
         UPDATE FORM
    ==================================================== -->

    <form
        class="form-panel reveal"
        method="POST"
    >


        <!-- Expense ID -->

        <input
            type="hidden"
            name="item_id"
            value="<?php echo (int) $expense_id; ?>"
        >


        <div class="form-grid">


            <!-- EXPENSE NAME -->

            <div class="field">

                <label for="item_name">
                    Expense name
                </label>

                <input
                    id="item_name"
                    name="item_name"
                    type="text"
                    value="<?php echo e($expense['item_name']); ?>"
                    required
                >

            </div>


            <!-- AMOUNT -->

            <div class="field">

                <label for="item_price">
                    Amount
                </label>

                <input
                    id="item_price"
                    name="item_price"
                    type="number"
                    step="0.01"
                    min="0"
                    value="<?php echo e($expense['item_price']); ?>"
                    required
                >

            </div>


            <!-- DATE -->

            <div class="field">

                <label for="item_date">
                    Date
                </label>

                <input
                    id="item_date"
                    name="item_date"
                    type="date"
                    value="<?php echo e($expense['item_date']); ?>"
                    required
                >

            </div>


            <!-- DETAILS -->

            <div class="field">

                <label for="item_details">
                    Details
                </label>

                <input
                    id="item_details"
                    name="item_details"
                    type="text"
                    value="<?php echo e($expense['item_details']); ?>"
                    placeholder="Optional note"
                >

            </div>


            <!-- ACTIONS -->

            <div
                class="full"
                style="
                    display:flex;
                    gap:10px;
                    justify-content:flex-end;
                "
            >

                <a
                    class="btn btn-ghost"
                    href="./all_expenses.php"
                >

                    Cancel

                </a>


                <button
                    type="submit"
                    class="btn btn-neon"
                    name="update"
                >

                    <i class="bi bi-check2"></i>

                    Save changes

                </button>

            </div>


        </div>

    </form>


</div>


<?php

/* =========================================================
   FOOTER
   ========================================================= */

mysqli_close($conn);

include('./includes/footer.php');

?>