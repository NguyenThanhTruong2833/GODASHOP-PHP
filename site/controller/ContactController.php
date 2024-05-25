<?php
class ContactController
{
    // hiển thị trang liên hệ
    function form()
    {
        require 'view/contact/form.php';
    }

    // gởi mail đến chủ cửa hàng
    function sendEmail()
    {
        $fullname = $_POST['fullname'];
        $mobile = $_POST['mobile'];
        $email = $_POST['email'];
        $message = $_POST['content'];
        $website = get_domain();//từ file bootstrap.php
        $to = SHOP_OWNER;
        $subject = APP_NAME . ' - liên hệ';
        $content = "
        Xin chào chủ cửa hàng, <br>
        Dưới đây là thông tin khác hàng liên hệ, <br>
        Tên: $fullname,<br>
        Mobile: $mobile, <br>
        Email: $email, <br>
        Nội dung: $message<br>
        ------------------<br>
        Được gởi từ trang web: $website
        ";
        $emailService = new EmailService();
        $emailService->send($to, $subject, $content);
        echo 'Đã gởi mail thành công';
    }
}