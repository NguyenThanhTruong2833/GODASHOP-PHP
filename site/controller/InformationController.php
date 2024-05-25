<?php
class InformationController
{
    // chính sách thanh toán
    function paymentPolicy()
    {
        require 'view/information/paymentPolicy.php';
    }

    // chính sách đổi trả
    function returnPolicy()
    {
        require 'view/information/returnPolicy.php';
    }

    // chính sách giao hàng
    function deliveryPolicy()
    {
        require 'view/information/deliveryPolicy.php';
    }
}
