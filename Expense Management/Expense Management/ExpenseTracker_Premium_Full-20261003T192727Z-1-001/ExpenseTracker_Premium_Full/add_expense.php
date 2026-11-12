<?php

/* =========================================================
   INITIALIZATION
   ========================================================= */

include('./includes/functions.php');

check_user();

include('./includes/db_conn.php');


/* =========================================================
   ADD EXPENSE
   ========================================================= */

if (isset($_POST['save'])) {

    $uid = current_user_id();

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
           Insert expense
           ------------------------------------------------- */

        $stmt = mysqli_prepare(
            $conn,
            'INSERT INTO expense_info
            (
                user_id,
                item_name,
                item_price,
                item_date,
                item_details
            )
            VALUES (?, ?, ?, ?, ?)'
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                'isdss',
                $uid,
                $name,
                $price,
                $date,
                $details
            );


            $result = mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);


            if ($result) {

                redirect_msg(
                    './index.php',
                    'success',
                    'Expense added successfully.'
                );

            } else {

                $error = 'Could not save the expense.';

            }

        } else {

            $error = 'Database error while saving expense.';

        }
    }
}


/* =========================================================
   HEADER
   ========================================================= */

include('./includes/header.php');

?>


<!-- =======================================================
     ADD EXPENSE PAGE
======================================================== -->

<div class="shell">


    <!-- PAGE HEADER -->

    <div class="page-head reveal">

        <div>

            <div class="eyebrow">
                New transaction
            </div>

            <h1>
                Add expense
            </h1>

            <p>
                Record one real expense. It will immediately
                affect your totals and reports.
            </p>

        </div>


        <a
            class="btn btn-ghost"
            href="./all_expenses.php"
        >
            Cancel
        </a>

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


    <!-- EXPENSE FORM -->

    <form
        class="form-panel reveal"
        method="POST"
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
                    placeholder="e.g. Lunch"
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
                    min="0"
                    step="0.01"
                    placeholder="0.00"
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
                    value="<?php echo date('Y-m-d'); ?>"
                    max="<?php echo date('Y-m-d'); ?>"
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
                    placeholder="Optional note"
                >

            </div>


            <!-- SUBMIT -->

            <div
                class="full"
                style="
                    display:flex;
                    justify-content:flex-end;
                "
            >

                <button
                    type="submit"
                    class="btn btn-neon"
                    name="save"
                >

                    <i class="bi bi-check2"></i>

                    Save expense

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