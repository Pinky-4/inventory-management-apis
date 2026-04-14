<?php

namespace App\Http\Resources\Api\V1\Category;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    private $pagination;

    public function __construct($resource, $pagination_links = false)
    {
        if ($pagination_links) {
            $this->pagination = [
                'total' => $resource->total(),
                'count' => $resource->count(),
                'per_page' => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'last_page' => $resource->lastPage(),
                'hasMorePages' => $resource->hasMorePages(),
            ];
            $resource = $resource->getCollection();
        }

        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $collection = [
            'category_list' => $this->collection->map(function ($value, $key) {
                return new CategoryResource($value);
            }),
        ];

        if (is_array($this->pagination)) {
            $collection['pagination'] = $this->pagination;
        }

        return  $collection;
    }
}
