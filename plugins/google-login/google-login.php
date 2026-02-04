<?php
/**
 * Plugin Name: Google Login
 * Description: Plugin to create a custom login screen with Google Login
 * Version: 4.2
 * Author: BalkanGameHub
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init', function () {
    add_rewrite_rule('^google-login/?$', 'index.php?google_login=1', 'top');
    add_rewrite_rule('^google-auth-callback/?$', 'index.php?google_callback=1', 'top');
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'google_login';
    $vars[] = 'google_callback';
    return $vars;
});

add_action('template_redirect', function () {

    if (get_query_var('google_login')) {

        $client_id = 'CLIENT_ID';
        $redirect_uri = site_url('/google-auth-callback');
        $state = wp_create_nonce('google_login');

        $url = add_query_arg([
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'prompt'        => 'select_account',
        ], 'https://accounts.google.com/o/oauth2/v2/auth');

        wp_redirect($url);
        exit;
    }

});

add_action('template_redirect', function () {

    if (!get_query_var('google_callback')) {
        return;
    }

    if (!isset($_GET['code'], $_GET['state']) || !wp_verify_nonce($_GET['state'], 'google_login')) {
        wp_die('Invalid Google login attempt');
    }

    $token_response = wp_remote_post('https://oauth2.googleapis.com/token', [
        'body' => [
            'code'          => sanitize_text_field($_GET['code']),
            'client_id'     => 'CLIENT_ID',
            'client_secret' => 'CLIENT_SECRET',
            'redirect_uri'  => site_url('/google-auth-callback'),
            'grant_type'    => 'authorization_code',
        ]
    ]);

    $token_data = json_decode(wp_remote_retrieve_body($token_response), true);

    if (empty($token_data['id_token'])) {
        wp_die('Google authentication failed');
    }

    $userinfo = wp_remote_get('https://www.googleapis.com/oauth2/v3/userinfo', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token_data['access_token']
        ]
    ]);

    $profile = json_decode(wp_remote_retrieve_body($userinfo), true);

    if (empty($profile['email'])) {
        wp_die('No email returned from Google');
    }

    handle_google_user_login($profile);
});

function handle_google_user_login($profile) {

    $email = sanitize_email($profile['email']);

    $user = get_user_by('email', $email);

    if (!$user) {

        $username = sanitize_user(str_replace('@', '_', $email), true);

        $user_id = wp_create_user(
            $username,
            wp_generate_password(),
            $email
        );

        if (is_wp_error($user_id)) {
            wp_die('User creation failed');
        }

        update_user_meta($user_id, 'google_id', sanitize_text_field($profile['sub']));

        $user = get_user_by('id', $user_id);
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    wp_redirect(home_url());
    exit;
}

add_action('login_form_login', function () {

    // Allow actual login submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return;
    }

    // Redirect humans away from wp-login.php UI
    wp_safe_redirect(site_url('/login'));
    exit;
});
