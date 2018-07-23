<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 19:11
 */


function bin_todec($datalisi,$bin)
{
    static
    $arr=array('0'=>0,'1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,'A'=>10,'B'=>11,'C'=>12,'D'=>13,
        'E'=>14,'F'=>15);
    if (!is_array($datalisi))$datalisi=array($datalisi);
    if ($bin==10)return $datalisi;//为10进制不转换
    $aOutData=array();//定义输出保存数组
    foreach ($datalisi as $num){
        $atnum = str_split($num);//将字符串分割为单个字符数组
        $atlen=count($atnum);
        $total=0;
        $i=1;
        foreach ($atnum as $tv){
            $tv=strtoupper($tv);
            if (array_key_exists($tv,$arr)){
                if ($arr[$tv]==0)continue;
                $total=$total+$arr[$tv]*pow($bin,$atlen-$i);
            }
            $i++;
        }
        $aOutData[]=$total;
    }
    return $aOutData;
}

//$str = 'asda421312432154353dasd12ad@@a.asjd123a2';
//$arr=bin_todec(md5($str),16);
//echo $arr[0];'<br/>';
////这里请注意，这里不能使用 $arr[0]%30 可能得到一个负数
//$no =fmod($arr[0],3);
//echo 'no ='.$no;