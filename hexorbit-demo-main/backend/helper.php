<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
