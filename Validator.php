<?php

/**
 * @file    Validator.php
 * @author  jwviva(@sina.com)
 * @date    2018/2/24 15:34
 * @brief   基于PHP 5.4.33版本
 */


/**
 * schema使用规则说明:
 * 0. $schema中如果出现非预期的key或非法的值value, 则直接忽略, 不会进行该条件的验证
 * 1. type值必须存在且是 bool, int, float, string, array, object 中的一个
 * 2. 如果用户提供了自定义验证函数, 并且可调用, 则使用用户指定的验证函数, 忽略其他验证条件
 * 3. bool、object型数据只验证类型
 * 4. int型数据可指定最大、最小有效值, key分别为max/min。 可以指定取值列表, key为in
 * 5. string型数据可指定长度的 最大、最小有效值, 均以UTF-8编码计算长度, key分别为max/min。可以指定取值列表, key为in。可指定验证正则表达式, key为match
 * 6. array型数据可指定元素个数的最大、最小值, key分别为maxCount/minCount
 * 7. array型数据可以为每个元素指定类型,key为subtype, 根据subtype值不同, 可分别指定上边的条件, 来验证数组的每个值
 * 8. array型数据可以为特定的元素指定针对该元素的验证规则, 规则如上
 * 9. array型数据可以指定某一个元素是否是必须的, key为isRequired。
 */

class Validator
{
    public static $VALID_TYPE = ['bool' => 'boolean', 'int' => 'integer', 'float' => 'double', 'string' => 'string', 'array' => 'array', 'object' => 'object'];

    /** 验证参数入口函数
     * @param $schema
     * @param $value
     * @param $err_msg
     * @return bool
     */
    public static function validate($schema, $value, &$err_msg)
    {
        //1. 是否指定了用户验证函数, 若有合法指定, 使用用户函数验证, 忽略其它条件
        if(!empty($schema['func']) && is_callable($schema['func']))
        {
            $userResult = $schema['func']($value, $err_msg);
            if($userResult !== true)
            {
                $err_msg = 'User function validate return is not true , err msg:'.strval($err_msg).', return:'.var_export($userResult, 1);
                return false;
            }
            return true;
        }
        //2. 检查type是否合法
        if(!isset($schema['type']) || !in_array($schema['type'], array_keys(self::$VALID_TYPE)))
        {
            $err_msg = 'Invalid schema, type is empty or error';
            return false;
        }
        //3. 验证传入的值类型, 并调用相应的类型验证函数
        if(gettype($value) != self::$VALID_TYPE[$schema['type']])
        {
            $err_msg = 'Invalid type, the value must be '.$schema['type'];
            return false;
        }

        $func = $schema['type'].'Validator';
        return self::$func($schema, $value, $err_msg);
    }

    /** bool类型数据的验证
     * @param $schema
     * @param $value
     * @param $err_msg
     * @return bool
     */
    public static function boolValidator($schema, $value, &$err_msg)
    {
        if(!is_bool($value))
        {
            return false;
        }
        return true;
    }

    /** int型数据验证
     * @param $schema
     * @param $value
     * @param $err_msg
     * @return bool
     */
    public static function intValidator($schema, $value, &$err_msg)
    {
        //1. 类型验证
        if(!is_int($value))
        {
            return false;
        }
        //2. 验证最小值条件
        if(isset($schema['min']) && is_numeric($schema['min']) && $value < $schema['min'])
        {
            $err_msg = 'The value must not less than '.$schema['min'];
            return false;
        }
        //3. 验证最大值条件
        if(isset($schema['max']) && is_numeric($schema['max']) && $value > $schema['max'])
        {
            $err_msg = 'The value must not greater than '.$schema['max'];
            return false;
        }
        //4. 验证in条件
        if(!empty($schema['in']) && is_array($schema['in']))
        {
            if(!in_array($value, $schema['in']))
            {
                $err_msg = 'The value must in the list:'.implode(',', $schema['in']);
                return false;
            }
        }

        return true;
    }

    /** float型数据验证, 目前暂时与int型验证规则一致
     * @param $schema
     * @param $value
     * @param $err_msg
     * @return bool
     */
    public static function floatValidator($schema, $value, &$err_msg)
    {
        //1. 类型验证
        if(!is_float($value))
        {
            return false;
        }
        //2. 验证最小值条件
        if(isset($schema['min']) && is_numeric($schema['min']) && $value < $schema['min'])
        {
            $err_msg = 'The value must not less than '.$schema['min'];
            return false;
        }
        //3. 验证最大值条件
        if(isset($schema['max']) && is_numeric($schema['max']) && $value > $schema['max'])
        {
            $err_msg = 'The value must not greater than '.$schema['max'];
            return false;
        }
        //4. 验证in条件
        if(!empty($schema['in']) && is_array($schema['in']))
        {
            if(!in_array($value, $schema['in']))
            {
                $err_msg = 'The value must in the list:'.implode(',', $schema['in']);
                return false;
            }
        }
        return true;
    }

