<?php
session_start();
$message = isset($_SESSION["message"]) ? $_SESSION["message"] :"";
unset($_SESSION["message"]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
</head>
<body>
    <h2>Đăng nhập</h2>
    <div class="container">
        <?php if ($message) : ?>
            <p class="message">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>
        <form action="xuly.php" method="post">
            <table>
                <tr>
                    <td>
                        <label for="username">Username</label>
                    </td>
                    <td>
                        <input type="text" name="username" id="username" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="password">Password</label>
                    </td>
                    <td>
                        <input type="password" name="password" id="password" required>
                    </td>
                </tr>
                <tr>
                    <td colsoan="2" style="text-align: center;">
                        <button type="submit">Login</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>