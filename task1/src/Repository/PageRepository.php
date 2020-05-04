<?php

namespace App\Repository;

use PDO;
use PDOStatement;
use Exception;
use App\Core\Repository;
use App\Model\Page;

use function sprintf;
use function array_column;

class PageRepository extends Repository
{
    /**
     * List of known pages
     *
     * @var array of \App\Model\Page
     */
    protected $pagePool = [];

    /**
     * Get page from db by its id
     *
     * @param int $id
     *
     * @return \App\Model\Page
     */
    public function getPageById(int $id): Page
    {
        $pages = $this->getPages([$id], 1);

        if (!isset($pages[$id])) {
            throw new Exception("Page not found", 404);
        }

        return $pages[$id];
    }

    /**
     * Get multiple pages
     *
     * @param array|null $ids page ids
     * @param int $limit
     * @param int $offset
     *
     * @return array of \App\Model\Page
     */
    public function getPages(?array $ids = null, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT * FROM pages %s LIMIT %d OFFSET %d";
        $parts = ['', $limit, $offset];

        if ($ids !== null) {
            if ($ids) {
                $parts[0] = sprintf("WHERE id IN (%s)", implode(',', $ids));
            } else {
                return [];
            }
        }

        $query = sprintf($sql, ...$parts);
        $statm = $this->db->prepare($query);
        $statm->execute();

        $pages = $this->buildPages($statm);
        $ids = array_column($pages, "id");
        $friendly = $this->getFriendlyPages($ids, $limit);

        return $pages;
    }

    /**
     * Get friendly of pages
     *
     * @param array $ids original page ids
     * @param int $limit
     *
     * @return array
     */
    public function getFriendlyPages(array $ids, int $limit = 10): array
    {
        if (empty($ids)) {
            return [];
        }

        $query = sprintf(
            <<<SQL
                SELECT f.* FROM pages AS f
                INNER JOIN pages AS p
                    ON f.id = p.friendly
                WHERE p.id IN (%s)
                LIMIT %d
            SQL,
            implode(',', $ids),
            $limit
        );

        $statm = $this->db->prepare($query);
        $statm->execute();

        return $this->buildPages($statm);
    }

    /**
     * Create Page objects based on db result
     *
     * @param PDOStatement $result
     *
     * @return array of \App\Model\Page
     */
    protected function buildPages(PDOStatement $result): array
    {
        $pages = [];

        foreach ($result as $data) {
            $id = $data["id"];
            $fid = $data["friendly"];
            $page =& $this->pagePool[$id];

            if (!isset($page)) {
                $page = new Page();
            }

            $page->id = $id;
            $page->title = $data["title"];
            $page->description = (string) $data["description"];
            $page->friendly =& $this->pagePool[$fid];

            $pages[$id] = $page;
        }

        return $pages;
    }

    /**
     * Remove all known objects
     */
    public function freePool(): void
    {
        $this->pagePool = [];
    }
}
