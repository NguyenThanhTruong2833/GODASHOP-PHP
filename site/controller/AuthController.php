<?php
class AuthController
{
    function login()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        // kiểm tra email có tồn tại trong hệ thống không
        if (!$customer) {
            $_SESSION['error'] = "Lỗi: Email $email không tồn tại trong hệ thống";
            // về trang chủ
            header('location: /');
            exit;
        }
        // kiểm tra mật khẩu đúng không
        // hàm này trả về true nếu mật khẩu nhập và mật khẩu đã mã hóa là 1
        if (!password_verify($password, $customer->getPassword())) {
            $_SESSION['error'] = "Lỗi: Mật khẩu không đúng";
            // về trang chủ
            header('location: /');
            exit;
        }

        // Kiểm tra tài khoản đã được kích hoạt chưa?
        if ($customer->getIsActive() == 0) {
            $_SESSION['error'] = "Lỗi: Tài khoản $email chưa được kích hoạt";
            // về trang chủ
            header('location: /');
            exit;
        }

        $_SESSION['email'] = $email;
        $_SESSION['name'] = $customer->getName();
        // về trang thông tin tài khoản (later)
        header('location: /');
    }

    function logout()
    {
        // hủy session
        session_destroy();
        header('location: /');
    }
}