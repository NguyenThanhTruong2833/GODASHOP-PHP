<?php
class ProductController
{
    // hiển thị danh sách sản phẩm đang bán
    function index()
    {
        $conds = [];
        $sorts = [];
        $page = $_GET['page'] ?? 1;
        $item_per_page = 10; //10 sản phẩm mỗi trang
        $productRepository = new ProductRepository();
        // Tìm sản phẩm theo danh mục
        // ?category_id=3
        $category_id = $_GET['category_id'] ?? null;
        if ($category_id) {
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category_id
                ]
            ];
            // SELECT * FROM view_product WHERE category_id=3
        }
        // tìm kiếm theo khoảng giá
        // ?price-range=300000-500000
        $priceRange = $_GET['price-range'] ?? null;
        if ($priceRange) {
            $temp = explode('-', $priceRange);
            $start_price = $temp[0];
            $end_price = $temp[1];
            $conds = [
                'sale_price' => [
                    'type' => 'BETWEEN',
                    'val' => "$start_price AND $end_price"
                ]
            ];
            // SELECT * FROM view_product WHERE sale_price BETWEEN 300000 AND 500000
            if ($end_price == 'greater') {
                $conds = [
                    'sale_price' => [
                        'type' => '>=',
                        'val' => $start_price
                    ]
                ];
                // SELECT * FROM view_product WHERE sale_price >= 1000000
            }
        }

        // sort=price-desc
        $sort = $_GET['sort'] ?? null;
        if ($sort) {
            $temp = explode('-', $sort);
            $dummyCol = $temp[0]; //price
            $order = $temp[1]; //desc
            $order = strtoupper($order); //DESC
            $map = ['price' => 'sale_price', 'alpha' => 'name', 'created' => 'created_date'];
            $colName = $map[$dummyCol];

            $sorts = [$colName => $order];
            // SELECT * FROM view_product ORDER BY sale_price DESC
        }

        $products = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        $totalPage = 3; //xử lý sau
        require 'view/product/index.php';
    }

    function detail()
    {
        $id = $_GET['id'];
        $productRepository = new ProductRepository();
        $product = $productRepository->find($id);
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();
        $category_id = $product->getCategoryId();
        // sản phẩm có liên quan là sản phẩm có cùng danh mục
        $conds = [
            'category_id' => [
                'type' => '=',
                'val' => $category_id //3
            ]
        ];

        // SELECT * FROM view_product WHERE category_id=3
        $relatedProducts = $productRepository->getBy($conds);
        require 'view/product/detail.php';
    }
}
