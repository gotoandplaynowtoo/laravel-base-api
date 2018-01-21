<?php

namespace App\Traits\Responses;
use Illuminate\Support\Collection;

trait BaseCommonResponse
{
    private function successResponse($data, $code) {
        return response()->json($data, $code);
    }
    protected function errorResponse($message, $code) {
        return response()->json([
            'response' => ['error' => $message]
        ], $code);
    }
    protected function showAll(Collection $collection, $code = 200) {
        return $this->successResponse(['response' => $collection], $code);
    }
    protected function showOne($data, $code = 200) {
        return $this->successResponse([
            'response' => ['data' => $data]
        ], $code);
    }
}