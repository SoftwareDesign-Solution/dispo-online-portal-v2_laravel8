<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        /*
        $collection = $this->collection;
        if (method_exists($collection->map, 'toArray')) {
            return $collection->map->toArray($request)->all();
        } else {
            // for stdClass
            return json_decode(json_encode($collection, true), true);
        }
        */

        return parent::toArray($request);

    }
}
