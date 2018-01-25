<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Overtrue\ChineseCalendar\Calendar;
use DB;

class IndexController extends BaseController
{
    /**
     * 保存用户信息
     * @param Request $request
     * @return array
     */
    public function user(Request $request)
    {
        $input = $request->all();

        if(empty($input['wechat_openid']))
        {
            return get_error_api_response(300,'参数wechat_openid不能为空');
        }

        if(empty($input['sex']) || !in_array($input['sex'],[1,2]))
        {
            return get_error_api_response(300,'请输入正确的性别');
        }

        if(empty($input['birth_year']))
        {
            return get_error_api_response(300,'请输入正确的出生年');
        }

        if(empty($input['birth_month']))
        {
            return get_error_api_response(300,'请输入正确的出生月');
        }

        if(empty($input['birth_day']))
        {
            return get_error_api_response(300,'请输入正确的出生日');
        }

        if(empty($input['birth_hour']))
        {
            return get_error_api_response(300,'请输入正确的出生时');
        }

        $user = User::where('wechat_openid',$input['wechat_openid'])->first();
        if($user)
        {
            User::where('wechat_openid',$input['wechat_openid'])->update($input);
        }
        else
        {
            User::create($input);
        }

        return get_success_api_response('操作成功');
    }

    /**
     * 获取精简版
     * @param Request $request
     * @return array
     */
    public function simple(Request $request)
    {
        $wechat_openid = $request->get('wechat_openid');

        if(empty($wechat_openid))
        {
            return get_error_api_response(300,'参数wechat_openid不能为空');
        }

        $user = User::where('wechat_openid',$wechat_openid)->first();
        if(empty($user))
        {
            return get_error_api_response(300,'用户不存在');
        }

        $calendar = new Calendar();
        $info = $calendar->solar($user->birth_year, $user->birth_month, $user->birth_day); // 阳历
        //print_r($info);

        $day_gan = mb_substr($info['ganzhi_day'],0,1);

        $result['ganzhi_day'] = $info['ganzhi_day'];
        $result['day_gan'] = $day_gan;
        $result['image'] = get_day_gan_image($day_gan);

        return get_api_response($result);
    }

    /**
     * 我的命盘
     * @param Request $request
     * @return array
     */
    public function my_mingpan(Request $request)
    {
        $wechat_openid = $request->get('wechat_openid');

        if(empty($wechat_openid))
        {
            return get_error_api_response(300,'参数wechat_openid不能为空');
        }

        $user = User::where('wechat_openid',$wechat_openid)->first();
        if(empty($user))
        {
            return get_error_api_response(300,'用户不存在');
        }



        $calendar = new Calendar();
        $info = $calendar->solar($user->birth_year, $user->birth_month, $user->birth_day); // 阳历
        //print_r($info);

        $xingbie = $user->sex==1?'男':'女';
        $bz_info = bz($user->birth_year, $user->birth_month, $user->birth_day, $user->birth_hour, $xingbie);

        $lunar_hour = get_lunar_hour($user->birth_hour);
        //阳历时间
        $result['gregorian'] = ['gregorian_year'=>$info['gregorian_year'],'gregorian_month'=>$info['gregorian_month'],'gregorian_day'=>$info['gregorian_day'],'gregorian_hour'=>$user->birth_hour];
        //阴历时间
        $result['lunar'] = ['lunar_year'=>$info['lunar_year'],'lunar_month'=>$info['lunar_month'],'lunar_day'=>$info['lunar_day'],'lunar_hour'=>$lunar_hour];

        $result['message'] = $bz_info['info']['message'];  //男=乾造  女=坤造

        //第一排十神  日主与其余干支的关系
        $result['first'] = ['year'=>$bz_info['shensha']['data'][0],'month'=>$bz_info['shensha']['data'][1],'day'=>'日主','hour'=>$bz_info['shensha']['data'][2]];

        //八字
        $result['bazi'] = ['year'=>$info['ganzhi_year'],'month'=>$info['ganzhi_month'],'day'=>$info['ganzhi_day'],'hour'=>$bz_info['info']['data'][3]];


        $year_dizhi = mb_substr($info['ganzhi_year'],1,1);
        $month_dizhi = mb_substr($info['ganzhi_month'],1,1);
        $day_dizhi = mb_substr($info['ganzhi_day'],1,1);
        $hour_dizhi = mb_substr($bz_info['info']['data'][3],1,1);

        //地支对应的多个天干
        $result['dizhi'] = ['year'=>get_tiangan_by_dizhi($year_dizhi),'month'=>get_tiangan_by_dizhi($month_dizhi),'day'=>get_tiangan_by_dizhi($day_dizhi),'hour'=>get_tiangan_by_dizhi($hour_dizhi)];

        $day_gan = mb_substr($info['ganzhi_day'],0,1);

        $year_shisha = '';
        foreach (get_tiangan_by_dizhi($year_dizhi) as $v)
        {
            $shisha = DB::table('shishen')->where('key',$day_gan.$v)->first();
            $year_shisha .= $shisha->value;
        }

        $month_shisha = '';
        foreach (get_tiangan_by_dizhi($month_dizhi) as $v)
        {
            $shisha = DB::table('shishen')->where('key',$day_gan.$v)->first();
            $month_shisha .= $shisha->value;
        }

        $day_shisha = '';
        foreach (get_tiangan_by_dizhi($day_dizhi) as $v)
        {
            $shisha = DB::table('shishen')->where('key',$day_gan.$v)->first();
            $day_shisha .= $shisha->value;
        }

        $hour_shisha = '';
        foreach (get_tiangan_by_dizhi($hour_dizhi) as $v)
        {
            $shisha = DB::table('shishen')->where('key',$day_gan.$v)->first();
            $hour_shisha .= $shisha->value;
        }

        //地支的十神
        $result['second'] = ['year'=>$year_shisha,'month'=>$month_shisha,'day'=>$day_shisha,'hour'=>$hour_shisha];

        //大运
        $result['dayun'] = $bz_info['dayun'];
        return get_api_response($result);
    }

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
        /*$arr = ['甲','乙','丙','丁','戊','己','庚','辛','壬','癸'];
        foreach ($arr as $v)
        {
            foreach ($arr as $vv)
            {
                DB::table('shishen')->insert(['key'=>$v.$vv]);
            }
        }*/
        return ['code'=>200,'info'=>'test'];
    }

}
