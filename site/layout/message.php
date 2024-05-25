<?php
// Lấy giá trị của phần tử có key là success
// Đọc: nếu có phần tử có key success và giá trị khác false
// Những giá trị sau được xem là false: false, 0, '', null
$message = '';
$classType = '';
if (!empty($_SESSION['success'])) {
    $message = $_SESSION['success'];
    // xóa phần tử có key là success ra khỏi array
    unset($_SESSION['success']);
    $classType = 'alert-success';
} else if (!empty($_SESSION['error'])) {
    $message = $_SESSION['error'];
    // xóa phần tử có key là success ra khỏi array
    unset($_SESSION['error']);
    $classType = 'alert-danger';
}
if ($message) :
?>
<!-- .message.alert.alert-success -->
<div class="message text-center alert <?= $classType ?> mt-3"><?= $message ?></div>
<?php endif ?>