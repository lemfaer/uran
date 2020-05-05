<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repositories\ProductRepository;

class MainController extends Controller
{
    public const PER_PAGE = 6;
    public const VIEW_PAGES = 5;

    /**
     * @var \App\Repositories\ProductRepository
     */
    protected ProductRepository $repository;

    /**
     * Init
     *
     * @param \App\Repositories\ProductRepository $repository
     */
    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Renders product list page
     *
     * @param int $page for pagination, >0
     */
    public function list(int $page = 1)
    {
        return $this->category(null, $page);
    }

    /**
     * Renders products from categorys
     *
     * @param string $category name
     * @param int $page for pagination, >0
     */
    public function category(?string $category, int $page = 1)
    {
        $limit = static::PER_PAGE;
        $plimit = static::VIEW_PAGES;
        $offset = ($page-1) * $limit;

        try {
            $entities = $this->repository->getProducts($category, $limit, $offset);
        } catch (Throwable $e) {
            throw $e;
            
            throw new NotFoundHttpException("Error Processing Request", $e);
        }

        if (!count($entities)) {
            throw new NotFoundHttpException("Products not found");
        }

        $count = $this->repository->getProductCount($category);
        $pcount = ceil($count / $limit);
        $pcount = $pcount > $plimit ? $plimit : $pcount;

        return view("list", compact(
            "category",
            "entities",
            "page",
            "pcount"
        ));
    }
}
