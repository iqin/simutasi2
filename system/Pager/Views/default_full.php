<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(1); // Menampilkan hanya 2 tombol angka (halaman saat ini + 1 sebelum & 1 sesudah)
?>

<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="pagination">
        <!-- Tombol First (<<) -->
        <li class="page-item <?= $pager->hasPrevious() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPrevious() ? $pager->getFirst() : 'javascript:void(0);' ?>" aria-label="<?= lang('Pager.first') ?>">
                <span aria-hidden="true">&laquo;</span> <!-- << -->
            </a>
        </li>

        <!-- Tombol Previous (<) -->
        <li class="page-item <?= $pager->hasPrevious() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPrevious() ? $pager->getPrevious() : 'javascript:void(0);' ?>" aria-label="<?= lang('Pager.previous') ?>">
                <span aria-hidden="true">&lsaquo;</span> <!-- < -->
            </a>
        </li>

        <!-- Nomor Halaman (Hanya 2 Angka yang Ditampilkan) -->
        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <!-- Tombol Next (>) -->
        <li class="page-item <?= $pager->hasNext() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNext() ? $pager->getNext() : 'javascript:void(0);' ?>" aria-label="<?= lang('Pager.next') ?>">
                <span aria-hidden="true">&rsaquo;</span> <!-- > -->
            </a>
        </li>

        <!-- Tombol Last (>>) -->
        <li class="page-item <?= $pager->hasNext() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNext() ? $pager->getLast() : 'javascript:void(0);' ?>" aria-label="<?= lang('Pager.last') ?>">
                <span aria-hidden="true">&raquo;</span> <!-- >> -->
            </a>
        </li>
    </ul>
</nav>
