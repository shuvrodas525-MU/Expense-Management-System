<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('./includes/functions.php');
include('./includes/db_conn.php');

check_user();

$user_id = isset($_GET['user_id'])
    ? (int)$_GET['user_id']
    : current_user_id();

/* Security: normal user can only update own picture */
if (!empty($_SESSION['is_admin']) && $user_id > 0) {
    // Admin can update selected user
} else {
    $user_id = current_user_id();
}

$error = '';

/* Upload Image */
if (isset($_POST['submit'])) {

    if (
        !isset($_FILES['user_pic']) ||
        $_FILES['user_pic']['error'] !== UPLOAD_ERR_OK
    ) {

        $error = 'Please select an image.';

    } else {

        $file = $_FILES['user_pic'];

        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        /* File type */
        if (!in_array($extension, $allowed, true)) {

            $error = 'Only JPG, JPEG, PNG or WEBP images are allowed.';

        /* File size: 2MB */
        } elseif ($file['size'] > 2 * 1024 * 1024) {

            $error = 'Image size must be less than 2MB.';

        } else {

            /*
             * Upload folder
             */
            $upload_dir = __DIR__ . '/images/user_image/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0775, true);
            }

            /*
             * Generate unique filename
             */
            $new_file_name =
                bin2hex(random_bytes(12))
                . '.'
                . $extension;

            $target_file =
                $upload_dir . $new_file_name;


            /*
             * Move uploaded image
             */
            if (move_uploaded_file(
                $file['tmp_name'],
                $target_file
            )) {

                /*
                 * Save filename in database
                 */
                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE reg_users
                     SET user_pic = ?
                     WHERE reg_id = ?"
                );

                if ($stmt) {

                    mysqli_stmt_bind_param(
                        $stmt,
                        "si",
                        $new_file_name,
                        $user_id
                    );

                    $result = mysqli_stmt_execute($stmt);

                    mysqli_stmt_close($stmt);


                    if ($result) {

                        /*
                         * IMPORTANT:
                         * Update current session immediately.
                         * No logout/login required.
                         */
                        if ($user_id === current_user_id()) {
                            $_SESSION['user_pic'] = $new_file_name;
                        }

                        redirect_msg(
                            './index.php',
                            'success',
                            'Profile image updated successfully.'
                        );

                        exit;

                    } else {

                        /* Delete uploaded file if DB update fails */
                        if (file_exists($target_file)) {
                            unlink($target_file);
                        }

                        $error = 'Could not update image in database.';
                    }

                } else {

                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }

                    $error = 'Database error.';
                }

            } else {

                $error = 'Could not upload the image. Check the images/user_image folder permission.';
            }
        }
    }
}

include('./includes/header.php');

?>

<div class="auth-page">

    <section class="auth-card reveal visible">

        <div class="auth-brand">

            <span class="brand-mark">
                <i class="bi bi-person-circle"></i>
            </span>

            Profile

        </div>


        <h1>Profile image.</h1>

        <div class="sub">
            Upload a new profile picture for your account.
        </div>


        <?php if (!empty($error)): ?>

            <div
                class="toast-alert danger"
                style="position:static;margin-bottom:16px"
            >

                <i class="bi bi-exclamation-circle"></i>

                <?php echo e($error); ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="field">

                <label for="user_pic">
                    Select Image
                </label>

                <input
                    type="file"
                    id="user_pic"
                    name="user_pic"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    required
                >

            </div>


            <button
                type="submit"
                name="submit"
                class="btn btn-neon"
                style="width:100%"
            >

                <i class="bi bi-cloud-arrow-up"></i>

                Upload Image

            </button>

        </form>


        <div
            style="
                margin-top:15px;
                text-align:center;
                opacity:.65;
                font-size:13px;
            "
        >

            JPG, PNG or WEBP • Maximum 2MB

        </div>

    </section>

</div>


<?php

mysqli_close($conn);

include('./includes/footer.php');

?>