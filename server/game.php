<?php



//洗牌
function washCards(){
    $cards = [
        11, 21, 31, 41, 51, 61, 71, 81, 91, 101, 111, 121, 131,
        12, 22, 32, 42, 52, 62, 72, 82, 92, 102, 112, 122, 132,
        13, 23, 33, 43, 53, 63, 73, 83, 93, 103, 113, 123, 133,
        14, 24, 34, 44, 54, 64, 74, 84, 94, 104, 114, 124, 134,

        11, 21, 31, 41, 51, 61, 71, 81, 91, 101, 111, 121, 131,
        12, 22, 32, 42, 52, 62, 72, 82, 92, 102, 112, 122, 132,
        13, 23, 33, 43, 53, 63, 73, 83, 93, 103, 113, 123, 133,
        14, 24, 34, 44, 54, 64, 74, 84, 94, 104, 114, 124, 134,

        11, 21, 31, 41, 51, 61, 71, 81, 91, 101, 111, 121, 131,
        12, 22, 32, 42, 52, 62, 72, 82, 92, 102, 112, 122, 132,
        13, 23, 33, 43, 53, 63, 73, 83, 93, 103, 113, 123, 133,
        14, 24, 34, 44, 54, 64, 74, 84, 94, 104, 114, 124, 134,

        11, 21, 31, 41, 51, 61, 71, 81, 91, 101, 111, 121, 131,
        12, 22, 32, 42, 52, 62, 72, 82, 92, 102, 112, 122, 132,
        13, 23, 33, 43, 53, 63, 73, 83, 93, 103, 113, 123, 133,
        14, 24, 34, 44, 54, 64, 74, 84, 94, 104, 114, 124, 134,

        11, 21, 31, 41, 51, 61, 71, 81, 91, 101, 111, 121, 131,
        12, 22, 32, 42, 52, 62, 72, 82, 92, 102, 112, 122, 132,
        13, 23, 33, 43, 53, 63, 73, 83, 93, 103, 113, 123, 133,
        14, 24, 34, 44, 54, 64, 74, 84, 94, 104, 114, 124, 134,

        11, 21, 31, 41, 51, 61, 71, 81, 91, 101, 111, 121, 131,
        12, 22, 32, 42, 52, 62, 72, 82, 92, 102, 112, 122, 132,
        13, 23, 33, 43, 53, 63, 73, 83, 93, 103, 113, 123, 133,
        14, 24, 34, 44, 54, 64, 74, 84, 94, 104, 114, 124, 134,

        11, 21, 31, 41, 51, 61, 71, 81, 91, 101, 111, 121, 131,
        12, 22, 32, 42, 52, 62, 72, 82, 92, 102, 112, 122, 132,
        13, 23, 33, 43, 53, 63, 73, 83, 93, 103, 113, 123, 133,
        14, 24, 34, 44, 54, 64, 74, 84, 94, 104, 114, 124, 134,

        11, 21, 31, 41, 51, 61, 71, 81, 91, 101, 111, 121, 131,
        12, 22, 32, 42, 52, 62, 72, 82, 92, 102, 112, 122, 132,
        13, 23, 33, 43, 53, 63, 73, 83, 93, 103, 113, 123, 133,
        14, 24, 34, 44, 54, 64, 74, 84, 94, 104, 114, 124, 134

    ];
    shuffle($cards);
    return $cards ;
}

