<?php

include('./includes/functions.php');
check_user();

include('./includes/db_conn.php');

$uid = current_user_id();


/* =========================
   Report Date
   ========================= */

$month = (int) ($_GET['month'] ?? date('n'));
$year  = (int) ($_GET['year'] ?? date('Y'));


/* Validate Month & Year */

if ($month < 1 || $month > 12) {
    $month = (int) date('n');
}

if ($year < 2000 || $year > 2100) {
    $year = (int) date('Y');
}


$start = sprintf(
    '%04d-%02d-01',
    $year,
    $month
);

$end = date(
    'Y-m-t',
    strtotime($start)
);

$label = date(
    'F Y',
    strtotime($start)
);


/* =========================
   Monthly Summary
   ========================= */

$s = mysqli_prepare(
    $conn,
    'SELECT
        COUNT(*) cnt,
        COALESCE(SUM(item_price),0) total,
        COALESCE(AVG(item_price),0) avg_price,
        COALESCE(MAX(item_price),0) max_price
     FROM expense_info
     WHERE user_id=?
     AND item_date BETWEEN ? AND ?'
);

mysqli_stmt_bind_param(
    $s,
    'iss',
    $uid,
    $start,
    $end
);

mysqli_stmt_execute($s);

$summary = mysqli_fetch_assoc(
    mysqli_stmt_get_result($s)
);

mysqli_stmt_close($s);


/* =========================
   Daily Spending
   ========================= */

$daily = [];

$s = mysqli_prepare(
    $conn,
    'SELECT
        DAY(item_date) d,
        SUM(item_price) total
     FROM expense_info
     WHERE user_id=?
     AND item_date BETWEEN ? AND ?
     GROUP BY DAY(item_date)
     ORDER BY d'
);

mysqli_stmt_bind_param(
    $s,
    'iss',
    $uid,
    $start,
    $end
);

mysqli_stmt_execute($s);

$rr = mysqli_stmt_get_result($s);


while ($r = mysqli_fetch_assoc($rr)) {

    $daily[] = $r;

}

mysqli_stmt_close($s);


/* =========================
   Monthly Transactions
   ========================= */

$s = mysqli_prepare(
    $conn,
    'SELECT
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
    $start,
    $end
);

mysqli_stmt_execute($s);

$transactions = [];

$rr = mysqli_stmt_get_result($s);


while ($r = mysqli_fetch_assoc($rr)) {

    $transactions[] = $r;

}

mysqli_stmt_close($s);

mysqli_close($conn);


/* =========================
   CSV Export
   ========================= */

