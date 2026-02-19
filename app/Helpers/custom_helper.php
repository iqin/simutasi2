<?php

// app/Helpers/custom_helper.php

function custom_pagination($baseUrl, $total, $perPage, $currentPage, $queryKey)
{
    $totalPages = ceil($total / $perPage);
    $pagination = '<nav><ul class="pagination justify-content-center">';

    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $currentPage) ? 'active' : '';
        $pagination .= '<li class="page-item ' . $active . '">';
        $pagination .= '<a class="page-link" href="' . $baseUrl . '?' . $queryKey . '=' . $i . '">' . $i . '</a>';
        $pagination .= '</li>';
    }

    $pagination .= '</ul></nav>';
    return $pagination;
}