//计算分数
function geiFen($num,$num2=0,$num3=0){
        /////////// 第一位为牌数 第二位花色
    $arr = [
        11=>1, 21=>2, 31=>3, 41=>4, 51=>5, 61=>6, 71=>7, 81=>8, 91=>9, 101=>0, 111=>0, 121=>0, 131=>0,
        12=>1, 22=>2, 32=>3, 42=>4, 52=>5, 62=>6, 72=>7, 82=>8, 92=>9, 102=>0, 112=>0, 122=>0, 132=>0,
        13=>1, 23=>2, 33=>3, 43=>4, 53=>5, 63=>6, 73=>7, 83=>8, 93=>9, 103=>0, 113=>0, 123=>0, 133=>0,
        14=>1, 24=>2, 34=>3, 44=>4, 54=>5, 64=>6, 74=>7, 84=>8, 94=>9, 104=>0, 114=>0, 124=>0, 134=>0
    ];
    $n1 =  isset($arr[$num])?$arr[$num]:0;
    $n2 =  isset($arr[$num2])?$arr[$num2]:0;
    $n3 =  isset($arr[$num3])?$arr[$num3]:0;
    $data = $n1 + $n2 + $n3;
    $data = $data % 10;
    return $data;
}

//发牌
function getPai($pais){

    if(count($pais>=6)){
       /* $pais=[112,111,141,142,142,141];*/
        $xpai[] = array_pop($pais);
        $data[] = ['type'=>'x1','pai'=>$xpai[0],'dian'=>0];

        $zpai[] = array_pop($pais);
        $data[] = ['type'=>'z1','pai'=>$zpai[0],'dian'=>0];

        $xpai[] = array_pop($pais);
        $data[] = ['type'=>'x2','pai'=>$xpai[1],'dian'=>geiFen($xpai[0],$xpai[1])];

        $zpai[] = array_pop($pais);
        $data[] = ['type'=>'z2','pai'=>$zpai[1],'dian'=>geiFen($zpai[0],$zpai[1])];

        $z   =  geiFen($zpai[0],$zpai[1]);  //庄分
        $x   =  geiFen($xpai[0],$xpai[1]);  //闲分

        //任何一方等于8或者9停止发牌
        if(!in_array($z,[8,9]) && !in_array($x,[8,9])){
            //闲小于5 闲加一张牌
            if($x <= 5){
                $xpai[] = array_pop($pais);
                $data[] = ['type'=>'x3','pai'=>$xpai[2],'dian'=>geiFen($xpai[0],$xpai[1],$xpai[2])];
            }
            //庄小于5 庄加一张牌
            if(count($xpai)!=3 && $z <= 5){
                $zpai[] = array_pop($pais);
                $data[] = ['type'=>'z3','pai'=>$zpai[2],'dian'=>geiFen($zpai[0],$zpai[1],$zpai[2])];
                //闲没有追加第三张
            }else if(count($xpai)==3){
                //庄为012无论闲第三张多少点都追加一张
                if(in_array($z,[0,1,2])){
                    $zpai[] = array_pop($pais);
                    $data[] = ['type'=>'z3','pai'=>$zpai[2],'dian'=>geiFen($zpai[0],$zpai[1],$zpai[2])];

                 //庄为3且闲第三张不是8 则追加
                }elseif($z == 3 && !geiFen($xpai[2])==8 ){
                    $zpai[] = array_pop($pais);
                    $data[] = ['type'=>'z3','pai'=>$zpai[2],'dian'=>geiFen($zpai[0],$zpai[1],$zpai[2])];

                 //如果闲第三张为234567将追加一张
                }elseif ($z == 4 && in_array(geiFen($xpai[2]),[2,3,4,5,6,7]) ){
                    $zpai[] = array_pop($pais);
                    $data[] = ['type'=>'z3','pai'=>$zpai[2],'dian'=>geiFen($zpai[0],$zpai[1],$zpai[2])];

                 //如果闲第三张为4567将追加一张
                }elseif ($z == 5 && in_array(geiFen($xpai[2]),[4,5,6,7]) ){
                    $zpai[] = array_pop($pais);
                    $data[] = ['type'=>'z3','pai'=>$zpai[2],'dian'=>geiFen($zpai[0],$zpai[1],$zpai[2])];

                    //如果闲第三张为67将追加一张
                }elseif ($z == 6 && in_array(geiFen($xpai[2]),[6,7]) ){
                    $zpai[] = array_pop($pais);
                    $data[] = ['type'=>'z3','pai'=>$zpai[2],'dian'=>geiFen($zpai[0],$zpai[1],$zpai[2])];
                }
            }
        }
        //计算闲最后分数
        if(count($xpai)==3){
            $x +=geiFen($xpai[2]);
            $x = $x % 10;
        }
        //计算庄最后分数
        if(count($zpai)==3){
            $z +=geiFen($zpai[2]);
            $z = $z % 10;
        }

        //获取结果
        $win = getWin($zpai,$xpai,$z,$x);
        // var_dump(count($pais));
        // D('结果:',['z'=>$zpai,'x'=>$xpai,'pais'=>$pais,'xd'=>$x,'zd'=>$z,'win'=>$win,'data'=>$data]);
        return $re = ['z'=>$zpai,'x'=>$xpai,'pais'=>$pais,'xd'=>$x,'zd'=>$z,'win'=>$win,'data'=>$data];


    }else{
        return false;
    }
}

