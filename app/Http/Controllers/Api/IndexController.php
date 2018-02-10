<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Overtrue\ChineseCalendar\Calendar;
use DB;

class IndexController extends BaseController
{
    public function openid(Request $request)
    {
        $code = $request->get('code');

        //$appid = 'wx6724bc9f066510f7';
        //$appsecret = '5356973396c48a9f918d4107966d1611';
        //$weixin =  file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=".$code."&grant_type=authorization_code");//通过code换取网页授权access_token
        //$jsondecode = json_decode($weixin,true); //对JSON格式的字符串进行编码
        //print_r($jsondecode);

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wx6724bc9f066510f7&secret=5356973396c48a9f918d4107966d1611&js_code={$code}&grant_type=authorization_code";
        $result = weixin_curl($url);
        return get_api_response($result);
    }

    public function user_info(Request $request)
    {
        $input = $request->all();
        $user = User::select('id','wechat_avatar','wechat_nickname','wechat_openid','wechat_original','sex','birth_year','birth_month','birth_day','birth_hour','birth_minute')->where('wechat_openid',$input['wechat_openid'])->first();
        if(empty($user))
        {
            return get_error_api_response(300,'获取用户信息失败');
        }

        return get_api_response($user);
    }

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

        $input['simple_image'] = '';
        $input['interest_info'] = '';
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

        $simple_image = $user->simple_image;
        if(empty($simple_image))
        {
            $simple_image = get_day_gan_image($day_gan);
            User::where('wechat_openid',$wechat_openid)->update(['simple_image'=>$simple_image]);
        }

        $result['ganzhi_day'] = $info['ganzhi_day'];
        $result['day_gan'] = $day_gan;
        $result['image'] = $simple_image;

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

    /**
     * 获取趣味版
     * @param Request $request
     * @return array
     */
    public function interest(Request $request)
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


        if(!empty($user->interest_info))
        {
            return get_api_response(json_decode($user->interest_info,true));
        }

        $calendar = new Calendar();
        $info = $calendar->solar($user->birth_year, $user->birth_month, $user->birth_day); // 阳历
        //print_r($info);

        $day_gan = mb_substr($info['ganzhi_day'],0,1);

        $xingbie = $user->sex==1?'男':'女';
        $bz_info = bz($user->birth_year, $user->birth_month, $user->birth_day, $user->birth_hour, $xingbie);
        //print_r($bz_info);

        $year_tiangan = mb_substr($info['ganzhi_year'],0,1);
        $month_tiangan = mb_substr($info['ganzhi_month'],0,1);
        $day_tiangan = mb_substr($info['ganzhi_day'],0,1);
        $hour_tiangan = mb_substr($bz_info['info']['data'][3],0,1);

        $hang = [
            ['flag'=>get_tiangan_hang($year_tiangan),'percent'=>5],
            ['flag'=>get_tiangan_hang($month_tiangan),'percent'=>14],
            ['flag'=>get_tiangan_hang($day_tiangan),'percent'=>10],
            ['flag'=>get_tiangan_hang($hour_tiangan),'percent'=>9],
        ];

        $year_dizhi = mb_substr($info['ganzhi_year'],1,1);
        $month_dizhi = mb_substr($info['ganzhi_month'],1,1);
        $day_dizhi = mb_substr($info['ganzhi_day'],1,1);
        $hour_dizhi = mb_substr($bz_info['info']['data'][3],1,1);

        foreach (get_dizhi_hang($year_dizhi) as $v)
        {
            array_push($hang,['flag'=>$v['flag'],'percent'=>$v['percent']*0.05]);
        }

        foreach (get_dizhi_hang($month_dizhi) as $v)
        {
            array_push($hang,['flag'=>$v['flag'],'percent'=>$v['percent']*0.35]);
        }

        foreach (get_dizhi_hang($day_dizhi) as $v)
        {
            array_push($hang,['flag'=>$v['flag'],'percent'=>$v['percent']*0.18]);
        }

