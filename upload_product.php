<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "product_db");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $image = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));

    $sql = "INSERT INTO products (name, description, price, image) 
            VALUES ('$name', '$description', '$price', '$image')";

    if ($conn->query($sql) === TRUE) {
        echo "Thêm sản phẩm thành công!";
    } else {
        echo "Lỗi: " . $conn->error;
    }
    $conn->close();
}
?>

<form method="POST" enctype="multipart/form-data">
    Tên sản phẩm: <input type="text" name="name"><br>
    Mô tả: <textarea name="description"></textarea><br>
    Giá: <input type="text" name="price"><br>
    Ảnh: <input type="file" name="image"><br>
    <button type="submit">Thêm sản phẩm</button>
</form>
