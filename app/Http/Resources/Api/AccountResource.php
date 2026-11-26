<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $plan = $this->plan;
        $isUnlimited = $plan?->monthly_build_credits === -1;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at !== null,
            'role' => $this->role,
            'status' => $this->status,
            'avatar' => $this->avatar,
            'plan' => [
                'id' => $plan?->id,
                'name' => $plan?->name ?? 'Free',
                'features' => $plan?->features ?? [],
            ],
            'build_credits' => [
                'available' => $isUnlimited ? 'unlimited' : $this->build_credits,
                'monthly_allocation' => $isUnlimited ? 'unlimited' : ($plan?->monthly_build_credits ?? 0),
                'unlimited' => $isUnlimited,
            ],
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
