<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class JsonExeption extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'errors' => explode("&&", $this->getMessage())
        ], 422);
    }
}
