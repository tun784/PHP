<html lang="en">
<head>
    <title>Home</title>
</head>
<body>
    <?php
        session_start();
        $tendn = $_SESSION["user"];
        if (empty($tendn))
            header("Location: login.php");
        else
            echo "<h1>Hello: $tendn</h1>";
    ?>
</body>
</html>