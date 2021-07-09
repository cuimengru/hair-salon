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
        $data['message'] = "添加地址成功";
        return response()->json($data, 200);
    }

    //编辑收货地址
    public function update(Request $request,UserAddress $userAddress)
    {
        $user_address = UserAddress::findOrFail($request->id);
        $user = $request->user();

        if($user->id != $user_address->user_id){
            $data['message'] = "此操作是未经授权的."; // 验证权限
            return response()->json($data, 500);
        }
        $attributes = $request->only(['contact_name', 'contact_phone','province','city','district','street','address','status']);// 允许更新的字段
        $user_address->update($attributes);
        $data['message'] = "地址修改成功!";
        $address_count = UserAddress::where('user_id','=',$user->id)->where('status','=',1)->count();
        if($address_count > 1){
            $address_status = UserAddress::where('user_id','=',$user->id)->orderBy('updated_at','asc')->where('status','=',1)->first();
            //if($address_status){
            $address_status->update(['status'=>0]);
            //}

        }
        return response()->json($data, 200);
    }

    //删除收货地址
    public function destroy(Request $request)
    {
        $user = $request->user();
        foreach ($request->address_id as $k=>$value){
            $user_address[$k] = UserAddress::findOrFail($value);

            if($user->id != $user_address[$k]->user_id){
                $data['message'] = "此操作是未经授权的."; // 验证权限
                return response()->json($data, 500);
            }

            $user_address[$k]->delete();
        }

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
