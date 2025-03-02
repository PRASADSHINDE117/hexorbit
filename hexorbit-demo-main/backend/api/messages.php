<?php
// API endpoint to handle message operations
require_once 'chat_functions.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Process based on request type
switch ($method) {
    case 'GET':
        // Get messages for a channel
        if (isset($_GET['channel_id'])) {
            $channelId = (int)$_GET['channel_id'];
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            
            $messages = getChannelMessages($channelId, $limit, $offset);
            echo json_encode(['status' => 'success', 'messages' => $messages]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Channel ID is required']);
        }
        break;
        
    case 'POST':
        // Save a new message
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['channel_id']) && isset($data['user_id']) && isset($data['content'])) {
            $response = saveMessage($data['channel_id'], $data['user_id'], $data['content']);
            echo json_encode($response);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        }
        break;
        
    case 'PUT':
        // Edit a message
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['message_id']) && isset($data['user_id']) && isset($data['content'])) {
            $success = editMessage($data['message_id'], $data['user_id'], $data['content']);
            
            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'Message updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update message']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        }
        break;
        
    case 'DELETE':
        // Delete a message
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['message_id']) && isset($data['user_id'])) {
            $success = deleteMessage($data['message_id'], $data['user_id']);
            
            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete message']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        }
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>
