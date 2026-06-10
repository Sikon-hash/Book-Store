<?php
if ($_SERVER['REQUEST_URI'] === '/test' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($input);
    return;
}

return false;
