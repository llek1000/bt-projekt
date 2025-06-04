<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class Controller
{
    /**
     * Success response helper
     */
    protected function success($data = [], $message = null, $code = 200)
    {
        $response = ['success' => true];

        if ($message) $response['message'] = $message;
        $response = array_merge($response, $data);

        return response()->json($response, $code);
    }

    /**
     * Error response helper
     */
    protected function error($message, $details = null, $code = 500)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($details) {
            if (is_array($details)) {
                $response['errors'] = $details;
            } else {
                $response['error'] = $details;
            }
        }

        return response()->json($response, $code);
    }

    /**
     * Validate request with custom messages
     */
    protected function validateRequest(Request $request, array $rules, array $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return null;
    }

    /**
     * Execute database transaction with error handling
     */
    protected function executeWithTransaction(callable $callback, $errorMessage = 'Operácia zlyhala')
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->error('Neplatné údaje', $e->errors(), 422);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->error('Záznam nebol nájdený', null, 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($errorMessage, [
                'error' => $e->getMessage(),
                'ip' => request()->ip()
            ]);
            return $this->error($errorMessage, $e->getMessage());
        }
    }

    /**
     * Log and handle exceptions
     */
    protected function handleException(\Exception $e, $context, $message, $code = 500)
    {
        Log::error($context, [
            'error' => $e->getMessage(),
            'ip' => request()->ip()
        ]);

        return $this->error($message, $e->getMessage(), $code);
    }

    /**
     * Check if user has specific role
     */
    protected function hasRole($user, $roleName)
    {
        return $user->roles()->where('name', $roleName)->exists();
    }
}
