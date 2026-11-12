<?php

include('./includes/functions.php');
check_user();

include('./includes/db_conn.php');


/* =========================
   User ID
   ========================= */

$id = (int) (
    $_GET['update_pass_id'] ?? current_user_id()
);


/* =========================
   Access Check
   ========================= */

if (
    empty($_SESSION['is_admin']) &&
    $id !== current_user_id()
) {

    redirect_msg(
        './index.php',
        'danger',
        'Access denied.'
    );
}


/* =========================
   Get User
   ========================= */

$s = mysqli_prepare(
    $conn,
    'SELECT user_name
     FROM reg_users
     WHERE reg_id=?'
);

mysqli_stmt_bind_param(
    $s,
    'i',
    $id
);

mysqli_stmt_execute($s);

$r = mysqli_fetch_assoc(
    mysqli_stmt_get_result($s)
);

mysqli_stmt_close($s);


if (!$r) {

    redirect_msg(
        './index.php',
        'danger',
        'User not found.'
    );

}


/* =========================
   Update Password
   ========================= */

if (isset($_POST['update_pass'])) {

    $p = $_POST['update_user_pass'] ?? '';
    $c = $_POST['confirm_pass'] ?? '';


    /* Validate Password */

    if (
        strlen($p) < 4 ||
        $p !== $c
    ) {

        $error =
            'Passwords must match and contain at least 4 characters.';

    } else {


        /* Hash Password */

        $hash = password_hash(
            $p,
            PASSWORD_DEFAULT
        );


        /* Update Database */

        $s = mysqli_prepare(
            $conn,
            'UPDATE reg_users
             SET user_pass=?
             WHERE reg_id=?'
        );

        mysqli_stmt_bind_param(
            $s,
            'si',
            $hash,
            $id
        );

        mysqli_stmt_execute($s);

        mysqli_stmt_close($s);


        /* Success */

        redirect_msg(
            './index.php',
            'success',
            'Password updated successfully.'
        );

    }

}


include('./includes/header.php');

?>


<div class="auth-page">


    <!-- =========================
         Security Card
         ========================= -->

    <section class="auth-card reveal visible">


        <!-- Brand -->

        <div class="auth-brand">

            <span class="brand-mark">
                <i class="bi bi-shield-lock"></i>
            </span>

            Security

        </div>


        <!-- Heading -->

        <h1>
            New password.
        </h1>


        <!-- User Information -->

        <div class="sub">

            Updating password for

            <strong>
                <?php echo e($r['user_name']); ?>
            </strong>

        </div>


        <!-- Error Message -->

        <?php if (isset($error)): ?>

            <div
                class="toast-alert danger"
                style="position:static;margin-bottom:16px"
            >

                <i class="bi bi-exclamation-circle"></i>

                <?php echo e($error); ?>

            </div>

        <?php endif; ?>


        <!-- Password Form -->

        <form method="POST">


            <!-- New Password -->

            <div class="field">

                <label>
                    New password
                </label>

                <input
                    type="password"
                    name="update_user_pass"
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


            <!-- Submit -->

            <button
                class="btn btn-neon"
                name="update_pass"
                style="width:100%"
            >
                Update password
            </button>

        </form>

    </section>

</div>


<?php

mysqli_close($conn);

include('./includes/footer.php');

?>