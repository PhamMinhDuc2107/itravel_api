<?php

namespace App\Repository\Eloquent;

use App\Model\BankAccountModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\BankAccountRepositoryInterface;

class BankAccountRepository extends BaseRepository implements BankAccountRepositoryInterface
{
    public function model(): string
    {
        return BankAccountModel::class;
    }
}

