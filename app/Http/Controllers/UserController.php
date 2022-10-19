<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $user;
    public function __construct(UserService $user){
        $this->user = $user;
    }

    public function index(){
        try {
            $data = $this->user->user()->get();
            return response()->json([
                'success' => true,
                'users' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function importCsv(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->user->importFile($request);
            return response()->json([
                'success' => $data['success'],
                'output' => $data['output'],
                'message' => $data['message'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
