<?php
/**
 * Created by PhpStorm.
 * User: yjd
 * Date: 2018/1/18
 * Time: 上午10:44
 */

if(!function_exists('get_api_response'))
{
    function get_api_response($result)
    {
        $response=['code'=>200,'result'=>$result];
        return $response;
    }
}

if(!function_exists('get_success_api_response'))
{
    function get_success_api_response($message)
    {
        $response=['code'=>200,'message'=>$message];
        return $response;
    }
}

if(!function_exists('get_error_api_response'))
{
    function get_error_api_response($code = 300, $message = '')
    {
        $response=['code'=>$code,'message'=>$message];
        return $response;
    }

}

if(!function_exists('weixin_curl'))
{
    function weixin_curl($url, $type = 'GET', $param = array())
    {
        $type = strtoupper($type);

        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 500);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if(!empty($param) && $type == 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $type);
        $result = curl_exec($ch);
        //print_r($result);
        if ($ch != null) curl_close($ch);

        $result = json_decode($result,true);
        return $result;
    }
}

/**
 * 根据强度获取十神对应关系
 */
if(!function_exists('get_by_strength'))
{
    function get_by_strength($strength)
    {
        if($strength >= 0 && $strength < 11)
        {
            return ['good'=>['官杀','正偏财','食伤'],'bad'=>['印枭','比劫']];
        }
        elseif($strength >= 11 && $strength < 36)
        {
            return ['good'=>['比劫','印枭'],'bad'=>['食伤','官杀','正偏财']];
        }
        elseif($strength >= 36 && $strength < 46)
        {
            return ['good'=>['印枭','正偏财'],'bad'=>['食伤','官杀']];
        }
        elseif($strength >= 46 && $strength < 61)
        {
            return ['good'=>['官杀','正偏财'],'bad'=>['印枭','比劫']];
        }
        elseif($strength >= 61 && $strength < 91)
        {
            return ['good'=>['官杀','食伤'],'bad'=>['比劫','印枭']];
        }
        elseif($strength >= 91 && $strength <= 100)
        {
            return ['good'=>['比劫','印枭'],'bad'=>['食伤']];
        }
    }
}

/**
 * 根据地支获取带有一个多个百分比的天干
 */
if(!function_exists('get_tiangan_by_dizhi_percent'))
{
    function get_tiangan_by_dizhi_percent($dizhi)
    {
        $result = [];

        switch ($dizhi)
        {
            case '子':
                $result = [['flag'=>'癸','percent'=>100]];
                break;
            case '丑':
                $result = [['flag'=>'己','percent'=>70],['flag'=>'癸','percent'=>20],['flag'=>'辛','percent'=>10]];
                break;
            case '寅':
                $result = [['flag'=>'甲','percent'=>70],['flag'=>'丙','percent'=>20],['flag'=>'戊','percent'=>10]];
                break;
            case '卯':
                $result = [['flag'=>'乙','percent'=>100]];
                break;
            case '辰':
                $result = [['flag'=>'戊','percent'=>70],['flag'=>'乙','percent'=>20],['flag'=>'癸','percent'=>10]];
                break;
            case '巳':
                $result = [['flag'=>'丙','percent'=>70],['flag'=>'庚','percent'=>20],['flag'=>'戊','percent'=>10]];
                break;
            case '午':
                $result = [['flag'=>'丁','percent'=>70],['flag'=>'己','percent'=>30]];
                break;
            case '未':
                $result = [['flag'=>'己','percent'=>70],['flag'=>'丁','percent'=>20],['flag'=>'乙','percent'=>10]];
                break;
            case '申':
                $result = [['flag'=>'庚','percent'=>70],['flag'=>'壬','percent'=>20],['flag'=>'戊','percent'=>10]];
                break;
            case '酉':
                $result = [['flag'=>'辛','percent'=>100]];
                break;
            case '戌':
                $result = [['flag'=>'戊','percent'=>70],['flag'=>'辛','percent'=>20],['flag'=>'丁','percent'=>10]];
                break;
            case '亥':
                $result = [['flag'=>'壬','percent'=>70],['flag'=>'甲','percent'=>30]];
                break;
        }

        return $result;
    }
}

