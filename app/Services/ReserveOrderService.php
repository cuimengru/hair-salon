<?php


namespace App\Services;


use App\Models\ReserveInformation;
use App\Models\ReserveOrder;
use App\Models\User;
use Illuminate\Http\Request;

class ReserveOrderService
{
    public function store(User $user,$request)
    {
        $reserve_id = $request->reserve_id;
        if ($reserve_id > 0){
            $reserve = ReserveInformation::where('id','=',$reserve_id)->first();
            $designer_id = $reserve->designer_id;
        }else{
            $reserve_id = 0;
            $designer_id = 0;
        }

        $order = new ReserveOrder([
            'reserve_id' => $reserve_id,
            'designer_id' => $designer_id,
            'user_id' => $user->id,
            'service_project' => $request->service_project,
            'date' => $request->date,
            'time'=> $request->time,
            'num' => $request->num,
            'phone' => $request->phone,
            'remark' => $request->remark,
            'status'=> 1 ,

        ]);

        $order->save();

        $data['message'] = "创建成功！";
        return response()->json($data, 200);
    }
}
