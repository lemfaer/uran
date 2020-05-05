<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repositories\ProductRepository;

class ProductController extends Controller
{
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
     * Renders product content by id
     *
     * @param int $id product id
     */
    public function view(int $id)
    {
        try {
            $entity = $this->repository->getProductById($id);
        } catch (Throwable $e) {
            throw new NotFoundHttpException("Product not found");
        }

        return view("product/view", compact("entity"));
    }
}
