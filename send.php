<?php
if (isset($_POST['message'])) {
    $msg = strip_tags($_POST['message']);
    $msg = htmlspecialchars($msg);
    $line = date('H:i') . ' | <b>You</b>: ' . $msg . "<br>\n";
    file_put_contents('chatlog.txt', $line, FILE_APPEND);
}
?>
