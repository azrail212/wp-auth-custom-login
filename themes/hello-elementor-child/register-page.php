<?php
/* Template Name: Custom Register Page */
get_header();
?>

<div class="login-form-container">
    <h2>Registruj se</h2>
    <form method="post">
        <p>
            <label for="username">Korisničko ime</label>
            <input type="text" name="username" id="username" required>
        </p>
        <p>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </p>
        <p>
            <label for="password">Šifra</label>
            <input type="password" name="password" id="password" required>
        </p>
        <p>
            <label for="password_confirm">Potvrdi Šifru</label>
            <input type="password" name="password_confirm" id="password_confirm" required>
        </p>
        <p>
            <input type="submit" name="submit" value="Registruj se">
        </p>
    </form>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center;">
        <p style="color: #666; margin-bottom: 15px;">Ili</p>
        <a href="<?php echo site_url('/google-login'); ?>" style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: 250px; margin: 0 auto; padding: 12px 16px; background-color: #ffffff; color: #3c4043; text-decoration: none; border: 1px solid #dadce0; border-radius: 4px; font-weight: 500; font-size: 15px; transition: background-color 0.2s, box-shadow 0.2s;">
            <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Registruj se sa Google-om
        </a>
    </div>

    <div style="margin-top: 25px; text-align: center;">
        <p style="color: #666; font-size: 14px;">Već imate račun? <a href="<?php echo site_url('/login'); ?>" style="color: #4285F4; text-decoration: none; font-weight: 500;">Prijavite se ovdje</a></p>
    </div>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validation
    $errors = [];

    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (username_exists($username)) {
        $errors[] = 'Username already exists.';
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (email_exists($email)) {
        $errors[] = 'Email already registered.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    } elseif ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Display errors
    if (!empty($errors)) {
        echo '<div style="color: red; margin-top: 15px;">';
        foreach ($errors as $error) {
            echo '<p>✗ ' . $error . '</p>';
        }
        echo '</div>';
    } else {
        // Create user
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            echo '<p style="color:red;">Registration failed. Please try again.</p>';
        } else {
            // Log the user in
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id, true);

            echo '<p style="color:green; margin-top: 15px;">✓ Registration successful! Redirecting...</p>';
            wp_redirect(home_url());
            exit;
        }
    }
}
get_footer();
?>