        foreach (get_dizhi_hang($hour_dizhi) as $v)
        {
            array_push($hang,['flag'=>$v['flag'],'percent'=>$v['percent']*0.04]);
        }

        $wood = 0;
        $fire = 0;
        $soil = 0;
        $gold = 0;
        $water = 0;

        foreach ($hang as $h)
        {
            if($h['flag'] == 'wood')
            {
                $wood += $h['percent'];
            }
            elseif($h['flag'] == 'fire')
            {
                $fire += $h['percent'];
            }
            elseif($h['flag'] == 'soil')
            {
                $soil += $h['percent'];
            }
            elseif($h['flag'] == 'gold')
            {
                $gold += $h['percent'];
            }
            elseif($h['flag'] == 'water')
            {
                $water += $h['percent'];
            }
        }

        $result['type'] = DB::table('wuhangyuanshen')->where('rigan',$day_tiangan)->first();
        $result['wuhang'] = ['wood'=>number_format($wood,1,'.',''),'fire'=>number_format($fire,1,'.',''),'soil'=>number_format($soil,1,'.',''),'gold'=>number_format($gold,1,'.',''),'water'=>number_format($water,1,'.','')];   //最终计算出来的八字五行比例
        //$result['xiji'] = DB::table('xiji')->where('key',get_tiangan_hang($day_tiangan))->get();

        /**
         * 日干为火，则日干强度计算为木+火
         * 日干为木，则日干强度计算为水+木
         * 日干为水，则日干强度计算为金+水
         * 日干为金，则日干强度计算为土+金
         * 日干为土，则日干强度计算为火+土
         */
        //计算日干强度
        $rigan = get_tiangan_hang($day_tiangan);
        switch ($rigan)
        {
            case 'fire':
                $strength = $fire + $wood;
                break;
            case 'wood':
                $strength = $wood + $water;
                break;
            case 'water':
                $strength = $water + $gold;
                break;
            case 'gold':
                $strength = $gold + $soil;
                break;
            case 'soil':
                $strength = $soil + $fire;
                break;
        }

        $shishen = [['flag'=>$bz_info['shensha']['data'][0],'percent'=>5],['flag'=>$bz_info['shensha']['data'][1],'percent'=>16],['flag'=>$bz_info['shensha']['data'][2],'percent'=>9]];

        $year_dizhi_shishen = get_tiangan_by_dizhi_percent($year_dizhi);
        foreach ($year_dizhi_shishen as $v)
        {
            $shisha = DB::table('shishen')->where('key',$day_gan.$v['flag'])->first();
            $shishen[] = ['flag'=>$shisha->value,'percent'=>$v['percent']*0.05];
        }

        $month_dizhi_shishen = get_tiangan_by_dizhi_percent($month_dizhi);
        foreach ($month_dizhi_shishen as $v)
        {
            $shisha = DB::table('shishen')->where('key',$day_gan.$v['flag'])->first();
            $shishen[] = ['flag'=>$shisha->value,'percent'=>$v['percent']*0.41];
        }

        $day_dizhi_shishen = get_tiangan_by_dizhi_percent($day_dizhi);
        foreach ($day_dizhi_shishen as $v)
        {
            $shisha = DB::table('shishen')->where('key',$day_gan.$v['flag'])->first();
            $shishen[] = ['flag'=>$shisha->value,'percent'=>$v['percent']*0.2];
        }

        $hour_dizhi_shishen = get_tiangan_by_dizhi_percent($hour_dizhi);
        foreach ($hour_dizhi_shishen as $v)
        {
            $shisha = DB::table('shishen')->where('key',$day_gan.$v['flag'])->first();
            $shishen[] = ['flag'=>$shisha->value,'percent'=>$v['percent']*0.04];
        }

        //print_r($shishen);
        $percent1 = 0;  //官杀
        $percent2 = 0;   //食伤
        $percent3 = 0;  //比劫
        $percent4 = 0;   //印枭
        $percent5 = 0;   //正偏财

