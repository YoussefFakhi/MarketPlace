<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'service' => [
                'id' => $this->service?->id,
                'title' => $this->service?->title,
                'description' => $this->service?->description,
                'price' => $this->service?->price,
            ],
            'client' => [
                'id' => $this->client?->id,
                'name' => $this->client?->name,
                'email' => $this->client?->email,
            ],
            'freelancer' => [
                'id' => $this->freelancer?->id,
                'name' => $this->freelancer?->name,
                'email' => $this->freelancer?->email,
            ],
            'status' => $this->status,
            'montant' => $this->montant,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
