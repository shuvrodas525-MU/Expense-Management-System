<?php

include('./includes/functions.php');


/* =========================
   Logged-in User Check
   ========================= */

if (!empty($_SESSION['is_login'])) {

    header('Location: ./index.php');
    exit;

}


include('./includes/header.php');

?>


<div class="welcome">


    <!-- =========================
         Welcome Card
         ========================= -->

    <div class="welcome-card reveal visible">


        <!-- Brand -->

        <div class="eyebrow">
            ExpenseFlow
        </div>


        <!-- Heading -->

        <h1>
            Own your <span>daily spending.</span>
        </h1>


        <!-- Description -->

        <p>
            A focused expense tracker for recording what you spend,
            seeing the real totals, and understanding your money
            without the clutter.
        </p>


        <!-- Actions -->

        <div class="welcome-actions">


            <!-- Login -->

            <a
                class="btn btn-neon"
                href="./login_user.php"
            >
                <i class="bi bi-arrow-right"></i>
                Get started
            </a>


            <!-- Register -->

            <a
                class="btn btn-ghost"
                href="./register_user.php"
            >
                Create account
            </a>

        </div>

    </div>

</div>


<?php

include('./includes/footer.php');

?>