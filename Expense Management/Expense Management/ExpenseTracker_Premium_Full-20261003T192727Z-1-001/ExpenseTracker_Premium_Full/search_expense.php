<?php

include('./includes/functions.php');
check_user();

include('./includes/db_conn.php');

$uid = current_user_id();


/* =========================
   Search Dates
   ========================= */

$from = $_GET['from_date'] ?? date('Y-m-01');
$to   = $_GET['to_date'] ?? date('Y-m-d');

$rows  = null;
$error = null;


/* =========================
   Search Expenses
   ========================= */

if (isset($_GET['search'])) {

    /* Validate Date Range */

    if ($from > $to) {

        $error = 'From date cannot be after To date.';

    } else {

        $s = mysqli_prepare(
            $conn,
            'SELECT
                item_id,
                item_name,
                item_price,
                item_date,
                item_details
             FROM expense_info
             WHERE user_id=?
             AND item_date BETWEEN ? AND ?
             ORDER BY item_date DESC, item_id DESC'
        );

        mysqli_stmt_bind_param(
            $s,
            'iss',
            $uid,
            $from,
            $to
        );

        mysqli_stmt_execute($s);

        $rows = mysqli_stmt_get_result($s);
    }
}


include('./includes/header.php');

?>


<div class="shell">


    <!-- =========================
         Page Header
         ========================= -->

    <div class="page-head reveal">

        <div>

            <div class="eyebrow">
                Find a range
            </div>

            <h1>
                Search expenses
            </h1>

            <p>
                Search only your own saved expense records.
            </p>

        </div>

    </div>


    <!-- =========================
         Search Form
         ========================= -->

    <form
        class="form-panel filter-bar reveal"
        method="GET"
    >

        <!-- From Date -->

        <div class="field">

            <label>
                From
            </label>

            <input
                type="date"
                name="from_date"
                value="<?php echo e($from); ?>"
                max="<?php echo date('Y-m-d'); ?>"
            >

        </div>


        <!-- To Date -->

        <div class="field">

            <label>
                To
            </label>

            <input
                type="date"
                name="to_date"
                value="<?php echo e($to); ?>"
                max="<?php echo date('Y-m-d'); ?>"
            >

        </div>


        <!-- Search Button -->

        <button
            class="btn btn-neon"
            name="search"
        >
            Search
        </button>

    </form>


    <!-- =========================
         Error Message
         ========================= -->

    <?php if ($error): ?>

        <div
            class="toast-alert danger"
            style="position:static;margin-bottom:16px;"
        >

            <i class="bi bi-exclamation-circle"></i>

            <?php echo e($error); ?>

        </div>

    <?php endif; ?>


    <!-- =========================
         Search Results
         ========================= -->

    <?php if ($rows): ?>

        <div class="table-wrap reveal">


            <div class="panel-head">

                <div class="panel-title">
                    Results
                </div>

            </div>


            <div class="table-scroll">

                <table class="app-table">


                    <!-- Table Header -->

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>Expense</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Actions</th>
                        </tr>

                    </thead>


                    <!-- Table Body -->

                    <tbody>

                        <?php

                        $i     = 1;
                        $total = 0;

                        while ($r = mysqli_fetch_assoc($rows)) {

                            $total += (float) $r['item_price'];

                        ?>

                            <tr>


                                <!-- Number -->

                                <td>
                                    <?php echo $i++; ?>
                                </td>


                                <!-- Expense -->

                                <td>
                                    <?php echo e($r['item_name']); ?>
                                </td>


                                <!-- Amount -->

                                <td class="amount-cell">
                                    <?php echo money($r['item_price']); ?>
                                </td>


                                <!-- Date -->

                                <td>
                                    <?php echo nice_date($r['item_date']); ?>
                                </td>


                                <!-- Details -->

                                <td>
                                    <?php echo e($r['item_details']); ?>
                                </td>


                                <!-- Actions -->

                                <td>

                                    <div class="ops">


                                        <!-- Edit -->

                                        <a
                                            class="mini-btn"
                                            href="./edit_expense.php?edit_expense_id=<?php echo $r['item_id']; ?>"
                                        >
                                            Edit
                                        </a>


                                        <!-- Delete -->

                                        <form
                                            class="delete-form"
                                            method="POST"
                                            action="./delete_expense.php"
                                        >

                                            <input
                                                type="hidden"
                                                name="del_expense_id"
                                                value="<?php echo $r['item_id']; ?>"
                                            >

                                            <button
                                                class="mini-btn danger"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php } ?>


                        <!-- Total -->

                        <tr>

                            <td colspan="4"></td>

                            <th>
                                Total
                            </th>

                            <th>
                                <?php echo money($total); ?>
                            </th>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    <?php endif; ?>


</div>


<?php

if (isset($s)) {
    mysqli_stmt_close($s);
}

mysqli_close($conn);

include('./includes/footer.php');

?>