//重新洗牌

function getPai2(){
    $data = washCards();
    $rand = rand(10,30);
    for ($i=1;$i<=$rand;$i++){
        array_pop($data);
    }
  var_dump(['rand'=>$rand,'pais' => $data]);
    return ['rand'=>$rand,'pais' => $data];
}

//判断输赢
function getWin($zpai,$xpai,$zd,$xd){
    //闲大
      if($xd>$zd){
          $re = 10;
        //庄大
      }elseif($zd>$xd){
          $re = 20;
              //和
      }elseif ($xd==$zd){
          $re = 30;
      }
    if(isTW($xpai[0],$xpai[1]) && isTW($zpai[0],$zpai[1])){
        $re += 3; 
    }elseif(isTW($xpai[0],$xpai[1])){
        $re += 1;
    }else if(isTW($zpai[0],$zpai[1])){
        $re += 2;
    }
    return $re;
}

//判断是否是对子
function isDui($p1,$p2){
    $p1 = floor($p1/10);
    $p2 = floor($p2/10);
    if($p1 == $p2){
        return true;
    }
    return false;
}
// 判断是否是天王
function isTW($p1,$p2){
   $xfen =  geiFen($p1,$p2);
   if(in_array($xfen,[8,9])){
        return true;
   }
     return false;
   
}

//返回数据格式
function reData($route,$data){
    $dd = [
        'route'=>$route,
        'data'=>$data
    ];
    return $dd;
}

//获取下注信息
// $zu = time().'/'.$data->type.'_'.$data->mid.'_'.$data->money;
function getZus($data){
    $dd = ['z'=>[],'x'=>[],'h'=>[],'zd'=>[],'xd'=>[]];
    $num = 0;
    foreach ($data  as $v){
        $re = explode('/',$v);
        $re = explode('_',$re[1]);
        $dd["{$re[0]}"][] =  (int)$re[2];
        $num +=(int)$re[2];
    }
    $dd = [
        'num'=>$num,
        'moneys'=>$dd
    ];
   return $dd;
}

//获取用户下注信息
 function getMzu($data,$mids=array()){
            $num = 0;
            $dd2 = ['z'=>0,'x'=>0,'h'=>0,'zd'=>0,'xd'=>0];
            foreach ($data  as $v){
                $re = explode('/',$v);
                $re = explode('_',$re[1]);
                if(!in_array($re[1],$mids)){
                    $num += (int)$re[2];
                    $dd2["{$re[0]}"]  +=  (int)$re[2];
                }
            }
            $d1 = [
                'num'=>$num,
                'moneys'=>$dd2
            ];
        return $d1;
    }

