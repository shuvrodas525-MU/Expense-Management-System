<?php

/* =========================================================
   SESSION
   ========================================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =========================================================
   SECURITY / OUTPUT
   ========================================================= */

/**
 * Safely escape output for HTML.
 */
function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/* =========================================================
   MONEY & DATE
   ========================================================= */

/**
 * Format money.
 */
function money($value)
{
    return '৳' . number_format(
        (float) $value,
        2
    );
}


/**
 * Format date.
 */
function nice_date($date)
{
    return date(
        'M d, Y',
        strtotime($date)
    );
}


/* =========================================================
   ALERT / NOTIFICATION
   ========================================================= */

/**
 * Show notification alert.
 */
function my_alert($color, $msg)
{
    $allowed_colors = [
        'success',
        'danger',
        'warning',
        'info'
    ];

    if (!in_array(
        $color,
        $allowed_colors,
        true
    )) {
        $color = 'info';
    }

    echo '
        <div class="toast-alert ' . e($color) . '">
            <i class="bi bi-info-circle-fill"></i>

            <span>' . e($msg) . '</span>

            <button
                type="button"
                onclick="this.parentElement.remove()"
                aria-label="Close"
            >
                ×
            </button>
        </div>
    ';
}


/* =========================================================
   USER / SESSION
   ========================================================= */

/**
 * Get currently logged-in user ID.
 */
function current_user_id()
{
    return isset($_SESSION['user_id'])
        ? (int) $_SESSION['user_id']
        : 0;
}


/**
 * Check whether user is logged in.
 */
function check_user()
{
    if (
        empty($_SESSION['is_login']) ||
        !current_user_id()
    ) {
        header('Location: ./login_user.php');
        exit;
    }
}


/**
 * Check whether logged-in user is admin.
 */
function check_admin()
{
    check_user();

    if (empty($_SESSION['is_admin'])) {
        http_response_code(403);
        exit('Access denied.');
    }
}


/* =========================================================
   REDIRECT
   ========================================================= */

/**
 * Store message and redirect.
 */
function redirect_msg(
    $url,
    $color,
    $msg
) {
    $_SESSION['message'] = $msg;
    $_SESSION['color']   = $color;

    header('Location: ' . $url);
    exit;
}


/* =========================================================
   CSRF PROTECTION
   ========================================================= */

/**
 * Generate / return CSRF token.
 */
function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf'];
}


/**
 * Verify CSRF token.
 */
function csrf_check($token)
{
    return (
        !empty($_SESSION['csrf']) &&
        hash_equals(
            $_SESSION['csrf'],
            (string) $token
        )
    );
}

?>
