<?php

namespace App\Traits;

trait Api_response
{

    protected function successRes($data, $code=200, $msg = null)
    {
        return response()->json(
            [
                'status' => 'success',
               // 'count'=>count($data),
                'data' => $data,
                'massage' => $msg
            ],
            $code

        );
    }
    protected function errorRes( $msg,$code=400 )
    {
      return  response()->json(
            [
                'status' => 'error',
                'massage' => $msg,
            ],
            $code
        );
    }
    protected function exceptionRes( $msg,$code )
    {
      return  response()->json(
            [
                'status' => 'exception',
                'massage' => $msg,
            ],
            $code
        );
    }
}