if (
    isset($_GET['export']) &&
    $_GET['export'] === 'csv'
) {

    header('Content-Type: text/csv; charset=utf-8');

    header(
        'Content-Disposition: attachment; filename="expense-report-' .
        $year .
        '-' .
        str_pad($month, 2, '0', STR_PAD_LEFT) .
        '.csv"'
    );


    $out = fopen(
        'php://output',
        'w'
    );


    /* CSV Header */

    fputcsv(
        $out,
        [
            'Date',
            'Expense',
            'Amount',
            'Details'
        ]
    );


    $csvTotal = 0;


    /* CSV Transactions */

    foreach ($transactions as $r) {

        fputcsv(
            $out,
            [
                $r['item_date'],
                $r['item_name'],
                $r['item_price'],
                $r['item_details']
            ]
        );

        $csvTotal += (float) $r['item_price'];
    }


    /* CSV Total */

    fputcsv(
        $out,
        [
            '',
            'TOTAL',
            $csvTotal,
            ''
        ]
    );


    fclose($out);

    exit;
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
                Analytics
            </div>

            <h1>
                <?php echo e($label); ?>
            </h1>

            <p>
                Every number below comes directly from
                your saved transactions.
            </p>

        </div>


        <div style="display:flex;gap:9px;">

            <!-- Print -->

            <button
                class="btn btn-ghost"
                onclick="window.print()"
            >
                <i class="bi bi-printer"></i>
                Print
            </button>


            <!-- CSV Export -->

            <a
                class="btn btn-neon"
                href="./reports.php?month=<?php echo $month; ?>&year=<?php echo $year; ?>&export=csv"
            >
                <i class="bi bi-download"></i>
                CSV
            </a>

        </div>

    </div>


    <!-- =========================
         Report Filter
         ========================= -->

    <form
        class="form-panel filter-bar reveal"
        method="GET"
    >

        <!-- Month -->

        <div class="field">

            <label>
                Month
            </label>

            <select name="month">

                <?php for ($m = 1; $m <= 12; $m++): ?>

                    <option
                        value="<?php echo $m; ?>"
                        <?php echo $m === $month ? 'selected' : ''; ?>
                    >
                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                    </option>

                <?php endfor; ?>

            </select>

        </div>


        <!-- Year -->

        <div class="field">

            <label>
                Year
            </label>

            <input
                name="year"
                type="number"
                min="2000"
                max="2100"
                value="<?php echo $year; ?>"
            >

        </div>


        <!-- Update -->

        <button class="btn btn-neon">
            Update report
        </button>

    </form>


    <!-- =========================
         Report Statistics
         ========================= -->

    <div class="report-stats">


        <!-- Total Spent -->

        <div class="report-stat reveal">

            <div class="label">
                Total spent
            </div>

            <div class="value">
                <?php echo money($summary['total']); ?>
            </div>

        </div>


        <!-- Transactions -->

        <div class="report-stat reveal">

            <div class="label">
                Transactions
            </div>

            <div class="value">
                <?php echo $summary['cnt']; ?>
            </div>

        </div>


        <!-- Average -->

        <div class="report-stat reveal">

            <div class="label">
                Average
            </div>

            <div class="value">
                <?php echo money($summary['avg_price']); ?>
            </div>

        </div>


        <!-- Largest -->

        <div class="report-stat reveal">

            <div class="label">
                Largest
            </div>

            <div class="value">
                <?php echo money($summary['max_price']); ?>
            </div>

        </div>

    </div>


    <!-- =========================
         Report Layout
         ========================= -->

    <div class="report-layout">


        <!-- Daily Spending Chart -->

        <div class="panel reveal">

            <div class="panel-head">

                <div class="panel-title">
                    Daily spending
                </div>

                <span class="muted-note">
                    <?php echo e($label); ?>
                </span>

            </div>


            <div class="chart-box">

                <canvas id="dailyChart"></canvas>

            </div>

        </div>


        <!-- Recent Transactions -->

        <div class="panel reveal">

            <div class="panel-head">

                <div class="panel-title">
                    Recent in this month
                </div>

            </div>


            <?php if ($transactions): ?>

                <?php foreach (array_slice($transactions, 0, 5) as $r): ?>

                    <div class="expense-row">

                        <div>

                            <div class="expense-name">
                                <?php echo e($r['item_name']); ?>
                            </div>

                            <div class="expense-sub">
                                <?php echo nice_date($r['item_date']); ?>
                            </div>

                        </div>

                        <div class="expense-amount">
                            <?php echo money($r['item_price']); ?>
                        </div>

                    </div>

                <?php endforeach; ?>


            <?php else: ?>

                <div class="empty-state">

                    <i class="bi bi-bar-chart"></i>

                    <h3>
                        No data
                    </h3>

                    <p>
                        Add expenses for this month
                        to see insights.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- =========================
         All Transactions
         ========================= -->

    <div
        class="table-wrap reveal"
        style="margin-top:16px;"
    >

        <div class="panel-head">

            <div class="panel-title">
                All transactions
            </div>

            <span class="muted-note">
                No demo rows · no fake totals
            </span>

        </div>


        <div class="table-scroll">

            <table class="app-table">


                <!-- Table Header -->

                <thead>

                    <tr>
                        <th>Date</th>
                        <th>Expense</th>
                        <th>Amount</th>
                        <th>Details</th>
                    </tr>

                </thead>


                <!-- Table Body -->

                <tbody>

                    <?php if ($transactions): ?>

                        <?php foreach ($transactions as $r): ?>

                            <tr>

                                <td>
                                    <?php echo nice_date($r['item_date']); ?>
                                </td>

                                <td>
                                    <?php echo e($r['item_name']); ?>
                                </td>

                                <td class="amount-cell">
                                    <?php echo money($r['item_price']); ?>
                                </td>

                                <td>
                                    <?php echo e($r['item_details']); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <tr>

                            <td colspan="4">

                                <div class="empty-state">

                                    <i class="bi bi-inbox"></i>

                                    <h3>
                                        No transactions
                                    </h3>

                                    <p>
                                        This report is empty until
                                        you add an expense.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- =========================
     Chart.js
     ========================= -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    const d = <?php echo json_encode($daily); ?>;


    new Chart(
        document.getElementById('dailyChart'),
        {
            type: 'bar',

            data: {

                labels: d.map(
                    x => x.d
                ),

                datasets: [

                    {
                        data: d.map(
                            x => Number(x.total)
                        ),

                        backgroundColor:
                            'rgba(46,230,199,.45)',

                        borderColor:
                            '#2ee6c7',

                        borderWidth: 1,

                        borderRadius: 8
                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    x: {

                        grid: {
                            display: false
                        },

                        ticks: {
                            color: '#8491a8'
                        }

                    },

                    y: {

                        grid: {
                            color:
                                'rgba(148,163,184,.1)'
                        },

                        ticks: {
                            color: '#8491a8'
                        }

                    }

                }

            }

        }
    );

</script>


<?php

include('./includes/footer.php');

?>