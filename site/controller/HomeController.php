<?php
class HomeController
{
    public function index()
    {
        $page = 1;
        $item_per_page = 4;
        $conds = [];
        $productRepository = new ProductRepository();
        // lấy 4 sản phẩm nổi bật
        $sorts = ['featured' => 'DESC'];
        $featuredProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
        // SELECT * FROM view_product ORDER BY featured DESC LIMIT 0, 4

        // lấy 4 sản phẩm mới nhất
        $sorts = ['created_date' => 'DESC'];
        $latestProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
        // SELECT * FROM view_product ORDER BY created_date DESC LIMIT 0, 4

        // Lấy tất cả danh mục
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        // Duyệt từng category để tạo cấu trúc dữ liệu gởi qua view
        $categoryProducts = []; //khai báo danh sách rỗng
        foreach ($categories as $category) {
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category->getId(), //2
                ],
            ];
            $products = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
            // SELECT * FROM view_product WHERE category_id=2 LIMIT 0, 4

            // Dấu ngoặc vuông bên trái là thêm 1 phần tử vào cuối danh sách
            // tương tự hàm array_push($cateogryProducts, $element)
            $categoryProducts[] = [
                'categoryName' => $category->getName(),
                'products' => $products,
            ];
        }

        require 'view/home/index.php';
    }
}