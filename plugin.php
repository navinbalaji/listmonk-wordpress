<?php
/*
Plugin Name: Listmonk
Description: Connects WPForms to List monk list
Version: 1.0
Author: Navin
*/

// wpforms_process_complete_{wp_form_id} replace with your wp form id
add_action('wpforms_process_complete_26659', 'send_data_to_api', 10, 4);

function send_data_to_api($form_data, $form_id, $fields, $form_attrs) {
        
    // Get the email field value (ID is 1)
    $email = isset($form_data[1]) ? $form_data[1]['value'] : '';

    // Check if the email is empty
    if (empty($email)) {
        return; // Exit the function if no email is found
    }

    // Prepare data for the API
    $api_data = array(
        'email' => $email,
        'status' => 'enabled',
        'lists' => array(3) // Adjust the list ID as needed
    );

    // Prepare Basic Authorization header
    $domain = 'https://domain.com/api/subscribers'; // Replace with your domain
    $username = ''; // Replace with your actual username
    $password = ''; // Replace with your actual password
    $credentials = base64_encode("$username:$password");

    // Send the data to the API using wp_remote_post
    $response = wp_remote_post($domain, array(
        'method'    => 'POST',
        'body'      => json_encode($api_data),
        'headers'   => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $credentials
        ),
    ));

    // Log the API response to a file
    $log_file = WP_CONTENT_DIR . '/api-log.txt'; // Use WP_CONTENT_DIR for portability

    // Check if the directory is writable
    if (is_writable(WP_CONTENT_DIR)) {
        $log_data = 'Email: ' . $email . "\n" . 'API Response: ' . wp_remote_retrieve_body($response) . "\n\n";
        file_put_contents($log_file, $log_data, FILE_APPEND);
    } else {
        error_log('Log directory is not writable.');
    }
}
