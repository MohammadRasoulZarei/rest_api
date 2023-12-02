<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\payment\Zibal;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class PaymentController extends ApiController
{
   public function send(Request $request) {

    $validate=Validator::make($request->all(),[
        'user_id'=>'required|exists:users,id',
        'request_from'=>'required',
        'order_items.*.product_id'=>'required',
        'order_items.*.quantity'=>['required']
    ]);
    if ($validate->fails()) {
        return $this->errorRes($validate->messages(),422);
    }
    $total=0;
    $delivery=0;
    foreach ($request->order_items as $item) {

        $product=Product::findOrFail($item['product_id']);
        if ($product->quantity<$item['quantity']) {
            return $this->errorRes([
                'swall'=>'تعداد محصول در خواستی برای'.$product->name."بیشتر از موجودی است"
            ],422);
        }
        $total+=$item['quantity']*$product->price;
        $delivery+=$product->delivery_amount;

    }
    $payingAmoutn=$total+$delivery;


    if ($request->gate=='zibal') {
        $result=Zibal::send($payingAmoutn);
        if (isset($result['url'])) {
            DB::beginTransaction();
            $order=Order::create([
                'user_id'=>$request->user_id,
                'total_amount'=>$total,
                'delivery_amount'=>$delivery,
                'paying_amount'=>$payingAmoutn,
                'payment_status'=>0,
            ]);
            foreach ($request->order_items as $item) {
                $product=Product::findOrFail($item['product_id']);
                OrderItem::create([
                    'order_id'=>$order->id,
                    'product_id'=>$product->id,
                    'price'=>$product->price,
                    'quantity'=>$item['quantity'],
                    'subtotal'=>$product->price*$item['quantity'],
                ]);
            }
            Transaction::create([
                'order_id'=>$order->id,
                'user_id'=>$request->user_id,
                'amount'=>$payingAmoutn,
                'token'=>$result['token'],
                'status'=>0,
                'request_from'=>$request->request_from,
            ]);

            DB::commit();
          return  $this->successRes($result);
        }else {
            return $this->errorRes($result);
        }

    }
   }
   public function verify(Request $request) {
    if(request()->gate=='zibal'){
        $response=Zibal::verify($request->token);
        if ($response['result']==100) {
            DB::beginTransaction();

            $Transaction=Transaction::where('token',$request->token)->firstOrfail();
            $Transaction->update([
                'status'=>1
            ]);
            Order::findOrFail($Transaction->order_id)->update([
                'status'=>1,
                'payment_status'=>1
            ]);

            foreach (OrderItem::where('order_id',$Transaction->id)->get() as  $item) {
                $product=Product::findOrFail($item->product_id);
                $product->update([
                    'quantity'=>$product->quantity - $item->quantity
                ]);
            }
            $response['request_from']=$Transaction->request_from;
            DB::commit();
        }else {
            return $this->errorRes('ترامنش انجام نشد بازگشت به سایت پذیرنده.');
        }
        return response()->json($response);
    }
   }

   // methods================
   public function database() {

   }


}

