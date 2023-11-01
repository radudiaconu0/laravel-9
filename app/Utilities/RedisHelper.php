<?php

namespace App\Utilities;

use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Redis;

class RedisHelper implements RedisHelperInterface
{
    protected $prefix = 'email:';

    public function storeRecentMessage(mixed $id, string $messageSubject, string $toEmailAddress): void
    {
        $key = $this->prefix . $id;
        $data = [
            'subject' => $messageSubject,
            'to' => $toEmailAddress,
        ];

        Redis::set($key, json_encode($data));
    }
}
