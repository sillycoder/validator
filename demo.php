<?php

/**
 * @file    demo.php
 * @author  jwviva(@sina.com)
 * @date    2018/2/24 17:19
 * @brief   使用方法: 命令行执行 php demo.php
 */

require_once './Validator.php';

//校验规则, 一个元素为一条规则
$schemas = [

    's1' => ['type' => 'string'],       //输入type为string, 返回true
    's2' => ['type' => 'str'],          //输入type拼写错误, 返回false
    's3' => ['type' => 'string', 'max' => 2],   //输入为string, 字符串长度最大不能超过2
    's4' => ['type' => 'string', 'min' => 5],   //输入为string, 字符串长度最小不能少于5
    's5' => ['type' => 'string', 'min' => 5, 'max' => 10],      //输入为string, 字符串长度最小不能少于5, 最大不能超过10
    's6' => ['type' => 'string', 'in' => ['abc','xyz']],     //输入为string, 值必须在给定的列表中
    's7' => ['type' => 'string', 'match' => '[a-z][a-z0-9_]+@.+\..+'],      //输入为string, 字符串必须符合给定的正则
    's8' => ['type' => 'int'],          //输入为int型, 返回true
    's9' => ['type' => 'int'],          //输入为int型, 返回true
    's10' => ['type' => 'int', 'max' => 100, 'min' => 60],  //输入为int型, 值介于60~100（含）之间
    's11' => ['type' => 'float'],       //输入为float型

    //输入为数组, 如果存在user_id字段, 必须为string, 且长度最小为1。必须有user_valid字段, 且为bool型。其它每个元素都为int, 值最小为0。
    's12' => ['type' => 'array', 'subtype' => 'int', 'min' => 0, 'subSchema' => ['user_id' => ['type' => 'string', 'min' => 1], 'user_valid' => ['type' => 'bool','isRequired'=>1]]],
    //输入为数组, 如果存在user_id字段, 必须为string, 且长度最小为1。必须有user_valid字段, 且为bool型。其它每个元素都为int。
    's13' => ['type' => 'array', 'subtype' => 'int', 'subSchema' => ['user_id' => ['type' => 'string'], 'user_valid' => ['type' => 'bool','isRequired'=>1]]],
    //输入为数组, 如果存在id字段, 使用用户自定义函数userFunc校验。其它每个元素都为int, 值最小为0。
    's14' => ['type' => 'array', 'subtype' => 'int', 'min' => 0, 'subSchema' => ['id' => ['func' => 'userFunc']]],
    's15' => ['func' => 'userFunc'],    //使用用户自定义函数校验
];

//规则对应输入的值
$values = [
    's1' => '10',
    's2' => 'aaa',
    's3' => '好好',
    's4' => 'asdf',
    's5' => '123456',
    's6' => 'abc',
    's7' => 'ba@xx.com',
    's8' => 10,
    's9' => '60',
    's10' => 60,
    's11' => 10.2,
    's12' => [1,2,'user_id' => '10001'],
    's13' => [1,2,'user_id' => '10001','user_valid'=>true],
    's14' => [1,2,'id' => '10001',],
    's15' => 'user func',
];


/** 测试用户验证函数
 * @param $value
 * @param $msg
 * @return bool
 */
function userFunc($value, &$msg)
{
    $msg = 'in user func validator';
    return true;
}

foreach($schemas as $k => $v)
{
    $msg = '';
    $res = Validator::validate($v, $values[$k], $msg);
    echo '校验规则:'.json_encode($v, JSON_UNESCAPED_UNICODE).PHP_EOL;
    echo '输入参数:';
    if(is_array($values[$k]))
    {
        echo json_encode($values[$k], JSON_UNESCAPED_UNICODE).PHP_EOL;
    }
    else if(is_object($values[$k]))
    {
        echo json_encode($values[$k]).PHP_EOL;
    }
    else if(is_float($values[$k]))
    {
        echo $values[$k].PHP_EOL;
    }
    else
    {
        echo var_export($values[$k], 1).PHP_EOL;
    }
    echo '校验结果:'.(($res === true) ? 'true' : 'false').PHP_EOL;
    if($res === false) {
        echo '未通过校验消息:'.$msg.PHP_EOL;
    }
    echo PHP_EOL;
}


/*
执行后输出:

校验规则:{"type":"string"}
输入参数:'10'
校验结果:true

校验规则:{"type":"str"}
输入参数:'aaa'
校验结果:false
未通过校验消息:Invalid schema, type is empty or error

校验规则:{"type":"string","max":2}
输入参数:'好好'
校验结果:true

校验规则:{"type":"string","min":5}
输入参数:'asdf'
校验结果:false
未通过校验消息:The string length must not less than 5

校验规则:{"type":"string","min":5,"max":10}
输入参数:'123456'
校验结果:true

校验规则:{"type":"string","in":["abc","xyz"]}
输入参数:'abc'
校验结果:true

校验规则:{"type":"string","match":"[a-z][a-z0-9_]+@.+\\..+"}
输入参数:'ba@xxx.com'
校验结果:true

校验规则:{"type":"int"}
输入参数:10
校验结果:true

校验规则:{"type":"int"}
输入参数:'60'
校验结果:false
未通过校验消息:Invalid type, the value must be int

校验规则:{"type":"int","max":100,"min":60}
输入参数:60
校验结果:true

校验规则:{"type":"float"}
输入参数:10.2
校验结果:true

校验规则:{"type":"array","subtype":"int","min":0,"subSchema":{"user_id":{"type":"string","min":1},"user_valid":{"type":"bool","isRequired":1}}}
输入参数:{"0":1,"1":2,"user_id":"10001"}
校验结果:false
未通过校验消息:Missing required value, "user_valid" is required

校验规则:{"type":"array","subtype":"int","subSchema":{"user_id":{"type":"string"},"user_valid":{"type":"bool","isRequired":1}}}
输入参数:{"0":1,"1":2,"user_id":"10001","user_valid":true}
校验结果:true

校验规则:{"type":"array","subtype":"int","min":0,"subSchema":{"id":{"func":"userFunc"}}}
输入参数:{"0":1,"1":2,"id":"10001"}
校验结果:true

校验规则:{"func":"userFunc"}
输入参数:'user func'
校验结果:true

 */
