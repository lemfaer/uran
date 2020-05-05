<?php

namespace App\Http\Controllers;

use Auth;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repositories\ProductRepository;
use App\Models\Category;
use App\Models\Product;

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

    /**
     * Renders page with product edit form
     *
     * @param int|null $id product id to edit
     */
    public function edit(?int $id = null)
    {
        if (!Auth::check()) {
            throw new NotFoundHttpException("Not authenticated");
        }

        try {
            $entity = $id ? $this->repository->getProductById($id) : new Product;
        } catch (Throwable $e) {
            throw new NotFoundHttpException("Product not found");
        }

        $categories = Category::all();

        return view("product/form", compact("entity", "categories"));
    }

    /**
     * Performs actions to create/edit article
     *
     * string <title> title
     * int <category> category id
     * string <content> content in html format
     * string <tags> string of tags, separated by `,`
     * string <csrf_token> token
     *
     * @param int|null $id article id to edit
     */
    public function edit_submit(Request $request, int $id = null)
    {
        if (!Auth::check()) {
            throw new NotFoundHttpException("Not authenticated");
        }

        try {
            if ($id) {
                $entity = $this->repository->getProductById($id);
            } else {
                $entity = new Product();
            }
        } catch (Throwable $e) {
            throw new NotFoundHttpException("Product not found");
        }

        $entity->title = $request->get("title");
        $entity->content = $request->get("content");
        $entity->category_id = $request->get("category");

        $entity->save();

        return redirect()
            ->route(
                "product_view",
                ["id" => $entity->id]
            );
    }
}
