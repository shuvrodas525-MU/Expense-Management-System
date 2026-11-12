<?php

include('./includes/functions.php');
include('./includes/db_conn.php');


/* =========================
   Admin Access Check
   ========================= */

if (
    !empty($_SESSION['is_login']) &&
    !empty($_SESSION['is_admin'])
) {

    // Administrator is allowed to continue.

} elseif (!empty($_SESSION['is_login'])) {

    redirect_msg(
        './index.php',
        'warning',
        'Only an administrator can add another user.'
    );
}


/* =========================
   Register User
   ========================= */

if (isset($_POST['register'])) {

    $name    = trim($_POST['user_name'] ?? '');
    $pass    = $_POST['user_pass'] ?? '';
    $confirm = $_POST['confirm_pass'] ?? '';
    $admin   = !empty($_POST['is_admin']) ? 1 : 0;


    /* Validate Input */

    if (
        $name === '' ||
        strlen($pass) < 4
    ) {

        $error =
            'Username and a password of at least 4 characters are required.';

    } elseif ($pass !== $confirm) {

        $error = 'Passwords do not match.';

    } else {


        /* Check Existing Username */

        $stmt = mysqli_prepare(
            $conn,
            'SELECT reg_id
             FROM reg_users
             WHERE user_name=?
             LIMIT 1'
        );

        mysqli_stmt_bind_param(
            $stmt,
            's',
            $name
        );

        mysqli_stmt_execute($stmt);

        $exists =
            mysqli_stmt_get_result($stmt)->num_rows > 0;

        mysqli_stmt_close($stmt);


        /* Username Already Exists */

        if ($exists) {

            $error = 'That username already exists.';

        } else {


            /* Create Password Hash */

            $hash = password_hash(
                $pass,
                PASSWORD_DEFAULT
            );


            /* Insert New User */

            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO reg_users
                (user_name, user_pass, is_admin)
                VALUES (?, ?, ?)'
            );

            mysqli_stmt_bind_param(
                $stmt,
                'ssi',
                $name,
                $hash,
                $admin
            );

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);


            /* Success */

            redirect_msg(
                './login_user.php',
                'success',
                'Account created successfully.'
            );
        }
    }
}


include('./includes/header.php');

?>


<div class="auth-page">


    <!-- =========================
         Authentication Card
         ========================= -->

    <section class="auth-card reveal visible">


        <!-- Brand -->

        <div class="auth-brand">

            <span class="brand-mark">
                <i class="bi bi-wallet2"></i>
            </span>

            Expense<span class="brand-accent">Flow</span>

        </div>


        <!-- Heading -->

        <h1>
            Create account.
        </h1>

        <div class="sub">
            Start with a clean personal expense workspace.
        </div>


        <!-- Error Message -->

        <?php if (isset($error)): ?>

            <div
                class="toast-alert danger"
                style="position:static;margin-bottom:16px;"
            >

                <i class="bi bi-exclamation-circle"></i>

                <?php echo e($error); ?>

            </div>

        <?php endif; ?>


        <!-- Registration Form -->

        <form method="POST">


            <!-- Username -->

            <div class="field">

                <label>
                    Username
                </label>

                <input
                    name="user_name"
                    required
                >

            </div>


            <!-- Password -->

            <div class="field">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="user_pass"
                    minlength="4"
                    required
                >

            </div>


            <!-- Confirm Password -->

            <div class="field">

                <label>
                    Confirm password
                </label>

                <input
                    type="password"
                    name="confirm_pass"
                    minlength="4"
                    required
                >

            </div>


            <!-- Admin Option -->

            <?php if (!empty($_SESSION['is_admin'])): ?>

                <div class="field">

                    <label>

                        <input
                            type="checkbox"
                            name="is_admin"
                            value="1"
                        >

                        Make administrator

                    </label>

                </div>

            <?php endif; ?>


            <!-- Submit -->

            <button
                class="btn btn-neon"
                style="width:100%"
                name="register"
            >

                Create account

                <i class="bi bi-arrow-right"></i>

            </button>

        </form>


        <!-- Authentication Links -->

        <div class="auth-links">

            <a href="./login_user.php">
                Already have an account?
            </a>

            <a href="./welcome.php">
                Home
            </a>

        </div>

    </section>

</div>


<?php

mysqli_close($conn);

include('./includes/footer.php');

?>