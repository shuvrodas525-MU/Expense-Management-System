<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Required files
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db_conn.php';


/*
|--------------------------------------------------------------------------
| Page title
|--------------------------------------------------------------------------
*/
$filename = basename($_SERVER['PHP_SELF'], '.php');

$page_title = ucfirst(str_replace('_', ' ', $filename));

if ($filename === 'index') {
    $page_title = 'Dashboard';
}


/*
|--------------------------------------------------------------------------
| User profile image
|--------------------------------------------------------------------------
*/
$user_pic = $_SESSION['user_pic'] ?? '';

$image_path = '';

if (!empty($user_pic)) {

    $server_image_path = __DIR__ . '/../images/user_image/' . $user_pic;

    if (file_exists($server_image_path)) {
        $image_path = './images/user_image/' . rawurlencode($user_pic);
    }
}

?>
<!doctype html>

<html lang="en" data-theme="dark">

<head>

<meta charset="utf-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<meta
    name="theme-color"
    content="#070b14"
>

<title>
    <?php echo e($page_title); ?> — ExpenseFlow
</title>


<!-- Google Fonts -->

<link rel="preconnect" href="https://fonts.googleapis.com">

<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
    crossorigin
>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet"
>


<!-- Bootstrap Icons -->

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
>


<!-- Modern Theme -->

<link
    rel="stylesheet"
    href="./assets/css/modern-theme.css"
>


<style>

/* Profile avatar */

.avatar {
    width: 40px;
    height: 40px;
    min-width: 40px;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    background: linear-gradient(
        135deg,
        #00ffd1,
        #7c5cff
    );

    color: #ffffff;

    font-weight: 800;

    border: 2px solid rgba(0,255,209,0.45);

    box-shadow:
        0 0 15px rgba(0,255,209,0.20);
}


/* Uploaded profile image */

.avatar img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    border-radius: 50%;
}


/* Profile button */

.profile-trigger {
    display: flex;
    align-items: center;
    gap: 9px;
}


/* Mobile */

@media (max-width: 600px) {

    .profile-name {
        display: none;
    }

}

</style>

</head>


<body>

<div class="ambient a1"></div>

<div class="ambient a2"></div>


<?php if (!empty($_SESSION['is_login'])): ?>


<nav class="app-nav">

<div class="nav-inner">


<!-- BRAND -->

<a
    class="brand"
    href="./index.php"
>

    <span class="brand-mark">

        <i class="bi bi-wallet2"></i>

    </span>

    <span>

        Expense<span class="brand-accent">Flow</span>

    </span>

</a>



<!-- NAVIGATION -->

<div class="nav-links">


<a
    class="<?php echo $filename === 'index' ? 'active' : ''; ?>"
    href="./index.php"
>

    <i class="bi bi-grid-1x2-fill"></i>

    Dashboard

</a>



<a
    class="<?php echo in_array(
        $filename,
        [
            'all_expenses',
            'today_expense',
            'yesterday_expense',
            'seven_days_expense',
            'search_expense',
            'month_expense'
        ],
        true
    ) ? 'active' : ''; ?>"
    href="./all_expenses.php"
>

    <i class="bi bi-receipt"></i>

    Expenses

</a>



<a
    class="<?php echo $filename === 'reports' ? 'active' : ''; ?>"
    href="./reports.php"
>

    <i class="bi bi-bar-chart-line-fill"></i>

    Reports

</a>



<?php if (!empty($_SESSION['is_admin'])): ?>

<a
    class="<?php echo $filename === 'display_reg_users' ? 'active' : ''; ?>"
    href="./display_reg_users.php"
>

    <i class="bi bi-people-fill"></i>

    Users

</a>

<?php endif; ?>


</div>



<!-- RIGHT SIDE -->

<div class="nav-actions">


<!-- ADD EXPENSE -->

<a
    href="./add_expense.php"
    class="btn btn-neon btn-small"
>

    <i class="bi bi-plus-lg"></i>

    <span>Add Expense</span>

</a>



<!-- THEME -->

<button
    class="icon-btn"
    id="themeToggle"
    type="button"
    aria-label="Toggle theme"
>

    <i class="bi bi-moon-stars-fill"></i>

</button>



<!-- PROFILE -->

<div class="profile-menu">


<button
    class="profile-trigger"
    id="profileTrigger"
    type="button"
>


<?php if (!empty($image_path)): ?>


<!-- SHOW UPLOADED IMAGE -->

<span class="avatar">

    <img
        src="<?php echo e($image_path); ?>"
        alt="Profile Picture"
    >

</span>


<?php else: ?>


<!-- SHOW FIRST LETTER IF NO IMAGE -->

<span class="avatar">

    <?php

    echo e(
        strtoupper(
            substr(
                $_SESSION['name'] ?? 'U',
                0,
                1
            )
        )
    );

    ?>

</span>


<?php endif; ?>


<span class="profile-name">

    <?php echo e($_SESSION['name'] ?? 'User'); ?>

</span>


<i class="bi bi-chevron-down"></i>


</button>



<!-- DROPDOWN -->

<div
    class="profile-dropdown"
    id="profileDropdown"
>


<a
    href="./upload_img.php?user_id=<?php echo current_user_id(); ?>"
>

    <i class="bi bi-person-circle"></i>

    Profile picture

</a>



<a
    href="./update_user_pass.php?update_pass_id=<?php echo current_user_id(); ?>"
>

    <i class="bi bi-shield-lock"></i>

    Change password

</a>



<div class="dropdown-line"></div>



<a
    class="danger-link"
    href="./logout.php"
>

    <i class="bi bi-box-arrow-right"></i>

    Log out

</a>


</div>


</div>


</div>

</div>

</nav>


<?php endif; ?>


<main class="app-main">


<?php

if (
    isset(
        $_SESSION['message'],
        $_SESSION['color']
    )
) {

    my_alert(
        $_SESSION['color'],
        $_SESSION['message']
    );

    unset(
        $_SESSION['message'],
        $_SESSION['color']
    );
}

?>