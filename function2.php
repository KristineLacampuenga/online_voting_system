<?php

// Function to check if the user is logged in and retrieve user data
function check_login($conn)
{
    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];

        // Check in the admin table
        $query = "SELECT * FROM admin WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $user_data['role'] = 'admin';
            return $user_data;
        }

        // Check in the users table
        $query = "SELECT * FROM users WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $user_data['role'] = 'user';
            return $user_data;
        }
    }

    // Redirect to admin.php if admin is logged in, otherwise to home.php
    if (isset($user_data['role']) && $user_data['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: adminmessage.php");
    }
    die;
}

// Function to generate a random number of specified length
function random_num($length)
{
    $text = "";
    if ($length < 5) {
        $length = 5;
    }

    $len = rand(4, $length);

    for ($i = 0; $i < $len; $i++) {
        $text .= rand(0, 9);
    }

    return $text;
}

?>