//获取总金额
//$dd = ['z'=>20,'x'=>20,'h'=>20,'zd'=>0,'xd'=>0]
//$win 10
function getMoney($data,$win){
      $win = Win($win);
      $site = Site();
      $num = 0;
      //计算庄闲和
        if($win[0] == 'h'){
            $num += $data['x']+$data['z']+$data['xd']+$data['zd'];
        }
        if(isset($data[$win[0]]) && $data[$win[0]]>0){
            $num += $data[$win[0]] * $site[$win[0]] + $data[$win[0]];
        }
         //又是庄对又是闲对
         if($win[1] && $win[1]=='zdxd'){
             //计算对子
             if(isset($data['zd']) && $data['zd']>0){
                 $num += $data['zd'] * $site['zd'] + $data['zd'];
             }
             if(isset($data['xd']) && $data['xd']>0){
                 $num += $data['xd'] * $site['xd'] + $data['xd'];
             }
         }else{
             //计算对子
             if($win[1] && isset($data[$win[1]]) && $data[$win[1]]>0){
                 $num += $data[$win[1]] * $site[$win[1]] + $data[$win[1]];
             }
         }
    return $num;
}

//获取牌的值
function pai($arr){
    foreach ($arr as &$v){
        $v = floor($v/10);
        if($v == 11){
            $v = 'J';
        }else if($v == 12){
            $v = 'Q';
        }else if($v == 13){
            $v = 'K';
        }else if($v == 1){
            $v = 'A';
        }else{
            $v = "$v";
        }
    }
    return $arr;
}

//获取输赢 11
function Win($num){
    $w1 = intval($num / 10);
    $w2 = $num % 10;
    if($w1 ==1 ){
        $w1 = 'x';
    }elseif ($w1 ==2){
        $w1 = 'z';
    }elseif ($w1 ==3){
        $w1 = 'h';
    }
    if($w2 ==1 ){
        $w2 = 'xd';
    }elseif ($w2 ==2){
        $w2 = 'zd';
    }elseif ($w2 ==3){
        $w2 = 'zdxd';
    }else{
        $w2 = '';
    }
    return [$w1,$w2];
}

//获取输赢
function Win2($num){
    $w1 = intval($num / 10);
    $w2 = $num % 10;
    if($w1 ==1 ){
        $w1 = '闲赢';
    }elseif ($w1 ==2){
        $w1 = '庄赢';
    }elseif ($w1 ==3){
        $w1 = '和';
    }

    if($w2 ==1 ){
        $w2 = ',闲对';
    }elseif ($w2 ==2){
        $w2 = ',庄对';
    }elseif ($w2 ==3){
        $w2 = ',闲对,庄对';
    }else{
        $w2 = '';
    }
    return $w1.$w2;
}

//赔率设置
function Site(){
   return  ['x'=>2,'z'=>1.95,'h'=>9,'ztw'=>3,'xtw'=>3];
}
//用户下注转格式
//$dd = ['z'=>20,'x'=>20,'h'=>20,'zd'=>0,'xd'=>0]
function bet($arr){
    $arr2 = [];
    $kk = [
        'z'=>'庄',
        'x'=>'闲',
        'h'=>'和',
        'zd'=>'庄对',
        'xd'=>'闲对'
    ];
    foreach ($arr as $k=>$v){
        if($v>0){
            $arr2[$kk[$k]] = $v;
        }
    }
    return $arr2;
}
/**
 * 生成一个1-10且不等于$n的随机数。 
*/
function get_rand_num($n)
{
    $num = rand(1,13);
    if($num == $n)
    {
        $num = get_rand_num($n);
    }
    return $num;
}

function get_rand($n){
    
        $dd = rand(0,9 - $n)+$n;
      if($dd == $n){
        $dd = get_rand($n);
      }
       
                    return $dd;
}
/**
 * 总风控
 * @param $pais  所有牌
 * @param $win 闲或者庄赢
 * @return array
 */