//获取地址五行
if(!function_exists('get_dizhi_hang'))
{
    function get_dizhi_hang($dizhi)
    {
        $result = [];

        switch ($dizhi)
        {
            case '子':
                $result = [['flag'=>'water','percent'=>100]];
                break;
            case '丑':
                $result = [['flag'=>'soil','percent'=>70],['flag'=>'water','percent'=>20],['flag'=>'gold','percent'=>10]];
                break;
            case '寅':
                $result = [['flag'=>'wood','percent'=>70],['flag'=>'fire','percent'=>20],['flag'=>'soil','percent'=>10]];
                break;
            case '卯':
                $result = [['flag'=>'wood','percent'=>100]];
                break;
            case '辰':
                $result = [['flag'=>'soil','percent'=>70],['flag'=>'wood','percent'=>20],['flag'=>'water','percent'=>10]];
                break;
            case '巳':
                $result = [['flag'=>'fire','percent'=>70],['flag'=>'gold','percent'=>20],['flag'=>'soil','percent'=>10]];
                break;
            case '午':
                $result = [['flag'=>'fire','percent'=>70],['flag'=>'soil','percent'=>30]];
                break;
            case '未':
                $result = [['flag'=>'soil','percent'=>70],['flag'=>'fire','percent'=>20],['flag'=>'wood','percent'=>10]];
                break;
            case '申':
                $result = [['flag'=>'gold','percent'=>70],['flag'=>'water','percent'=>20],['flag'=>'soil','percent'=>10]];
                break;
            case '酉':
                $result = [['flag'=>'gold','percent'=>100]];
                break;
            case '戌':
                $result = [['flag'=>'soil','percent'=>70],['flag'=>'gold','percent'=>20],['flag'=>'fire','percent'=>10]];
                break;
            case '亥':
                $result = [['flag'=>'water','percent'=>70],['flag'=>'wood','percent'=>30]];
                break;
        }

        return $result;
    }
}

//天干五行
if(!function_exists('get_tiangan_hang'))
{
    function get_tiangan_hang($tiangan)
    {
        $result = '';
        if($tiangan == '甲' || $tiangan == '乙')
        {
            $result = 'wood';  //木
        }
        elseif($tiangan == '丙' || $tiangan == '丁')
        {
            $result = 'fire';  //火
        }
        elseif($tiangan == '戊' || $tiangan == '己')
        {
            $result = 'soil';   //土
        }
        elseif($tiangan == '庚' || $tiangan == '辛')
        {
            $result = 'gold';   //金
        }
        elseif($tiangan == '壬' || $tiangan == '癸')
        {
            $result = 'water';   //水
        }

        return $result;
    }
}


//根据地支算出多个天干
if(!function_exists('get_tiangan_by_dizhi'))
{
    function get_tiangan_by_dizhi($tiangan)
    {
        $dizhi = [];

        switch ($tiangan)
        {
            case '子':
                $dizhi = ['癸'];
                break;
            case '丑':
                $dizhi = ['己','癸','辛'];
                break;
            case '寅':
                $dizhi = ['甲','丙','戊'];
                break;
            case '卯':
                $dizhi = ['乙'];
                break;
            case '辰':
                $dizhi = ['戊','乙','癸'];
                break;
            case '巳':
                $dizhi = ['丙','庚','戊'];
                break;
            case '午':
                $dizhi = ['丁','己'];
                break;
            case '未':
                $dizhi = ['己','丁','乙'];
                break;
            case '申':
                $dizhi = ['庚','壬','戊'];
                break;
            case '酉':
                $dizhi = ['辛'];
                break;
            case '戌':
                $dizhi = ['戊','辛','丁'];
                break;
            case '亥':
                $dizhi = ['壬','甲'];
                break;
        }

        return $dizhi;
    }
}

if(!function_exists('get_lunar_hour'))
{
    function get_lunar_hour($hour)
    {
        $result = '';
        if($hour>=23 && $hour<24)
        {
            $result = '子时';
        }
        elseif($hour>=1 && $hour<3)
        {
            $result = '丑时';
        }
        elseif($hour>=3 && $hour<5)
        {
            $result = '寅时';
        }
        elseif($hour>=5 && $hour<7)
        {
            $result = '卯时';
        }
        elseif($hour>=7 && $hour<9)
        {
            $result = '辰时';
        }
        elseif($hour>=9 && $hour<11)
        {
            $result = '巳时';
        }
        elseif($hour>=11 && $hour<13)
        {
            $result = '午时';
        }
        elseif($hour>=13 && $hour<15)
        {
            $result = '未时';
        }
        elseif($hour>=15 && $hour<17)
        {
            $result = '申时';
        }
        elseif($hour>=17 && $hour<19)
        {
            $result = '酉时';
        }
        elseif($hour>=19 && $hour<21)
        {
            $result = '戌时';
        }
        elseif($hour>=21 && $hour<23)
        {
            $result = '亥时';
        }
        return $result;
    }
}

