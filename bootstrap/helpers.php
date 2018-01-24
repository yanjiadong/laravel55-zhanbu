<?php
/**
 * Created by PhpStorm.
 * User: yjd
 * Date: 2018/1/18
 * Time: 上午10:44
 */

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
        $a[31] = "子 ";
        $a[32] = "丑 ";
        $a[33] = "寅 ";
        $a[34] = "卯 ";
        $a[35] = "辰 ";
        $a[36] = "巳 ";
        $a[37] = "午 ";
        $a[38] = "未 ";
        $a[39] = "申 ";
        $a[40] = "酉 ";
        $a[41] = "戌 ";
        $a[30] = "亥 ";

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
            for ($i=1; $i<=6; $i++) {
                $dayun[] = $a[(20+((($mg+10)-$i)%10))].$a[(30+((($mz+12)-$i)%12))];
            }
        } else {
            for ($i=1; $i<=6; $i++) {
                $dayun[] = $a[(20+((($mg+10)+$i)%10))].$a[(30+((($mz+12)+$i)%12))];
            }
        }

        $shensha['message'] = '神杀';
        $shensha['data'] = [$a[$ygs],$a[$mgs],$a[$tgs],$a[$yzs],$a[$mzs],$a[$dzs],$a[$tzs]];
        $result = ['info'=>$info,'dayun'=>$dayun,'shensha'=>$shensha];
        return $result;
    }
}
