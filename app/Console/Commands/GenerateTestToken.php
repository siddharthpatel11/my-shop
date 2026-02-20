<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;

class GenerateTestToken extends Command
{
    protected $signature = 'generate:test-token';
    protected $description = 'Generate a token for the first customer';

    public function handle()
    {
        $customer = Customer::first();
        if (!$customer) {
            $this->error('No customer found');
            return;
        }
        $token = $customer->createToken('test-token')->plainTextToken;
        $this->info($token);
    }
}
