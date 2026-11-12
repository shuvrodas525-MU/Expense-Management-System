<?php

include('./includes/functions.php');
check_user();

include('./includes/db_conn.php');

$uid = current_user_id();


/* =========================
   Date Range
   ========================= */

$from  = date('Y-m-d');
$to    = date('Y-m-d');
$title = 'Today';


/* =========================
   Fetch Expenses
   ========================= */

$stmt = mysqli_prepare(
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
    $stmt,
    'iss',
    $uid,
    $from,
    $to
);

mysqli_stmt_execute($stmt);

$rows = mysqli_stmt_get_result($stmt);


include('./includes/header.php');

?>


<div class="shell">


    <!-- =========================
         Page Header
         ========================= -->

    <div class="page-head reveal">

        <div>

            <div class="eyebrow">
                Expense history
            </div>

            <h1>
                <?php echo $title; ?>
            </h1>

            <p>
                Only your saved transactions are shown here.
            </p>

        </div>


        <!-- Add Expense -->

        <a
            class="btn btn-neon"
            href="./add_expense.php"
        >
            <i class="bi bi-plus-lg"></i>
            Add Expense
        </a>

    </div>


    <!-- =========================
         Transactions
         ========================= -->

    <div class="table-wrap reveal">


        <!-- Table Header -->

        <div class="panel-head">

            <div class="panel-title">
                Transactions
            </div>

            <span class="muted-note">

                <?php echo nice_date($from); ?>

                —

                <?php echo nice_date($to); ?>

            </span>

        </div>


        <!-- Table -->

        <div class="table-scroll">

            <table class="app-table">


                <!-- Table Head -->

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
                                        href="./edit_expense.php?edit_expense_id=<?php echo (int) $r['item_id']; ?>"
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
                                            value="<?php echo (int) $r['item_id']; ?>"
                                        >

                                        <button
                                            class="mini-btn danger"
                                            type="submit"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php } ?>


                    <?php if ($i === 1): ?>

                        <!-- Empty State -->

                        <tr>

                            <td colspan="6">

                                <div class="empty-state">

                                    <i class="bi bi-receipt"></i>

                                    <h3>
                                        No expenses found
                                    </h3>

                                    <p>
                                        There are no records in this period.
                                    </p>

                                </div>

                            </td>

                        </tr>


                    <?php else: ?>

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

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
<?php

mysqli_stmt_close($stmt);
mysqli_close($conn);

include('./includes/footer.php');

?>