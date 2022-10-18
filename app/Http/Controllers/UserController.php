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
                'total' => $data->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function importCsv(Request $request){
        try {
            $this->user->importFile($request);
            return response()->json([
                'success' => true,
                'message' => 'Imported',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
