<nav aria-label="Page navigation">
    <ul class="pagination">
        <!-- Tombol First -->
        <li class="page-item <?= $pager->hasPrevious() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPrevious() ? $pager->getFirst() : 'javascript:void(0);' ?>" aria-label="First">First</a>
        </li>

        <!-- Tombol Previous -->
        <li class="page-item <?= $pager->hasPrevious() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPrevious() ? $pager->getPreviousPage() : 'javascript:void(0);' ?>" aria-label="Previous">Previous</a>
        </li>

        <!-- Nomor Halaman -->
        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach; ?>

        <!-- Tombol Next -->
        <li class="page-item <?= $pager->hasNext() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNext() ? $pager->getNextPage() : 'javascript:void(0);' ?>" aria-label="Next">Next</a>
        </li>

        <!-- Tombol Last -->
        <li class="page-item <?= $pager->hasNext() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNext() ? $pager->getLast() : 'javascript:void(0);' ?>" aria-label="Last">Last</a>
        </li>
    </ul>
</nav>
