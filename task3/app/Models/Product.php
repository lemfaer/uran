<?php

namespace App\Models;

use DOMXpath;
use DOMDocument;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const PREVIEW_LENGTH = 100;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "product";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * Xpath for querying content
     *
     * @var DOMXpath|null
     */
    protected ?DOMXpath $xpath = null;

    /**
     * Get the product category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Returns a preview image src, null if no image
     *
     * @return string|null
     */
    public function getImageAttribute(): ?string
    {
        $xpath = $this->getContentXPath();

        $iquery = $xpath->query("//img");

        if ($iquery->length) {
            $img = $iquery->item(0)->getAttribute("src");
        } else {
            $img = null;
        }

        return $img;
    }

    /**
     * Extracts description info from article content
     *
     * @return string
     */
    public function getDescriptionAttribute(): string
    {
        $xpath = $this->getContentXPath();

        $pquery = $xpath->query("//p");

        $length = static::PREVIEW_LENGTH;

        if ($pquery->length) {
            $desc = $pquery->item(0)->nodeValue;
            $suff = mb_strlen($desc) > $length ? "..." : "";
            $desc = mb_substr($desc, 0, $length) . $suff;
        } else {
            $desc = '';
        }

        return $desc;
    }

    protected function getContentXPath(): DOMXpath
    {
        if (isset($this->xpath)) {
            return $this->xpath;
        }

        // clear errors from non-well formatted/broken HTML
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($this->content);

        $this->xpath = new DOMXpath($dom);

        return $this->xpath;
    }
}