if(!function_exists('get_day_gan_image'))
{
    function get_day_gan_image($day_gan)
    {
        //$image = 'http://p15eu3hv7.bkt.clouddn.com/jia_1.png';
        $image = config('app.url').'/image/jia1.jpg';
        switch ($day_gan)
        {
            case '甲':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/jia_1.png";
                $image = config('app.url')."/image/jia1.jpg";
                break;
            case '乙':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/yi_1.png";
                $image = config('app.url')."/image/yi1.jpg";
                break;
            case '丙':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/bing_1.png";
                $image = config('app.url')."/image/bing1.jpg";
                break;
            case '丁':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/ding_1.png";
                $image = config('app.url')."/image/ding1.jpg";
                break;
            case '戊':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/wu_1.png";
                $image = config('app.url')."/image/wu1.jpg";
                break;
            case '己':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/ji_1.png";
                $image = config('app.url')."/image/ji1.jpg";
                break;
            case '庚':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/geng_1.png";
                $image = config('app.url')."/image/geng1.jpg";
                break;
            case '辛':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/xin_1.png";
                $image = config('app.url')."/image/xin1.jpg";
                break;
            case '壬':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/ren_1.png";
                $image = config('app.url')."/image/ren1.jpg";
                break;
            case '癸':
                //$image = "http://p15eu3hv7.bkt.clouddn.com/gui_1.png";
                $image = config('app.url')."/image/gui1.jpg";
                break;
        }

        return $image;
    }
}