        $best_shishen_percent = 0;
        $best_shishen = [];
        foreach ($shishen as $shi)
        {
            if($shi['flag']=='正官' || $shi['flag'] == '七杀')
            {
                $percent1 += $shi['percent'];
            }
            elseif($shi['flag']=='食神' || $shi['flag'] == '伤官')
            {
                $percent2 += $shi['percent'];
            }
            elseif($shi['flag']=='比肩' || $shi['flag'] == '劫财')
            {
                $percent3 += $shi['percent'];
            }
            elseif($shi['flag']=='正印' || $shi['flag'] == '偏印')
            {
                $percent4 += $shi['percent'];
            }
            elseif($shi['flag']=='正财' || $shi['flag'] == '偏财')
            {
                $percent5 += $shi['percent'];
            }

            if($shi['percent'] > $best_shishen_percent)
            {
                $best_shishen_percent = $shi['percent'];
                $best_shishen = $shi;
            }
        }

        //echo '官杀'.$percent1.'<br/>';
        //echo '食伤'.$percent2.'<br/>';
        //echo '比劫'.$percent3.'<br/>';
        //echo '印枭'.$percent4.'<br/>';
        //echo '正偏财'.$percent5.'<br/>';
        //echo $strength;
        $good_bad = get_by_strength($strength);
        //print_r($good_bad);

        $good_temp = 0;
        $good_result = [];
        foreach ($good_bad['good'] as $good)
        {
            if($good == '官杀' && $percent1 > $good_temp)
            {
                $good_temp = $percent1;
                $good_result = ['name'=>$good,'percent'=>$percent1,'value'=>'正官'];
            }
            elseif($good == '食伤' && $percent2 > $good_temp)
            {
                $good_temp = $percent2;
                $good_result = ['name'=>$good,'percent'=>$percent1,'value'=>'食神'];
            }
            elseif($good == '比劫' && $percent3 > $good_temp)
            {
                $good_temp = $percent3;
                $good_result = ['name'=>$good,'percent'=>$percent1,'value'=>'比肩'];
            }
            elseif($good == '印枭' && $percent4 > $good_temp)
            {
                $good_temp = $percent4;
                $good_result = ['name'=>$good,'percent'=>$percent1,'value'=>'正印'];
            }
            elseif($good == '正偏财' && $percent5 > $good_temp)
            {
                $good_temp = $percent5;
                $good_result = ['name'=>$good,'percent'=>$percent1,'value'=>'正财'];
            }
        }

        $bad_temp = 0;
        $bad_result = [];
        //dd($good_bad);
        foreach ($good_bad['bad'] as $bad)
        {
            if($bad == '官杀' && $percent1 > $bad_temp)
            {
                $bad_temp = $percent1;
                $bad_result = ['name'=>$bad,'percent'=>$percent1,'value'=>'正官'];
            }
            elseif($bad == '食伤' && $percent2 > $bad_temp)
            {
                $bad_temp = $percent2;
                $bad_result = ['name'=>$bad,'percent'=>$percent1,'value'=>'食神'];
            }
            elseif($bad == '比劫' && $percent3 > $bad_temp)
            {
                $bad_temp = $percent3;
                $bad_result = ['name'=>$bad,'percent'=>$percent1,'value'=>'比肩'];
            }
            elseif($bad == '印枭' && $percent4 > $bad_temp)
            {
                $bad_temp = $percent4;
                $bad_result = ['name'=>$bad,'percent'=>$percent1,'value'=>'正印'];
            }
            elseif($bad == '正偏财' && $percent5 > $bad_temp)
            {
                $bad_temp = $percent5;
                $bad_result = ['name'=>$bad,'percent'=>$percent1,'value'=>'正财'];
            }
        }

        //print_r($good_result);



