<?php

/* =========================================================
   INITIALIZATION
   ========================================================= */

include('./includes/functions.php');

include('./includes/db_conn.php');


/* =========================================================
   VARIABLES
   ========================================================= */

$message = null;
$error   = null;


/* =========================================================
   PASSWORD RECOVERY
   ========================================================= */

if (isset($_POST['recover'])) {

    $username = trim(
        $_POST['user_name'] ?? ''
    );

    $new_password = $_POST['new_password'] ?? '';

    $confirm_password = $_POST['confirm_password'] ?? '';


    /* -----------------------------------------------------
       VALIDATION
       ----------------------------------------------------- */

    if (strlen($new_password) < 4) {

        $error = 'Password must be at least 4 characters.';

    } elseif ($new_password !== $confirm_password) {

        $error = 'Passwords do not match.';

    } else {


        /* -------------------------------------------------
           FIND USER
           ------------------------------------------------- */

        $stmt = mysqli_prepare(
            $conn,
            'SELECT reg_id
             FROM reg_users
             WHERE user_name = ?
             LIMIT 1'
        );


        if (!$stmt) {

            $error = 'Database error. Please try again.';

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                's',
                $username
            );

            mysqli_stmt_execute($stmt);


            $result = mysqli_stmt_get_result($stmt);

            $user = mysqli_fetch_assoc($result);


            mysqli_stmt_close($stmt);


            /* ---------------------------------------------
               USER NOT FOUND
            --------------------------------------------- */

            if (!$user) {

                $error = 'Username not found.';

            } else {


                /* -----------------------------------------
                   HASH NEW PASSWORD
                ----------------------------------------- */

                $password_hash = password_hash(
                    $new_password,
                    PASSWORD_DEFAULT
                );


                /* -----------------------------------------
                   UPDATE PASSWORD
                ----------------------------------------- */

                $stmt = mysqli_prepare(
                    $conn,
                    'UPDATE reg_users
                     SET user_pass = ?
                     WHERE reg_id = ?'
                );


                if (!$stmt) {

                    $error = 'Could not update password.';

                } else {

                    mysqli_stmt_bind_param(
                        $stmt,
                        'si',
                        $password_hash,
                        $user['reg_id']
                    );


                    $updated = mysqli_stmt_execute($stmt);


                    mysqli_stmt_close($stmt);


                    if ($updated) {

                        $message =
                            'Password updated. You can sign in now.';

                    } else {

                        $error =
                            'Could not update password. Please try again.';

                    }

                }

            }

        }

    }

}


/* =========================================================
   HEADER
   ========================================================= */

include('./includes/header.php');

?>


<!-- =======================================================
     PASSWORD RECOVERY PAGE
======================================================== -->

<div class="auth-page">


    <section class="auth-card reveal visible">


        <!-- BRAND -->

        <div class="auth-brand">

            <span class="brand-mark">

                <i class="bi bi-shield-lock"></i>

            </span>

            Expense<span class="brand-accent">Flow</span>

        </div>


        <!-- TITLE -->

        <h1>
            Reset access.
        </h1>


        <div class="sub">

            Local XAMPP recovery: enter your username
            and choose a new password.

        </div>


        <!-- SUCCESS MESSAGE -->

        <?php if ($message): ?>

            <div
                class="toast-alert success"
                style="position:static;margin-bottom:16px;"
            >

                <i class="bi bi-check-circle"></i>

                <?php echo e($message); ?>

            </div>

        <?php endif; ?>


        <!-- ERROR MESSAGE -->

        <?php if ($error): ?>

            <div
                class="toast-alert danger"
                style="position:static;margin-bottom:16px;"
            >

                <i class="bi bi-exclamation-circle"></i>

                <?php echo e($error); ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             RECOVERY FORM
        ================================================== -->

        <form method="POST">


            <!-- USERNAME -->

            <div class="field">

                <label for="user_name">
                    Username
                </label>

                <input
                    id="user_name"
                    type="text"
                    name="user_name"
                    autocomplete="username"
                    required
                >

            </div>


            <!-- NEW PASSWORD -->

            <div class="field">

                <label for="new_password">
                    New password
                </label>

                <input
                    id="new_password"
                    type="password"
                    name="new_password"
                    minlength="4"
                    autocomplete="new-password"
                    required
                >

            </div>


            <!-- CONFIRM PASSWORD -->

            <div class="field">

                <label for="confirm_password">
                    Confirm new password
                </label>

                <input
                    id="confirm_password"
                    type="password"
                    name="confirm_password"
                    minlength="4"
                    autocomplete="new-password"
                    required
                >

            </div>


            <!-- SUBMIT -->

            <button
                type="submit"
                class="btn btn-neon"
                name="recover"
                style="width:100%;"
            >

                <i class="bi bi-shield-check"></i>

                Update password

            </button>


        </form>


        <!-- BACK TO LOGIN -->

        <div class="auth-links">

            <a href="./login_user.php">

                <i class="bi bi-arrow-left"></i>

                Back to login

            </a>

        </div>


    </section>

</div>


<?php

/* =========================================================
   FOOTER
   ========================================================= */

mysqli_close($conn);

include('./includes/footer.php');

?>