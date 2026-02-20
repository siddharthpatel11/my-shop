<?php

use Illuminate\Support\Facades\DB;

$tokens = DB::table('personal_access_tokens')->get();
foreach ($tokens as $token) {
    echo "ID: {$token->id}, Tokenable ID: {$token->tokenable_id}, Name: {$token->name}\n";
}
