<?php
// Utility function to return success response
function success($data = [], $message = 'Request successful') {
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Utility function to return error response
function error($message = 'Request failed', $code = 400) {
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit();
}
