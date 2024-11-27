<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function register(UserRequest $request, string $role = null) {
        $params_account = $request->only([
            'username', 'email', 'avatar',
            'phone', 'address'
        ]);

        $params_user = $request->only([
            'description', 'unit_name',
            'tax_code'
        ]);

        if(isset($request->password)) {
            $params_account['password'] = Hash::make($request->password);
        }
        $response = [];

        DB::beginTransaction();
        $account = new Account();
        try {
            $account->fill($params_account);
            if(!$role) {
                $role = "seller";
            }
            $account->role = $role;
            $account->save();
            $user = new User();
            $user->account_id = $account->id;
            $user->fill($params_user);
            $user->position = $account->role;
            $user->save();
            DB::commit();
            $response["status"] = true;
            $response["message"] = "Register successfully!";
            $response["info"] = $account;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "Register failure!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function update(UserRequest $request, string $user_id) {
        
    }
}