        $good_result_info = DB::table('shishen')->where('value',$good_result['value'])->where('first',$day_tiangan)->first();
        $bad_result_info = DB::table('shishen')->where('value',$bad_result['value'])->where('first',$day_tiangan)->first();

        $good_info = DB::table('xiji')->where('type',1)->where('key',$good_result_info->second_hang)->first();
        $bad_info = DB::table('xiji')->where('type',2)->where('key',$bad_result_info->second_hang)->first();
        $result['xiji'] = ['good'=>$good_result_info->second_hang,'good_info'=>$good_info->value,'bad'=>$bad_result_info->second_hang,'bad_info'=>$bad_info->value];


        $result['find_self'] = [];
        //根据日干获取三条
        $list1 = DB::table('find_self_1')->where('type',1)->where('key',$day_tiangan)->inRandomOrder()->limit(3)->get();
        foreach ($list1 as $l)
        {
            $result['find_self'][] = $l->content;
        }

        //根据日主状态
        if($strength <= 45)
        {
            //弱
            $list2 = DB::table('find_self_1')->where('type',3)->where('key',$day_tiangan)->inRandomOrder()->limit(2)->get();
        }
        else
        {
            //强
            $list2 = DB::table('find_self_1')->where('type',2)->where('key',$day_tiangan)->inRandomOrder()->limit(2)->get();
        }
        foreach ($list2 as $l)
        {
            $result['find_self'][] = $l->content;
        }

        //最旺五行
        //echo array_search(max($result['wuhang']),$result['wuhang']);   //获取最旺五行的键
        $list3 = DB::table('find_self_2')->where('type',array_search(max($result['wuhang']),$result['wuhang']))->inRandomOrder()->limit(2)->get();
        foreach ($list3 as $l)
        {
            $result['find_self'][] = $l->content;
        }

        //最旺十神
        //print_r($best_shishen);
        $list4 = DB::table('find_self_3')->where('key',$best_shishen['flag'])->where('type',1)->inRandomOrder()->limit(2)->get();  //有点两条
        $list5 = DB::table('find_self_3')->where('key',$best_shishen['flag'])->where('type',2)->inRandomOrder()->limit(1)->get();  //缺点一条
        foreach ($list4 as $l)
        {
            $result['find_self'][] = $l->content;
        }
        foreach ($list5 as $l)
        {
            $result['find_self'][] = $l->content;
        }

        //计算行事风格
        $result['action_style'] = [];
        $action_style1 = DB::table('action_style')->where('key',$best_shishen['flag'])->where('type',1)->inRandomOrder()->first();
        $result['action_style'][] = str_replace('(ID)',$user->wechat_nickname,$action_style1->content);

        $action_style2 = DB::table('action_style')->where('key',$best_shishen['flag'])->where('type',2)->inRandomOrder()->first();
        $result['action_style'][] = str_replace('(ID)',$user->wechat_nickname,$action_style2->content);

        $action_style3 = DB::table('action_style')->where('key',$best_shishen['flag'])->where('type',3)->inRandomOrder()->first();
        $result['action_style'][] = str_replace('(ID)',$user->wechat_nickname,$action_style3->content);

        $action_style4 = DB::table('action_style')->where('key',$best_shishen['flag'])->where('type',4)->inRandomOrder()->first();
        $result['action_style'][] = str_replace('(ID)',$user->wechat_nickname,$action_style4->content);

        //print_r($result);
        User::where('wechat_openid',$wechat_openid)->update(['interest_info'=>json_encode($result)]);

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
        $list = DB::table('shishen')->get();
        foreach ($list as $v)
        {
            $first = mb_substr($v->key,0,1);
            $second = mb_substr($v->key,1,1);

            $first_hang = get_tiangan_hang($first);
            $second_hang = get_tiangan_hang($second);

            DB::table('shishen')->where('id',$v->id)->update(['first_hang'=>$first_hang,'second_hang'=>$second_hang]);

        }

        return ['code'=>200,'info'=>'test'];
    }

}