//思路：控制第三张牌 根据闲的点数 来处理庄的最后一张牌
//随机获取大小 随机获取花色
function controlzong($pais,$win)
{
    //先把闲的三张牌发完 根据闲来 处理庄的最后一张
            $xs = rand(1, 5);
            $xpai[] = array_pop($pais); //闲牌
            $num = ($xs + 10 - geiFen($xpai[0]))%10;
            $xpai[] = use_pai($num);
            $k = array_search($xpai[1], $pais);
            if ($k != false) {
                unset($pais[$k]);
                shuffle($pais);
            } else {
                array_pop($pais);
            }
            $xpai[] = array_pop($pais);

            $zs = rand(0, 2);
            $zpai[] = array_pop($pais);
            $num = ($zs+10 - geiFen($zpai[0]))%10;
            $zpai[] = use_pai($num);
            $k = array_search($zpai[1], $pais);
            if ($k != false) {
                unset($pais[$k]);
                shuffle($pais);
            } else {
                array_pop($pais);
            }
            $xd = geiFen($xpai[0], $xpai[1], $xpai[2]);
            $zd = geiFen($zpai[0], $zpai[1]);
            //如果闲是0  那么就和
            if ($xd == 0) {
                $num = abs(10 - $zd);
                $zpai[] = use_pai($num);
                $k = array_search($zpai[2], $pais);
                if ($k != false) {
                    unset($pais[$k]);
                    shuffle($pais);
                } else {
                    array_pop($pais);
                }
            }else {
                if($win == 'x'){
                    $dd = rand(0, $xd - 1);
                }elseif($win == 'z'){
                    $dd = rand(0,9 - $xd)+$xd;
                }
                $num = ($dd+10 - $zd)%10;
                $zpai[] = use_pai($num);
                $k = array_search($zpai[2], $pais);
                if ($k != false) {
                    unset($pais[$k]);
                    shuffle($pais);
                } else {
                    array_pop($pais);
                }
             }
        $data[] = ['type'=>'x1','pai'=>$xpai[0],'dian'=>0];
        $data[] = ['type'=>'z1','pai'=>$zpai[0],'dian'=>0];
        $data[] = ['type'=>'x2','pai'=>$xpai[1],'dian'=>geiFen($xpai[0],$xpai[1])];
        $data[] = ['type'=>'z2','pai'=>$zpai[1],'dian'=>geiFen($zpai[0],$zpai[1])];
        $data[] = ['type'=>'x3','pai'=>$xpai[2],'dian'=>geiFen($xpai[0],$xpai[1],$xpai[2])];
        $data[] = ['type'=>'z3','pai'=>$zpai[2],'dian'=>geiFen($zpai[0],$zpai[1],$zpai[2])];
        $zd = geiFen($zpai[0],$zpai[1],$zpai[2]);
    //获取结果
    $win = getWin($zpai,$xpai,$zd,$xd);
    return $re = ['z'=>$zpai,'x'=>$xpai,'pais'=>$pais,'xd'=>$xd,'zd'=>$zd,'win'=>$win,'data'=>$data];
}

/**
 * 风控
 * @param $pais  所有牌
 * @param $win 闲或者庄赢
 * @return array
 */
//思路：控制第三张牌 根据闲的点数 来处理庄的最后一张牌
//随机获取大小 随机获取花色
// $cards = washCards();

