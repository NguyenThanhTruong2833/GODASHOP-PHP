<?php
// Create connection
// database server là mysql/sqlserver/oracle/mongodb...
// mỗi database server sẽ có nhiều database (study_k86)
//kết nối
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
// Những giá trị được xem là false: false, null, '', 0
// Ngược lại được xem là true
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
mysqli_set_charset($conn, "utf8");