<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserAddressRequest;
use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    //创建收货地址
    public function store(UserAddressRequest $request,UserAddress $userAddress)
    {
        $userAddress->fill($request->all());
        $userAddress->user_id = $request->user()->id;
        $userAddress->save();
        $data['message'] = "Address Created OK!";
        return response()->json($data, 200);
    }

    //编辑收货地址
    public function update(Request $request,UserAddress $userAddress)
    {
        $user_address = UserAddress::findOrFail($request->id);
        $user = $request->user();
        if($user->id != $user_address->user_id){
            $data['message'] = "This action is unauthorized."; // 验证权限
            return response()->json($data, 500);
        }
        $attributes = $request->only(['contact_name', 'contact_phone','province','city','district','street','address']);// 允许更新的字段
        $user_address->update($attributes);
        $data['message'] = "Address Updated OK!";
        return response()->json($data, 200);
    }

    //删除收货地址
    public function destroy(string $Id,Request $request)
    {
        $user_address = UserAddress::findOrFail($Id);
        $user = $request->user();
        if($user->id != $user_address->user_id){
            $data['message'] = "This action is unauthorized."; // 验证权限
            return response()->json($data, 500);
        }
        $user_address->delete();
        $data['message'] = "Address Deleted OK!";
        return response()->json($data, 200);
    }

    //收货地址列表
    public function index(Request $request)
    {
        $user = $request->user();
        $user_address = UserAddress::where('user_id','=',$user->id)
            ->orderBy('created_at','desc')
            ->get();
        return $user_address;
    }

}