//  control($cards,'z');
function control($pais,$win)
{
    //先把闲的三张牌发完 根据闲来 处理庄的最后一张
            $xs = rand(1, 5);
            $xpai[] = array_pop($pais); //闲牌

            $num = ($xs + 10 - geiFen($xpai[0]))%10;

            $xpai[] = use_pai($num);
            $k = array_search($xpai[1], $pais);
            if ($k != false) {
                unset($pais[$k]);
                shuffle($pais);
            } else {
                array_pop($pais);
            }
            // $xpai[] = array_pop($pais); //出和的情况
            $xd = geiFen($xpai[0], $xpai[1]); //不出和的情况
            if($win == 'x'){
              $n = abs(10-$xd);
            }else{
              $n = abs(9-$xd);
            }
                 // $n = abs(10-$xd);
     
            $b = get_rand_num($n);
            $xpai[] = use_pai($b);
               $k = array_search($xpai[2], $pais);
                      if ($k != false) {
                unset($pais[$k]);
                shuffle($pais);
            } else {
                array_pop($pais);
            }
            $zs = rand(0, 2);
            $zpai[] = array_pop($pais);
            $num = ($zs+10 - geiFen($zpai[0]))%10;
            $zpai[] = use_pai($num);
            $k = array_search($zpai[1], $pais);
            if ($k != false) {
                unset($pais[$k]);
                shuffle($pais);
            } else {
                array_pop($pais);
            }
            $xd = geiFen($xpai[0], $xpai[1], $xpai[2]);
            $zd = geiFen($zpai[0], $zpai[1]);
            //如果闲是0  那么就和
            // if ($xd == 0) {
            //     $num = abs(10 - $zd);
            //     $zpai[] = use_pai($num);
            //     $k = array_search($zpai[2], $pais);
            //     if ($k != false) {
            //         unset($pais[$k]);
            //         shuffle($pais);
            //     } else {
            //         array_pop($pais);
            //     }
            // }else {
                if($win == 'x'){
                    $dd = rand(0, $xd - 1);
                }elseif($win == 'z'){
                  
                   $dd =  get_rand($xd);
                    // $dd = rand(0,9 - $xd)+$xd;
                   // echo $dd;
                }
                $num = ($dd+10 - $zd)%10;
                $zpai[] = use_pai($num);
                $k = array_search($zpai[2], $pais);
                if ($k != false) {
                    unset($pais[$k]);
                    shuffle($pais);
                } else {
                    array_pop($pais);
                }
             // }
        $data[] = ['type'=>'x1','pai'=>$xpai[0],'dian'=>0];
        $data[] = ['type'=>'z1','pai'=>$zpai[0],'dian'=>0];
        $data[] = ['type'=>'x2','pai'=>$xpai[1],'dian'=>geiFen($xpai[0],$xpai[1])];
        $data[] = ['type'=>'z2','pai'=>$zpai[1],'dian'=>geiFen($zpai[0],$zpai[1])];
        $data[] = ['type'=>'x3','pai'=>$xpai[2],'dian'=>geiFen($xpai[0],$xpai[1],$xpai[2])];
        $data[] = ['type'=>'z3','pai'=>$zpai[2],'dian'=>geiFen($zpai[0],$zpai[1],$zpai[2])];
        $zd = geiFen($zpai[0],$zpai[1],$zpai[2]);
    //获取结果
    $win = getWin($zpai,$xpai,$zd,$xd);
   
    return $re = ['z'=>$zpai,'x'=>$xpai,'pais'=>$pais,'xd'=>$xd,'zd'=>$zd,'win'=>$win,'data'=>$data];
}
/**
 * 风控
 * @param $pais  所有牌
 * @param $win 庄天王或闲天王赢
 * @return array
 */
