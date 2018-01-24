<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Overtrue\ChineseCalendar\Calendar;

class IndexController extends BaseController
{
    public function index(Request $request)
    {
        $calendar = new Calendar();

        $nian = $request->get('nian');
        $yue = $request->get('yue');
        $ri = $request->get('ri');
        $hh = $request->get('hh');
        $xingbie = $request->get('xingbie');

        $result1 = bz($nian, $yue, $ri, $hh, $xingbie);

        $result2 = $calendar->solar($nian, $yue, $ri); // 阳历
        //print_r($result);
        return ['result1'=>$result1,'result2'=>$result2];
    }

    public function test()
    {
        return ['code'=>200,'info'=>'test'];
    }

}