if(!function_exists('bz'))
{
    function bz($nian1,$yue1,$ri1,$hh1,$xingbie = '男')
    {
        //$nian1 = 1992;//年
        //$yue1 = 01;//月
        //$ri1 = 24;//日
        //$hh1 = 20;//时
        //$xingbie = '男';//性别

        //十神名称
        $a[1] = "比肩";
        $a[2] = "劫财";
        $a[3] = "食神";
        $a[4] = "伤官";
        $a[5] = "偏财";
        $a[6] = "正财";
        $a[7] = "七杀";
        $a[8] = "正官";
        $a[9] = "偏印";
        $a[0] = "正印";

        //十天干
        $a[21] = "甲";
        $a[22] = "乙";
        $a[23] = "丙";
        $a[24] = "丁";
        $a[25] = "戊";
        $a[26] = "己";
        $a[27] = "庚";
        $a[28] = "辛";
        $a[29] = "壬";
        $a[20] = "癸";

        //十二地支
        $a[31] = "子";
        $a[32] = "丑";
        $a[33] = "寅";
        $a[34] = "卯";
        $a[35] = "辰";
        $a[36] = "巳";
        $a[37] = "午";
        $a[38] = "未";
        $a[39] = "申";
        $a[40] = "酉";
        $a[41] = "戌";
        $a[30] = "亥";

        $year = $nian1;
        $month = $yue1;
        $day = $ri1;
        $time = $hh1;

        if ($xingbie=="男") {
            $sex=1;
        } else {
            $sex=0;
        }

        //检测输入数据是否合法

        switch ($month) {
            case 2:
                if ((($year%4 == 0) && (($year%100) != 0) || ($year%400 == 0)) && ($day > 29)) {
                    echo "请检查输入数据是否出错！";
                }
                if ((($year%4 != 0) && (($year%100) == 0) || ($year%400 != 0)) && ($day > 28)) {
                    echo "请检查输入数据是否出错！";
                }
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                if (($day > 30)) {
                    echo "请检查输入数据是否出错！";
                }
                break;
            default:
                break;
        }

        //dim $md;
//确定节气 yz 月支  起运基数 qyjs

        $md=$month * 100 + $day;
        if($md>=204 and $md<= 305) {
            $mz = 3;
            $qyjs = (($month - 2) * 30 + $day - 4) / 3;
        }

        if($md>=306 and $md<=404) {
            $mz = 4;
            $qyjs = (($month - 3) * 30 + $day - 6) / 3;
        }

        if($md>=405 and $md<= 504) {
            $mz = 5;
            $qyjs = (($month - 4) * 30 + $day - 5) / 3;
        }

        if($md>=505 and $md<=  605) {
            $mz = 6;
            $qyjs = (($month - 5) * 30 + $day - 5) / 3;
        }

        if($md>=606 and $md<= 706) {
            $mz = 7;
            $qyjs = (($month - 6) * 30 + $day - 6) / 3;
        }

        if($md>=707 and $md<= 807) {
            $mz = 8;
            $qyjs = (($month - 7) * 30 + $day - 7) / 3;
        }

        if($md>=808 and $md<=907) {
            $mz = 9;
            $qyjs = (($month - 8) * 30 + $day - 8) / 3;
        }

        if($md>=908 and $md<=1007) {
            $mz = 10;
            $qyjs = (($month - 9) * 30 + $day - 8) / 3;
        }

        if($md>=1008 and $md<= 1106) {
            $mz = 11;
            $qyjs = (($month - 10) * 30 + $day - 8) / 3;
        }

        if($md>=1107 and $md<=  1207) {
            $mz = 0;
            $qyjs = (($month - 11) * 30 + $day - 7) / 3;
        }

        if($md>=1208 and $md<=  1231) {
            $mz = 1;
            $qyjs = ($day - 8) / 3;
        }

        if($md>=101 and $md<= 105) {
            $mz = 1;
            $qyjs = (30 + $day - 4) / 3;
        }

        if($md>=106 and $md<=  203) {
            $mz = 2;
            $qyjs = (($month - 1) * 30 + $day - 6) / 3;
        }

//确定年干和年支 yg 年干 yz 年支
        if($md>=204 and $md<=1231) {
            $yg = ($year - 3)%10;
            $yz = ($year - 3)%12;
        }
        if($md>=101 and $md<=203 ) {
            $yg = ($year - 4)%10;
            $yz = ($year - 4)%12;
        }

//确定月干 mg 月干

        if (($mz > 2 && $mz <= 11)) {

            $mg = ($yg * 2 + $mz + 8)%10;
        } else {
            $mg = ($yg * 2 + $mz)%10;
        }

//从公元0年到目前年份的天数 yearlast

        $yearlast = ($year - 1) * 5 + ($year - 1) / 4 - ($year - 1) / 100 + ($year - 1) / 400;
//计算某月某日与当年1月0日的时间差（以日为单位）yearday
        $yearday = 0;
        for ($i=1; $i<$month; $i++) {
            switch ($i) {
                case 1:
                case 3:
                case 5:
                case 7:
                case 8:
                case 10:
                case 12:
                    $yearday = $yearday + 31;
                    break;
                case 4:
                case 6:
                case 9:
                case 11:
                    $yearday = $yearday + 30;
                    break;
                case 2:
                    if (($year%4 == 0) && (($year%100) != 0) || ($year%400 == 0)) {
                        $yearday = $yearday + 29;
                    } else {
                        $yearday = $yearday + 28;
                    }
                    break;
                default:
                    break;
            }


        }

        $yearday = $yearday + $day;
// echo $yearday;
// $yearday = 286;
//计算日的六十甲子数 day60
        $day60 = ($yearlast + $yearday + 6015)%60;

//确定 日干 dg  日支  dz
        $dg = $day60%10;
        $dz = $day60%12;
//确定 时干 tg 时支 tz
        $tz = ($time + 3) / 2%12;
//tg = (dg * 2 + tz + 8) Mod 10
        if (($tz == 0)) {
            $tg = ($dg * 2 + $tz)%10;

        } else {
            $tg = ($dg * 2 + $tz + 8)%10;
        }

//到此，已经完成把 年月日时 转换成为 生辰八字的任务


//确定各地支所纳天干
//年支纳干 yzg 月支纳干 mzg  日支纳干 dzg 时支纳干 tzg
//年支纳干 yzg
        switch ($yz) {
            case 1:
                $yzg = 0;
                break;
            case 2:
            case 8:
                $yzg = 6;
                break;
            case 3:
                $yzg = 1;
                break;
            case 4:
                $yzg = 2;
                break;
            case 5:
            case 11:
                $yzg = 5;
                break;
            case 6:
                $yzg = 3;
                break;
            case 7:
                $yzg = 4;
                break;
            case 9:
                $yzg = 7;
                break;
            case 10:
                $yzg = 8;
                break;
            case 0:
                $yzg = 9;
                break;
            default:
                break;
        }

//月支纳干 mzg
        switch ($mz) {
            case 1:
                $mzg = 0;
                break;
            case 2:
            case 8:
                $mzg = 6;
                break;
            case 3:
                $mzg = 1;
                break;
            case 4:
                $mzg = 2;
                break;
            case 5:
            case 11:
                $mzg = 5;
                break;
            case 6:
                $mzg = 3;
                break;
            case 7:
                $mzg = 4;
                break;
            case 9:
                $mzg = 7;
                break;
            case 10:
                $mzg = 8;
                break;
            case 0:
                $mzg = 9;
                break;

            default:
                break;
        }


//日支纳干 dzg
        switch ($dz) {
            case 1:
                $dzg = 0;
                break;
            case 2:
            case 8:
                $dzg = 6;
                break;
            case 3:
                $dzg = 1;
                break;
            case 4:
                $dzg = 2;
                break;
            case 5:
            case 11:
                $dzg = 5;
                break;
            case 6:
                $dzg = 3;
                break;
            case 7:
                $dzg = 4;
                break;
            case 9:
                $dzg = 7;
                break;
            case 10:
                $dzg = 8;
                break;
            case 0:
                $dzg = 9;
                break;
            default:

                break;
        }


//时支纳干 tzg
        switch ($tz) {
            case 1:
                $tzg = 0;
                break;
            case 2:
            case 8:
                $tzg = 6;
                break;
            case 3:
                $tzg = 1;
                break;
            case 4:
                $tzg = 2;
                break;
            case 5:
            case 11:
                $tzg = 5;
                break;
            case 6:
                $tzg = 3;
                break;
            case 7:
                $tzg = 4;
                break;
            case 9:
                $tzg = 7;
                break;
            case 10:
                $tzg = 8;
                break;
            case 0:
                $tzg = 9;
                break;
            default:

                break;
        }


        //确定各支对应的十神

//年干十神 ygs
        $ygs = (($yg + 11 - $dg) + (($dg + 1)%2) * (($yg + 10 - $dg)%2) * 2)%10;

//月干十神 mgs
        $mgs = (($mg + 11 - $dg) + (($dg + 1)%2) * (($mg + 10 - $dg)%2) * 2)%10;

//时干十神 dgs
        $tgs = (($tg + 11 - $dg) + (($dg + 1)%2) * (($tg + 10 - $dg)%2) * 2)%10;

//年支十神 yzs
        $yzs = (($yzg + 11 - $dg) + (($dg + 1)%2) * (($yzg + 10 - $dg)%2) * 2)%10;

//月支十神 mzs
        $mzs = (($mzg + 11 - $dg) + (($dg + 1)%2) * (($mzg + 10 - $dg)%2) * 2)%10;

//日支十神 dzs
        $dzs = (($dzg + 11 - $dg) + (($dg + 1)%2) * (($dzg + 10 - $dg)%2) * 2)%10;

//时支十神 tzs
        $tzs = (($tzg + 11 - $dg) + (($dg + 1)%2) * (($tzg + 10 - $dg)%2) * 2)%10;

        if ($sex==1) {
            $info['message'] = '乾造';
            $info['data'] = [$a[20 + $yg].$a[30 + $yz],$a[20 + $mg].$a[30 + $mz],$a[20 + $dg].$a[30 + $dz],$a[20 + $tg].$a[30 + $tz]];
            //echo ("乾造：".$a[20 + $yg].$a[30 + $yz].$a[20 + $mg].$a[30 + $mz].$a[20 + $dg].$a[30 + $dz].$a[20 + $tg].$a[30 + $tz]);
        } else {
            $info['message'] = '坤造';
            $info['data'] = [$a[20 + $yg].$a[30 + $yz],$a[20 + $mg].$a[30 + $mz],$a[20 + $dg].$a[30 + $dz],$a[20 + $tg].$a[30 + $tz]];
            //echo ("坤造：".$a[20 + $yg].$a[30 + $yz].$a[20 + $mg].$a[30 + $mz].$a[20 + $dg].$a[30 + $dz].$a[20 + $tg].$a[30 + $tz]);
        }

        $dayun = [];
        if (($yz%2)) {
            for ($i=1; $i<=8; $i++) {
                $dayun[] = $a[(20+((($mg+10)-$i)%10))].$a[(30+((($mz+12)-$i)%12))];
            }
        } else {
            for ($i=1; $i<=8; $i++) {
                $dayun[] = $a[(20+((($mg+10)+$i)%10))].$a[(30+((($mz+12)+$i)%12))];
            }
        }

        $shensha['message'] = '神杀';
        $shensha['data'] = [$a[$ygs],$a[$mgs],$a[$tgs],$a[$yzs],$a[$mzs],$a[$dzs],$a[$tzs]];
        $result = ['info'=>$info,'dayun'=>$dayun,'shensha'=>$shensha];
        return $result;
    }
}
