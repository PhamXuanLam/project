<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class CustomerController extends Controller
{
    public function register(CustomerRequest $request) {
        $params = $request->only([
            'username', 'email',
            'phone', "role"
        ]);

        if(isset($request->password)) {
            $params['password'] = Hash::make($request->password);
        }
        $response = [];

        DB::beginTransaction();
        $account = new Account();
        try {
            $account->fill($params);
            $account->save();
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

    public function update(CustomerRequest $request) {
        $params = $request->only([
            'username', 'email',
            'phone', "role", "avatar", "address",
        ]);
        $account = Auth::guard('account_api')->user();

        $response = [];

        DB::beginTransaction();
        try {
            $account->fill($params);
            $account->save();
            DB::commit();
            $response["status"] = true;
            $response["message"] = "Update successfully!";
            $response["info"] = $account;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "Update failure!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }
}
