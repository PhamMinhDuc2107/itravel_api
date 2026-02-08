<?php

namespace App\Http\Resources\Admin\BankAccount;

use App\Http\Resources\Base\BaseResource;

class BankAccountResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'account_holder' => $this->account_holder,
            'branch' => $this->branch,
            'logo' => $this->logo_url,
            'qr_code' => $this->qr_code_url,
            'position' => $this->position,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

