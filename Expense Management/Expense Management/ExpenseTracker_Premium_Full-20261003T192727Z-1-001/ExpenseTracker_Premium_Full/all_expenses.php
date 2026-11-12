<?php

/* =========================================================
   INITIALIZATION
   ========================================================= */

include('./includes/functions.php');

check_user();

include('./includes/db_conn.php');


/* =========================================================
   CURRENT USER & DATES
   ========================================================= */

$uid = current_user_id();

$today = date('Y-m-d');

$month_start = date('Y-m-01');


/* =========================================================
   PERIOD SUMMARY FUNCTION
   ========================================================= */

function period($conn, $user_id, $start_date, $end_date)
{
    $stmt = mysqli_prepare(
        $conn,
        'SELECT
            COUNT(*) AS c,
            COALESCE(SUM(item_price), 0) AS total
         FROM expense_info
         WHERE user_id = ?
         AND item_date BETWEEN ? AND ?'
    );

    if (!$stmt) {
        return [
            'c'     => 0,
            'total' => 0
        ];
    }


    mysqli_stmt_bind_param(
        $stmt,
        'iss',
        $user_id,
        $start_date,
        $end_date
    );


    mysqli_stmt_execute($stmt);


    $result = mysqli_stmt_get_result($stmt);

    $data = mysqli_fetch_assoc($result);


    mysqli_stmt_close($stmt);


    return $data ?: [
        'c'     => 0,
        'total' => 0
    ];
}


/* =========================================================
   EXPENSE PERIOD DATA
   ========================================================= */

$periods = [

    [
        'title' => 'Today',

        'url' => 'today_expense.php',

        'data' => period(
            $conn,
            $uid,
            $today,
            $today
        )
    ],

    [
        'title' => 'Yesterday',

        'url' => 'yesterday_expense.php',

        'data' => period(
            $conn,
            $uid,
            date(
                'Y-m-d',
                strtotime('-1 day')
            ),
            date(
                'Y-m-d',
                strtotime('-1 day')
            )
        )
    ],

    [
        'title' => 'Last 7 days',

        'url' => 'seven_days_expense.php',

        'data' => period(
            $conn,
            $uid,
            date(
                'Y-m-d',
                strtotime('-6 days')
            ),
            $today
        )
    ],

    [
        'title' => 'This month',

        'url' => 'month_expense.php',

        'data' => period(
            $conn,
            $uid,
            $month_start,
            $today
        )
    ]

];


/* =========================================================
   HEADER
   ========================================================= */

include('./includes/header.php');

?>


<!-- =======================================================
     EXPENSES PAGE
======================================================== -->

<div class="shell">


    <!-- PAGE HEADER -->

    <div class="page-head reveal">

        <div>

            <div class="eyebrow">
                Transactions
            </div>

            <h1>
                Expenses
            </h1>

            <p>
                Choose a view or search your own transaction history.
            </p>

        </div>


        <!-- ADD EXPENSE -->

        <a
            class="btn btn-neon"
            href="./add_expense.php"
        >

            <i class="bi bi-plus-lg"></i>

            Add Expense

        </a>

    </div>



    <!-- ===================================================
         PERIOD CARDS
    ==================================================== -->

    <div class="period-grid">


        <?php foreach ($periods as $period): ?>


            <a
                class="period-card reveal"
                href="./<?php echo e($period['url']); ?>"
            >


                <!-- ICON -->

                <div class="period-icon">

                    <i class="bi bi-receipt"></i>

                </div>


                <!-- TITLE -->

                <h3>

                    <?php echo e($period['title']); ?>

                </h3>


                <!-- COUNT -->

                <div class="count">

                    <?php

                    echo (int) $period['data']['c'];

                    ?>

                </div>


                <!-- TOTAL -->

                <p>

                    <?php

                    echo money(
                        $period['data']['total']
                    );

                    ?>

                    total

                </p>


                <!-- LINK -->

                <span class="period-link">

                    Open transactions

                    <i class="bi bi-arrow-up-right"></i>

                </span>


            </a>


        <?php endforeach; ?>


    </div>



    <!-- ===================================================
         SEARCH PANEL
    ==================================================== -->

    <div
        class="panel reveal"
        style="margin-top:18px;"
    >


        <div class="panel-head">


            <div class="panel-title">

                Search by date

            </div>


            <a
                class="panel-link"
                href="./search_expense.php"
            >

                Open search

                <i class="bi bi-arrow-right"></i>

            </a>


        </div>


        <div
            style="
                padding:22px;
                color:var(--muted);
                font-size:13px;
            "
        >

            Filter your saved expenses between any two dates.

        </div>


    </div>


</div>


<?php

/* =========================================================
   FOOTER
   ========================================================= */

include('./includes/footer.php');

?>