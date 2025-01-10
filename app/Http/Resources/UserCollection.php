<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public bool $preserveKeys = true;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}


