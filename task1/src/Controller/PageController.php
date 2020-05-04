<?php

namespace App\Controller;

use App\Core\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\PageRepository;

class PageController extends Controller
{
    /**
     * @example /
     */
    public function list(): Response
    {
        $pages = $this
            ->container
            ->get(PageRepository::class)
            ->getPages();

        return $this->jsonResponse(200, $pages);
    }

    /**
     * @example /get/{id}
     */
    public function single(string $id): Response
    {
        $page = $this
            ->container
            ->get(PageRepository::class)
            ->getPageById($id);

        return $this->jsonResponse(200, (array) $page);
    }
}
