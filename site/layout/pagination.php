<!-- Paging -->
<ul class="pagination pull-right">
    <?php if ($page > 1): ?>
    <li><a href="javascript:void(0)" onclick="goToPage(<?=$page - 1?>)">Trang trước</a></li>
    <?php endif ?>

    <?php for ($i = 1; $i <= $totalPage; $i++) : ?>
    <li class="<?= $page == $i ? 'active' : '' ?>"><a href="javascript:void(0)"
            onclick="goToPage(<?= $i ?>)"><?= $i ?></a>
    </li>
    <?php endfor ?>

    <?php if ($page < $totalPage): ?>
    <li><a href="javascript:void(0)" onclick="goToPage(<?=$page + 1?>)">Trang sau</a></li>
    <?php endif ?>
</ul>
<!-- End paging -->