//思路：控制两张牌输赢
//随机获取大小 随机获取花色
//通过点数获取牌
// $cards = washCards();
// WinTw($cards,'ztw');
function WinTw($pais,$win){
    if($win == 'xtw'){

          $rand = rand(8,9);
    $xpai[] = array_pop($pais);

    $num = ($rand - geiFen($xpai[0]));
    if($num <=0){
        $num = 0;
   
    }
      $xpai[] = use_pai($num);
       $k = array_search($xpai[1], $pais);
        if ($k != false) {
                unset($pais[$k]);
                shuffle($pais); 
            } else {
                array_pop($pais);
            }
    $zpai[] =  array_pop($pais);
    $zpai[] =  array_pop($pais);
     $xd = geiFen($xpai[0], $xpai[1]);
    $zd = geiFen($zpai[0], $zpai[1]);
    
    }else{
          $rand = rand(8,9);
    $zpai[] = array_pop($pais);

    $num = ($rand - geiFen($zpai[0]));
    if($num <=0){
        $num = 0;
   
    }
      $zpai[] = use_pai($num);
       $k = array_search($zpai[1], $pais);
        if ($k != false) {
                unset($pais[$k]);
                shuffle($pais); 
            } else {
                array_pop($pais);
            }
    $xpai[] =  array_pop($pais);
    $xpai[] =  array_pop($pais);
     $zd = geiFen($zpai[0], $zpai[1]);
    $xd = geiFen($xpai[0], $xpai[1]);
    
    }
   $win = getWin($zpai,$xpai,$zd,$xd);
        $data[] = ['type'=>'x1','pai'=>$xpai[0],'dian'=>0];
        $data[] = ['type'=>'z1','pai'=>$zpai[0],'dian'=>0];
        $data[] = ['type'=>'x2','pai'=>$xpai[1],'dian'=>geiFen($xpai[0],$xpai[1])];
        $data[] = ['type'=>'z2','pai'=>$zpai[1],'dian'=>geiFen($zpai[0],$zpai[1])];
        D('jieguo',['z'=>$zpai,'x'=>$xpai,'xd'=>$xd,'zd'=>$zd,'win'=>$win,'data'=>$data]);
        // var_dump(['z'=>$zpai,'x'=>$xpai,'xd'=>$xd,'zd'=>$zd,'win'=>$win,'data'=>$data]);
        return $re = ['z'=>$zpai,'x'=>$xpai,'pais'=>$pais,'xd'=>$xd,'zd'=>$zd,'win'=>$win,'data'=>$data];

}
function  use_pai($dian){
    if($dian == 0 || $dian == 10){
        $num = rand(10,13);
    }else{
        $num = $dian;
    }
    $pai = $num * 10 + rand(1, 4);
    return $pai;
}
// 转换压住数据
    function zhuanhuan($one,$tow){
        $zxh = '';
        $tw = '';
        $twh = '';
        $shuz = '';//输庄
        $shux = '';//输闲
        $shuh = '';//输和
        $shuztw = '';//输庄天王
        $shuxtw = '';//输闲天王
        if($one == 1){ // 如果闲赢  庄 和输
            $zxh = 'x';
            $shuz = 'z';
            $shuh = 'h';

        }elseif($one == 2){// 如果庄赢  闲 和输
            $zxh = 'z';
            $shuz = 'x';
            $shuh = 'h';
        }else{// 和赢 庄闲都不输
            $zxh = 'h';
        }

        if($tow == 1){// 闲天王赢 庄天文输
            $tw = 'xtw';
            $shuztw = 'ztw';

        }elseif($tow == 2){//// 庄天王赢 闲天文输
            $tw = 'ztw';
            $shuxtw = 'xtw';
        }elseif($tow == 3){ // 天王都赢 
            $twh = 'twh'; // 庄闲天王都赢
        }else{// 天王不赢 则压天王的都输 
            $tw = '';
            $shuztw = 'ztw';
            $shuxtw = 'xtw';
        }
        return ['zxh'=>$zxh,'tw'=>$tw,'twh'=>$twh,'shuz'=>$shuz,'shux'=>$shux,'shuh'=>$shuh,'shuztw'=>$shuztw,'shuxtw'=>$shuxtw] ;
    }
/**
 * 随机处理机器人下注
 * @return int|string
 * 加入机器人  机器人离开   下注  无操作
 */

// $proarr = bet_rand();
// robot_rand($proarr);
// robot_rand(money());
function  robot_rand($proArr) {
    $result = '';
    //概率数组的总概率精度
    $proSum = array_sum($proArr);
    //概率数组循环
    foreach ($proArr as $key => $proCur) {
        $randNum = mt_rand(1, $proSum);
        if ($randNum <= $proCur) {
            $result = $key;
            break;
        } else {
            $proSum -= $proCur;
        }
    }
    D('随机概率：',$result);
    return $result;
}
//风控开启概率
function fenkong(){
    $arr = [
        '1'=>80,
        '0'=>20
    ];
    return $arr;
}
//机器人随机的金币
function money(){
     $arr[]= [
        '1000'=>10,    
        '100'=>50,   
        '5000'=>30,   
        '10000'=>5,     
        '50000'=>5    
    ];
    $arr[] = [
        '1000'=>40,     
        '100'=>30,     
        '5000'=>10,     
        '10000'=>15,    
        '50000'=>5    
    ];
    $arr[] = [
        '1000'=>45,     
        '100'=>44,    
        '5000'=>5,    
        '10000'=>3,     
        '50000'=>3    
    ];
    $re = mt_rand(0,2);
    return $arr[$re];
}


