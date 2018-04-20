<?php

function E($str, $flag=true){
    if($flag){
        echo str_repeat('-', 16) . '【' . $str . '】' .
        date('Y-m-d H:i:s', time()) . str_repeat('-', 16) . "\n";
    }else{
        if(!is_array($str)){
            echo '【' . $str . '】' . "\n";
        }else{
            var_dump($str);
        }
    }
}

function D($str, $data){
    if(!is_string($data) && !is_int($data)){
        echo '【' . $str . '】：' . "\n";
        var_dump($data);
    }else{
        echo '【' . $str . '】:' . $data . "\n";
    }
}
