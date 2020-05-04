<?php

namespace App\Model;

use App\Core\Model;

class Page extends Model
{
    public int $id;
    public ?Page $friendly;
    public string $title;
    public string $description;

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        $page = [
            "id" => $this->id,
            "friendly" => null,
            "title" => $this->title,
            "description" => $this->description
        ];

        if ($this->friendly) {
            $page["friendly"] = $this->friendly->id;
        }

        return $page;
    }
}