//机器人下注的可能
function  bet_rand(){
    $arr[]= [
        'z'=>17,     //庄
        'x'=>80,     //闲
        'h'=>1,     //和
        'ztw'=>1,     //庄对
        'xtw'=>1    //闲对
    ];
    $arr[] = [
        'z'=>80,     //庄
        'x'=>17,     //闲
        'h'=>1,     //和
        'ztw'=>1,     //庄对
        'xtw'=>1    //闲对
    ];
    $arr[] = [
        'z'=>45,     //庄
        'x'=>44,     //闲
        'h'=>5,     //和
        'ztw'=>3,     //庄对
        'xtw'=>3    //闲对
    ];
    $re = mt_rand(0,2);
    return $arr[$re];
}


/**
 * 路单
 * @param $re 结果
 * @param $dalu  路单
 * @param $last  上把结果
 * @param $weizhi  位置
 * @return array
 */
function dalu($re,$dalu=[],$last=0,$weizhi=[]){
    if(empty($dalu)){
        $dalu[0][0] = $re;
        $last = $re;
        $weizhi = [0,0];
        return ['dalu'=>$dalu,'last'=>$last,'weizhi'=>$weizhi];
    }else{
        if(getNum($re)[2] == getNum($last)[2]){
            if(isset($dalu[$weizhi[0]][$weizhi[1]+1]) || $weizhi[1] == 6){
                $dalu[$weizhi[0]+1][$weizhi[1]] = $re;
                $last = $re;
                $weizhi =[($weizhi[0]+1),$weizhi[1]];
                return ['dalu'=>$dalu,'last'=>$last,'weizhi'=>$weizhi];
            }else{
                $dalu[$weizhi[0]][$weizhi[1]+1] = $re;
                $last = $re;
                $weizhi =[$weizhi[0],($weizhi[1]+1)];
                return ['dalu'=>$dalu,'last'=>$last,'weizhi'=>$weizhi];
            }
        }else{
            $zuihou = change_l($dalu);
            $dalu[$zuihou][0] = $re;
            $last = $re;
            $weizhi = [$zuihou,0];
            return ['dalu'=>$dalu,'last'=>$last,'weizhi'=>$weizhi];
        }
    }
}

function change_l($arr){
    for($i = 0; $i<100; $i++){
        if(!isset($arr[$i][0])){
            return $i;
        }
    }
}


//获取位数
function getNum($num){
     $g = $num % 10;
     $shi = ($num - $g) % 10;
     $bai = floor($num / 100);
     return [$g,$shi,$bai];
}

//大陆2数据
function dalu2($re,$dalu=[],$weizhi=[]){
    if(empty($dalu)){
        $dalu[0][0] = $re;
        $weizhi = [0,0];
        return ['dalu'=>$dalu,'weizhi'=>$weizhi];
    }else{
        if(getNum($re)[2] == getNum($dalu[$weizhi[0]][$weizhi[1]])[2]){
                $dalu[$weizhi[0]][$weizhi[1]+1] = $re;
                $weizhi =[$weizhi[0],($weizhi[1]+1)];
                return ['dalu'=>$dalu,'weizhi'=>$weizhi];
        }else{
            $zuihou = change_l($dalu);
            $dalu[$zuihou][] = $re;
            $weizhi = [$zuihou,0];
            return ['dalu'=>$dalu,'weizhi'=>$weizhi];
        }
    }
}
