<?php

/* =========================================================
   INITIALIZATION
   ========================================================= */

include('./includes/functions.php');

check_admin();

include('./includes/db_conn.php');


/* =========================================================
   FETCH REGISTERED USERS
   ========================================================= */

$users_query = mysqli_query(
    $conn,
    'SELECT
        reg_id,
        user_name,
        user_pic,
        is_admin
     FROM reg_users
     ORDER BY reg_id DESC'
);


/* =========================================================
   HEADER
   ========================================================= */

include('./includes/header.php');

?>


<!-- =======================================================
     USERS MANAGEMENT PAGE
======================================================== -->

<div class="shell">


    <!-- PAGE HEADER -->

    <div class="page-head reveal">

        <div>

            <div class="eyebrow">
                Administration
            </div>

            <h1>
                Users
            </h1>

            <p>
                Manage accounts from one standardized interface.
            </p>

        </div>


        <!-- ADD USER -->

        <a
            class="btn btn-neon"
            href="./register_user.php"
        >

            <i class="bi bi-person-plus"></i>

            Add user

        </a>

    </div>



    <!-- ===================================================
         USERS TABLE
    ==================================================== -->

    <div class="table-wrap reveal">


        <!-- TABLE HEADER -->

        <div class="panel-head">

            <div class="panel-title">
                Registered users
            </div>

        </div>


        <div class="table-scroll">

            <table class="app-table">


                <!-- TABLE HEAD -->

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            User
                        </th>

                        <th>
                            Role
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <!-- TABLE BODY -->

                <tbody>

                <?php if ($users_query && mysqli_num_rows($users_query) > 0): ?>


                    <?php while ($user = mysqli_fetch_assoc($users_query)): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?php
                                echo (int) $user['reg_id'];
                                ?>

                            </td>


                            <!-- USER -->

                            <td>

                                <strong>

                                    <?php
                                    echo e(
                                        $user['user_name']
                                    );
                                    ?>

                                </strong>

                            </td>


                            <!-- ROLE -->

                            <td>

                                <?php if ((int) $user['is_admin'] === 1): ?>

                                    Administrator

                                <?php else: ?>

                                    User

                                <?php endif; ?>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <div class="ops">


                                    <!-- CHANGE PASSWORD -->

                                    <a
                                        class="mini-btn"
                                        href="./update_user_pass.php?update_pass_id=<?php echo (int) $user['reg_id']; ?>"
                                    >

                                        Password

                                    </a>


                                    <!-- DELETE -->

                                    <?php
                                    if (
                                        (int) $user['reg_id']
                                        !== current_user_id()
                                    ):
                                    ?>

                                        <form
                                            method="POST"
                                            action="./delete_user.php"
                                            class="delete-form"
                                        >

                                            <input
                                                type="hidden"
                                                name="del_user_id"
                                                value="<?php echo (int) $user['reg_id']; ?>"
                                            >


                                            <button
                                                type="submit"
                                                class="mini-btn danger"
                                            >

                                                Delete

                                            </button>

                                        </form>

                                    <?php endif; ?>


                                </div>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <!-- NO USERS -->

                    <tr>

                        <td
                            colspan="4"
                            style="text-align:center;"
                        >

                            No registered users found.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>


</div>


<?php

/* =========================================================
   FOOTER
   ========================================================= */

mysqli_close($conn);

include('./includes/footer.php');

?>
