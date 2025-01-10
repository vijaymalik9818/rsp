<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PropertyCollection extends ResourceCollection
{
    public bool $preserveKeys = true;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}


