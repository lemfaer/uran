<?php

namespace App\Repositories;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;
use App\Models\Category;

class ProductRepository
{
    /**
     * Select multiple products (in category)
     *
     * @param string|null $category name
     * @param int $limit limit to load
     * @param int $offset offset count
     *
     * @throws \InvalidArgumentException category not exists
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProducts(?string $category, int $limit, int $offset): Collection
    {
        return $this
            ->productQuery($category)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * Get products count (in category)
     *
     * @param string|null $category name
     *
     * @throws \InvalidArgumentException category not exists
     *
     * @return int
     */
    public function getProductCount(?string $category): int
    {
        return $this
            ->productQuery($category)
            ->count();
    }

    protected function productQuery(?string $name): Builder
    {
        if (isset($name)) {
            $category = Category::query()
                ->where("name", $name)
                ->first();

            if (empty($category)) {
                throw new InvalidArgumentException("Category $name not found");
            }

            $query = $category
                ->products()
                ->getQuery();
        } else {
            $query = Product::query();
        }

        return $query;
    }
}