    /** string型数据验证
     * @param $schema
     * @param $value
     * @param $err_msg
     * @return bool
     */
    public static function stringValidator($schema, $value, &$err_msg)
    {
        //1. string类型验证
        if(!is_string($value))
        {
            return false;
        }
        //2. 验证长度最小值条件
        if(isset($schema['min']) && is_numeric($schema['min']) && mb_strlen($value,"utf-8") < intval($schema['min']))
        {
            $err_msg = 'The string length must not less than '.intval($schema['min']);
            return false;
        }
        //3. 验证长度最大值条件
        if(isset($schema['max']) && is_numeric($schema['max']) && mb_strlen($value,"utf-8") > intval($schema['max']))
        {
            $err_msg = 'The string length must not greater than '.intval($schema['max']);
            return false;
        }
        //4. 验证in条件
        if(!empty($schema['in']) && is_array($schema['in']) && !in_array($value, $schema['in']))
        {
            $err_msg = 'The string value must in the list:'.implode(',', $schema['in']);
            return false;

        }
        //5. 验证正则条件
        if(!empty($schema['match']) && is_string($schema['match']))
        {
            $match = $schema['match'];
            if(substr($schema['match'], 0, 1) !== '/')
            {
                $match = '/'.$match;
            }
            if(substr($schema['match'], -1, 1) !== '/')
            {
                $match = $match.'/';
            }
            if (!@preg_match($match, $value))
            {
                $err_msg = 'The string value must match the regex:' . $schema['match'];
                return false;
            }
        }

        return true;
    }

    /** array型数据验证
     * @param $schema
     * @param $value
     * @param $err_msg
     * @return bool
     */
    public static function arrayValidator($schema, $value, &$err_msg)
    {
        //1. array类型验证
        if(!is_array($value))
        {
            return false;
        }
        //2. 验证数组元素个数最少值条件
        if(isset($schema['minCount']) && is_numeric($schema['minCount']) && count($value) < $schema['minCount'])
        {
            $err_msg = 'The array count must not less than '.$schema['minCount'];
            return false;
        }
        //3. 验证数组元素个数最大值条件
        if(isset($schema['maxCount']) && is_numeric($schema['maxCount']) && count($value) > intval($schema['maxCount']))
        {
            $err_msg = 'The array count must not greater than '.intval($schema['maxCount']);
            return false;
        }
        //4. 验证针对特定数组元素的验证条件
        if(!empty($schema['subSchema']))
        {
            foreach($schema['subSchema'] as $k => $v)
            {
                //是否是必须有的元素
                if(isset($v['isRequired']) && $v['isRequired'] == true && !isset($value[$k]))
                {
                    $err_msg = 'Missing required value, "'.$k.'" is required';
                    return false;
                }
                //如果有用户自定义验证函数, 或type有指定, 递归调用验证
                if(isset($value[$k]) && (!empty($v['type']) || (!empty($v['func']) && is_callable($v['func']))))
                {
                    $subValidator = Validator::validate($v, $value[$k], $err_msg);
                    if($subValidator === false)
                    {
                        return false;
                    }
                }
            }
        }
        //5. 验证针对每个数组元素的验证条件
        if(!empty($schema['subtype']))
        {
            //5.1 每个数组元素类型的验证
            if(!in_array($schema['subtype'], array_keys(self::$VALID_TYPE)))
            {
                $err_msg = 'Invalid schema, subtype is error';
                return false;
            }
            $subSchema = $schema;
            $subSchema['type'] = $schema['subtype'];
            unset($subSchema['subtype']);
            unset($subSchema['subSchema']);
            foreach($value as $k => $v)
            {
                if(isset($schema['subSchema'][$k]))
                {
                    //如果已经针对特定的元素进行了验证, 并且元素type值与外层subtype值不一样, 无需再验证
                    if(!empty($schema['subSchema'][$k]['type']) && $schema['subSchema'][$k]['type'] != $schema['subtype'])
                    {
                        continue;
                    }
                    //如果针对特定元素进行了验证, 并且没有显示指定“需要外层验证”, 无需再验证
                    if(!isset($schema['subSchema'][$k]['validateParent']) || $schema['subSchema'][$k]['validateParent'] != true)
                    {
                        continue;
                    }
                }
                //递归调用验证每个元素
                $subValidator = Validator::validate($subSchema, $v, $err_msg);
                if($subValidator === false)
                {
                    $err_msg = 'Array value validate error: '.$err_msg;
                    return false;
                }
            }
        }

        return true;
    }

    /** object型数据验证
     * @param $schema
     * @param $value
     * @param $err_msg
     * @return bool
     */
    public static function objectValidator($schema, $value, &$err_msg)
    {
        if(!is_object($value))
        {
            return false;
        }
        return true;
    }
}
