<?php

include('./includes/functions.php');
check_user();

include('./includes/db_conn.php');

$uid       = current_user_id();
$today     = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$week      = date('Y-m-d', strtotime('-6 days'));
$month     = date('Y-m-01');


function ds($c, $u, $a, $b)
{
    $s = mysqli_prepare(
        $c,
        'SELECT COUNT(*) cnt, COALESCE(SUM(item_price),0) total
         FROM expense_info
         WHERE user_id=?
         AND item_date BETWEEN ? AND ?'
    );

    mysqli_stmt_bind_param($s, 'iss', $u, $a, $b);
    mysqli_stmt_execute($s);

    $r = mysqli_fetch_assoc(
        mysqli_stmt_get_result($s)
    );

    mysqli_stmt_close($s);

    return $r;
}


/* =========================
   Expense Statistics
   ========================= */

$today_s = ds($conn, $uid, $today, $today);

$yest_s = ds(
    $conn,
    $uid,
    $yesterday,
    $yesterday
);

$week_s = ds(
    $conn,
    $uid,
    $week,
    $today
);

$month_s = ds(
    $conn,
    $uid,
    $month,
    $today
);

$all_s = ds(
    $conn,
    $uid,
    '1900-01-01',
    '2100-12-31'
);


/* =========================
   Recent Expenses
   ========================= */

$s = mysqli_prepare(
    $conn,
    'SELECT item_name, item_price, item_date, item_details
     FROM expense_info
     WHERE user_id=?
     ORDER BY item_date DESC, item_id DESC
     LIMIT 6'
);

mysqli_stmt_bind_param($s, 'i', $uid);
mysqli_stmt_execute($s);

$recent = mysqli_stmt_get_result($s);


include('./includes/header.php');

?>

<div class="shell">

    <!-- =========================
         Hero Section
         ========================= -->

    <section class="hero reveal">

        <div>
            <div class="eyebrow">
                ExpenseFlow
            </div>

            <h1>
                Your <span class="accent">spending.</span>
            </h1>

            <p>
                Welcome back,
                <?php echo e($_SESSION['name']); ?>.
                Your dashboard reflects only the expenses you saved.
            </p>
        </div>

        <div class="hero-action">

            <a href="./add_expense.php" class="btn btn-neon">
                <i class="bi bi-plus-lg"></i>
                Add Expense
            </a>

        </div>

    </section>


    <!-- =========================
         Statistics
         ========================= -->

    <section class="stat-grid">

        <!-- Today -->

        <article class="stat-card reveal">

            <div class="stat-top">

                <span class="stat-label">
                    Today
                </span>

                <span class="stat-icon">
                    <i class="bi bi-lightning-charge-fill"></i>
                </span>

            </div>

            <div class="stat-value">
                <?php echo money($today_s['total']); ?>
            </div>

            <div class="stat-meta">
                <?php echo $today_s['cnt']; ?>
                expense<?php echo $today_s['cnt'] == 1 ? '' : 's'; ?>
            </div>

        </article>


        <!-- Yesterday -->

        <article class="stat-card reveal">

            <div class="stat-top">

                <span class="stat-label">
                    Yesterday
                </span>

                <span class="stat-icon">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </span>

            </div>

            <div class="stat-value">
                <?php echo money($yest_s['total']); ?>
            </div>

            <div class="stat-meta">
                <?php echo $yest_s['cnt']; ?>
                expense<?php echo $yest_s['cnt'] == 1 ? '' : 's'; ?>
            </div>

        </article>


        <!-- Last 7 Days -->

        <article class="stat-card reveal">

            <div class="stat-top">

                <span class="stat-label">
                    Last 7 days
                </span>

                <span class="stat-icon">
                    <i class="bi bi-calendar3"></i>
                </span>

            </div>

            <div class="stat-value">
                <?php echo money($week_s['total']); ?>
            </div>

            <div class="stat-meta">
                <?php echo $week_s['cnt']; ?>
                expense<?php echo $week_s['cnt'] == 1 ? '' : 's'; ?>
            </div>

        </article>


        <!-- This Month -->

        <article class="stat-card reveal">

            <div class="stat-top">

                <span class="stat-label">
                    This month
                </span>

                <span class="stat-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </span>

            </div>

            <div class="stat-value">
                <?php echo money($month_s['total']); ?>
            </div>

            <div class="stat-meta">
                <?php echo $month_s['cnt']; ?>
                expense<?php echo $month_s['cnt'] == 1 ? '' : 's'; ?>
            </div>

        </article>

    </section>


    <!-- =========================
         Main Content
         ========================= -->

    <section class="content-grid">


        <!-- =========================
             Recent Expenses
             ========================= -->

        <div class="panel reveal">

            <div class="panel-head">

                <div class="panel-title">
                    Recent expenses
                </div>

                <a
                    class="panel-link"
                    href="./all_expenses.php"
                >
                    View all
                    <i class="bi bi-arrow-up-right"></i>
                </a>

            </div>


            <?php if (mysqli_num_rows($recent)): ?>

                <?php while ($x = mysqli_fetch_assoc($recent)): ?>

                    <div class="expense-row">

                        <div>

                            <div class="expense-name">
                                <?php echo e($x['item_name']); ?>
                            </div>

                            <div class="expense-sub">

                                <?php echo nice_date($x['item_date']); ?>

                                <?php if ($x['item_details']): ?>

                                    · <?php echo e($x['item_details']); ?>

                                <?php endif; ?>

                            </div>

                        </div>

                        <div class="expense-amount">
                            <?php echo money($x['item_price']); ?>
                        </div>

                    </div>

                <?php endwhile; ?>


            <?php else: ?>

                <!-- Empty State -->

                <div class="empty-state">

                    <i class="bi bi-receipt"></i>

                    <h3>
                        No expenses yet
                    </h3>

                    <p>
                        Your dashboard will fill up from
                        the expenses you add.
                    </p>

                    <a
                        class="btn btn-neon"
                        href="./add_expense.php"
                    >
                        Add first expense
                    </a>

                </div>

            <?php endif; ?>

        </div>


        <!-- =========================
             Side Information
             ========================= -->

        <div class="side-stack">


            <!-- Total Spending -->

            <div class="panel insight reveal">

                <div class="insight-icon">
                    <i class="bi bi-wallet2"></i>
                </div>

                <div class="small">
                    Total spending
                </div>

                <div class="big">
                    <?php echo money($all_s['total']); ?>
                </div>

                <div class="small">
                    Across
                    <?php echo $all_s['cnt']; ?>
                    saved record<?php echo $all_s['cnt'] == 1 ? '' : 's'; ?>
                </div>

            </div>


            <!-- Average Expense -->

            <div class="panel insight reveal">

                <div class="insight-icon">
                    <i class="bi bi-calculator"></i>
                </div>

                <div class="small">
                    Average expense
                </div>

                <div class="big">

                    <?php
                    echo $all_s['cnt']
                        ? money($all_s['total'] / $all_s['cnt'])
                        : money(0);
                    ?>

                </div>

                <div class="small">
                    Calculated from your actual records
                </div>

            </div>

        </div>

    </section>

</div>


<?php

mysqli_stmt_close($s);
mysqli_close($conn);

include('./includes/footer.php');

?>