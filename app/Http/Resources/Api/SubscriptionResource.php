<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'plan' => [
                'id' => $this->plan?->id,
                'name' => $this->plan?->name,
                'price' => $this->plan?->price,
                'currency' => $this->plan?->currency ?? 'USD',
                'billing_period' => $this->plan?->billing_period ?? 'monthly',
                'features' => $this->plan?->features ?? [],
            ],
            'renewal_at' => $this->renewal_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
