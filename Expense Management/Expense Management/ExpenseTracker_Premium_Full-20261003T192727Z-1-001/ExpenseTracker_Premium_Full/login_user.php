<?php

include('./includes/functions.php');
include('./includes/db_conn.php');

if (!empty($_SESSION['is_login'])) {
    header('Location: ./index.php');
    exit;
}

if (isset($_POST['login'])) {

    $name = trim($_POST['user_name'] ?? '');
    $pass = $_POST['user_pass'] ?? '';

    $stmt = mysqli_prepare(
        $conn,
        'SELECT reg_id, user_name, user_pass, user_pic, is_admin 
         FROM reg_users 
         WHERE user_name = ? 
         LIMIT 1'
    );

    mysqli_stmt_bind_param($stmt, 's', $name);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $r = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if ($r && password_verify($pass, $r['user_pass'])) {

        session_regenerate_id(true);

        $_SESSION['is_login'] = true;
        $_SESSION['user_id'] = (int)$r['reg_id'];
        $_SESSION['name'] = $r['user_name'];

        // Profile image session
        $_SESSION['user_pic'] = $r['user_pic'] ?? '';

        // Admin status
        $_SESSION['is_admin'] = ((int)$r['is_admin'] === 1);

        redirect_msg(
            './index.php',
            'success',
            'Welcome back, ' . $r['user_name'] . '!'
        );
    }

    $error = 'Invalid username or password.';
}

include('./includes/header.php');

?>

<div class="auth-page">

    <section class="auth-card reveal visible">

        <div class="auth-brand">
            <span class="brand-mark">
                <i class="bi bi-wallet2"></i>
            </span>

            Expense<span class="brand-accent">Flow</span>
        </div>

        <h1>Welcome back.</h1>

        <div class="sub">
            Sign in to continue to your personal expense dashboard.
        </div>

        <?php if (isset($error)): ?>

            <div
                class="toast-alert danger"
                style="position:static;margin-bottom:16px;"
            >
                <i class="bi bi-exclamation-circle"></i>

                <?php echo e($error); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="field">

                <label>Username</label>

                <input
                    type="text"
                    name="user_name"
                    required
                    autocomplete="username"
                    value="<?php echo e($_POST['user_name'] ?? ''); ?>"
                >

            </div>

            <div class="field">

                <label>Password</label>

                <input
                    type="password"
                    name="user_pass"
                    required
                    autocomplete="current-password"
                >

            </div>

            <button
                type="submit"
                class="btn btn-neon"
                style="width:100%;"
                name="login"
            >
                Sign in
                <i class="bi bi-arrow-right"></i>
            </button>

        </form>

        <div class="auth-links">

            <a href="./forgot_password.php">
                Forgot password?
            </a>

            <a href="./register_user.php">
                Create account
            </a>

        </div>

    </section>

</div>

<?php

mysqli_close($conn);

include('./includes/footer.php');

?>