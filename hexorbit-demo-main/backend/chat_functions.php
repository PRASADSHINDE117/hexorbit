<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'hex_orbit');

/**
 * Connect to database
 * @return mysqli Database connection
 */
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

/**
 * Save a new message to the database
 * @param int $channelId The channel ID
 * @param int $userId The user ID
 * @param string $content The message content
 * @return array Response with status and message data
 */
function saveMessage($channelId, $userId, $content) {
    $conn = connectDB();
    
    // Sanitize inputs
    $channelId = (int)$channelId;
    $userId = (int)$userId;
    $content = $conn->real_escape_string($content);
    
    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO messages (channel_id, user_id, message_content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $channelId, $userId, $content);
    
    $response = array();
    
    if ($stmt->execute()) {
        $messageId = $stmt->insert_id;
        
        // Get username for the response
        $userQuery = $conn->query("SELECT username FROM users WHERE user_id = $userId");
        $userData = $userQuery->fetch_assoc();
        $username = $userData['username'] ?? 'Unknown User';
        
        // Format timestamp
        $timestamp = date('h:i A');
        
        $response = array(
            'status' => 'success',
            'message' => array(
                'id' => $messageId,
                'userId' => $userId,
                'username' => $username,
                'content' => $content,
                'timestamp' => $timestamp
            )
        );
    } else {
        $response = array(
            'status' => 'error',
            'error' => $stmt->error
        );
    }
    
    $stmt->close();
    $conn->close();
    
    return $response;
}

/**
 * Get messages for a specific channel
 * @param int $channelId The channel ID
 * @param int $limit Optional limit (default 100)
 * @param int $offset Optional offset for pagination
 * @return array List of messages
 */
function getChannelMessages($channelId, $limit = 100, $offset = 0) {
    $conn = connectDB();
    
    // Sanitize inputs
    $channelId = (int)$channelId;
    $limit = (int)$limit;
    $offset = (int)$offset;
    
    $query = "SELECT m.message_id, m.user_id, u.username, m.message_content, 
              DATE_FORMAT(m.created_at, '%h:%i %p') as timestamp 
              FROM messages m 
              JOIN users u ON m.user_id = u.user_id 
              WHERE m.channel_id = $channelId 
              ORDER BY m.created_at 
              LIMIT $offset, $limit";
    
    $result = $conn->query($query);
    $messages = array();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = array(
                'id' => $row['message_id'],
                'userId' => $row['user_id'],
                'username' => $row['username'],
                'content' => $row['message_content'],
                'timestamp' => $row['timestamp']
            );
        }
    }
    
    $conn->close();
    
    return $messages;
}

/**
 * Delete a message
 * @param int $messageId The message ID
 * @param int $userId The user ID (for validation)
 * @return bool True if successful, false otherwise
 */
function deleteMessage($messageId, $userId) {
    $conn = connectDB();
    
    // Sanitize inputs
    $messageId = (int)$messageId;
    $userId = (int)$userId;
    
    // Only allow users to delete their own messages (or add admin check here)
    $stmt = $conn->prepare("DELETE FROM messages WHERE message_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $messageId, $userId);
    
    $success = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $success;
}

/**
 * Edit a message
 * @param int $messageId The message ID
 * @param int $userId The user ID (for validation)
 * @param string $newContent The new message content
 * @return bool True if successful, false otherwise
 */
function editMessage($messageId, $userId, $newContent) {
    $conn = connectDB();
    
    // Sanitize inputs
    $messageId = (int)$messageId;
    $userId = (int)$userId;
    $newContent = $conn->real_escape_string($newContent);
    
    // Only allow users to edit their own messages
    $stmt = $conn->prepare("UPDATE messages SET message_content = ? WHERE message_id = ? AND user_id = ?");
    $stmt->bind_param("sii", $newContent, $messageId, $userId);
    
    $success = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $success;
}
?>
