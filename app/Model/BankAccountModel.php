<?php

namespace App\Model;

use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BankAccountModel extends Model
{
    use HasFactory;

    protected $table = 'bank_accounts';

    public array $searchable = ['bank_name', 'account_number', 'account_holder'];
    public array $sortable = ['id', 'created_at', 'bank_name', 'position', 'status'];

    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder',
        'branch',
        'logo',
        'qr_code',
        'position',
        'status',
    ];

    protected $casts = [
        'position' => 'integer',
        'status' => 'integer',
    ];

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->attributes['logo'] ?? null;
                if (!$path) {
                    return null;
                }
                $diskManager = $this->diskManager();
                return $diskManager->url($path);
            }
        );
    }

    protected function qrCodeUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->attributes['qr_code'] ?? null;
                if (!$path) {
                    return null;
                }
                $diskManager = $this->diskManager();
                return $diskManager->url($path);
            }
        );
    }

    private function diskManager(): DiskManager
    {
        return app(DiskManager::class);
    }
}

