<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CustomerController
{
    // Hiển thị thông tin tài khoản
    function show()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        require 'view/customer/show.php';
    }

    function updateInfo()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $customer->setName($_POST['fullname']);
        $customer->setMobile($_POST['mobile']);
        // kiểm tra mật khẩu nếu người dùng có nhu cầu đổi
        $current_password = $_POST['current_password'];
        $new_password = $_POST['password'];
        if ($current_password && $new_password) {
            //người dùng muốn đổi mật khẩu
            // kiểm tra coi chính chủ hok?
            if (!password_verify($current_password, $customer->getPassword())) {
                $_SESSION['error'] = 'Sai mật khẩu';
                header('location: ?c=customer&a=show');
                exit;
            }
            // đã nhập mật khẩu đúng
            // cập nhật mật khẩu mới
            $encode_new_password = password_hash($new_password, PASSWORD_BCRYPT);
            $customer->setPassword($encode_new_password);
        }

        if ($customerRepository->update($customer)) {
            //cập nhật session 
            $_SESSION['name'] = $_POST['fullname'];

            $_SESSION['success'] = 'Đã cập nhật thông tin tài khoản thành công';
            header('location: ?c=customer&a=show');
            exit;
        }
        $_SESSION['error'] = $customerRepository->getError();
        header('location: ?c=customer&a=show');
    }

    // Hiển thị địa chỉ giao hàng mặc định
    function defaultShipping()
    {
        require 'view/customer/defaultShipping.php';
    }

    // Hiển thị danh sách đơn hàng
    function orders()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        $orderRepository = new OrderRepository();
        $orders = $orderRepository->getByCustomerId($customer->getId());
        require 'view/customer/orders.php';
    }

    // Hiển thị chi tiết đơn hàng
    function orderDetail()
    {
        require 'view/customer/orderDetail.php';
    }

    function notExistingEmail()
    {
        $email = $_GET['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        // customer tồn tại thì không đăng ký được nữa, phải báo false
        if ($customer) {
            echo 'false';
            exit;
        }
        echo 'true';
    }

    function register()
    {
        // Kiểm tra google recaptcha đúng không
        $secret = GOOGLE_RECAPTCHA_SECRET;
        $gRecaptchaResponse = $_POST['g-recaptcha-response'];
        $remoteIp = '127.0.0.1';
        $domain = get_host_name();
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $resp = $recaptcha->setExpectedHostname($domain)
            ->verify($gRecaptchaResponse, $remoteIp);
        if (!$resp->isSuccess()) {
            $errors = $resp->getErrorCodes();
            // implode() chuyển array thành chuỗi
            $_SESSION['error'] = implode('<br>', $errors);
            header('location: /');
            exit;
        }
        $data = [];
        $data["name"] = $_POST['fullname'];
        $data["password"] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $data["mobile"] = $_POST['mobile'];
        $data["email"] = $_POST['email'];
        $data["login_by"] = 'form';
        $data["shipping_name"] = $_POST['fullname'];
        $data["shipping_mobile"] = $_POST['mobile'];
        $data["ward_id"] = null;
        $data["is_active"] = 0;
        $data["housenumber_street"] = '';
        $customerRepository = new CustomerRepository();
        if (!$customerRepository->save($data)) {
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /');
            exit;
        }
        $email = $data["email"];
        $_SESSION['success'] = "Đã tạo tài khoản thành công, vui lòng vào email $email để kích hoạt tài khoản";
        // gởi mail kích hoạt tài khoản
        // mã hóa
        $key = JWT_KEY;
        // dữ liệu
        $payload = [
            'email' => $email
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');

        $emailService = new EmailService();
        $to = $email;
        $subject = 'Godashop - Verify your email';
        $activeAccountLink = get_domain_site() . "?c=customer&a=activeAccount&token=$jwt";
        $domain = get_domain();
        $content = "
        Dear $email, <br>
        Vui lòng click vào link bên dưới để kích hoạt tài khoản <br>
        <a href='$activeAccountLink'>Active Account</a> <br>
        Được gởi từ trang web $domain

        ";
        $emailService->send($to, $subject, $content);

        header('location: /');
    }

    function activeAccount()
    {
        // giải mã
        $key = JWT_KEY;
        $jwt = $_GET['token'];
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $email = $decoded->email;
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        // kích hoat account
        $customer->setIsActive(1);
        if (!$customerRepository->update($customer)) {
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /');
            exit;
        }
        $_SESSION['success'] = "Đã kích hoạt tài khoản $email thành công";
        // cho login vào hệ thống
        $_SESSION['name'] = $customer->getName();
        $_SESSION['email'] = $email;
        header('location: ?c=customer&a=show');
    }

    function test1()
    {
        // mã hóa
        $key = 'con gà đi 2 chân';
        // dữ liệu
        $payload = [
            'email' => 'abc@gmail.com'
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');
        echo $jwt;
    }

    function test2()
    {
        // giải mã
        $key = 'con gà đi 2 chân';
        $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImFiY0BnbWFpbC5jb20ifQ.5YWjtvg4Uzyhfe4Mbw0hrN8b0tqkVlUyrhsYVAweK0w';
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        print_r($decoded);
    }
}
