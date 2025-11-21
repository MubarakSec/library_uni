<?php
/**
 * Helper functions for API responses
 * Provides consistent JSON response format
 */

/**
 * Send a JSON response
 */
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send an error response
 */
function error_response($message, $status = 400, $errors = []) {
    json_response([
        'success' => false,
        'error' => $message,
        'errors' => $errors
    ], $status);
}

/**
 * Send a success response
 */
function success_response($data, $message = null) {
    $response = ['success' => true, 'data' => $data];
    if ($message) {
        $response['message'] = $message;
    }
    json_response($response);
}

/**
 * Validate required fields
 */
function validate_required($fields, $data) {
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[] = "الحقل {$field} مطلوب";
        }
    }
    return $errors;
}
