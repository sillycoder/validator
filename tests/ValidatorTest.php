<?php

/**
 * @file    ValidatorTest.php
 * @author  jwviva(@sina.com)
 * @date    2018/2/25 11:34
 * @brief   使用方法: 命令行执行 phpunit --bootstrap ./Validator.php  tests/ValidatorTest
 */

class ValidatorTest extends PHPUnit_Framework_TestCase
{

    /** 输入正确数据, 验证函数返回, 断言true
     * @param $schema
     * @param $value
     * @dataProvider validDataProvider
     * @covers Validator::validate
     */
    public function testValidateTrue($schema, $value)
    {
        $this->assertTrue(Validator::validate($schema, $value, $msg));
    }

    /** 提供正确数据
     * @return array
     */
    public function validDataProvider()
    {
        return [
            [['type'=>'int'],10],
            [['type'=>'int'],-10000010],
            [['type'=>'int','min'=>10],10],
            [['type'=>'int','min'=>-10],0],
            [['type'=>'int','max'=>10],10],
            [['type'=>'int','in'=>[2,4,6,8,10]],10],
            [['type'=>'float'],19.9],
            [['type'=>'float','min'=>10.1],19.9],
            [['type'=>'float','max'=>10.1],1.1],
            [['type'=>'float','in'=>[2.1,4,'a4',8]],2.1],
            [['type'=>'bool'],true],
            [['type'=>'bool'],false],
            [['type'=>'bool','max'=>100],true],
            [['type'=>'object'],$this],
            [['type'=>'object'],new ArrayObject()],
            [['type'=>'string'],'1'],
            [['type'=>'string','max'=>2],'你好'],
            [['type'=>'string','min'=>4],'test'],
            [['type'=>'string','min'=>4,'max'=>4],'test'],
            [['type'=>'string','in'=>['true','false']],'true'],
            [['type'=>'string','match'=>'[a-z][a-z0-9_]+@.+\..+'],'test112@abc.cn'],
            [['type'=>'array'],[]],
            [['type'=>'array'],['one'=>1]],
            [['type'=>'array'],[100000000]],
            [['type'=>'array','maxCount'=>3],[1,2,3]],
            [['type'=>'array','maxCount'=>3],[]],
            [['type'=>'array','minCount'=>2],['a'=>1,'b'=>2]],
            [['type'=>'array','minCount'=>2],['a','b','1','2']],
            [['type'=>'array','minCount'=>4,'maxCount'=>4],['a','b','1','2']],
            [['type'=>'array','subtype'=>'string'],['true','false']],
            [['type'=>'array','subtype'=>'string','min'=>5],['macBook','Windows']],
            [['type'=>'array','subtype'=>'int','min'=>0],[10001,10002,10003]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000]]], [1001]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000]]], ['user_id'=>1001]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000,'isRequired'=>1]]], ['user_id'=>1001]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000],'enable'=>['type'=>'bool']]], ['user_id'=>1001]],
            [['type'=>'array','subSchema'=>['enable'=>['type'=>'bool','isRequired'=>true]]], ['user_id'=>1001,'enable'=>true]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000],'enable'=>['type'=>'bool','isRequired'=>true]]], ['user_id'=>1001,'enable'=>true]],
        ];
    }

    /** 输入正确数据, 验证函数返回, 断言false
     * @param $schema
     * @param $value
     * @dataProvider invalidDataProvider
     * @covers Validator::validate
     */
    public function testValidateFalse($schema, $value)
    {
        $this->assertFalse(Validator::validate($schema, $value, $msg));
    }

    /** 提供非法数据供验证
     * @return array
     */
    public function invalidDataProvider()
    {
        return [
            [['type'=>'int'],'string value'],
            [['type'=>'int'],['array value']],
            [['type'=>'int','min'=>10],9],
            [['type'=>'int','max'=>10],11],
            [['type'=>'int','in'=>[2,4,6,8]],10],
            [['type'=>'float'],9],
            [['type'=>'float','min'=>10.1],9.9],
            [['type'=>'float','max'=>10.1],11.1],
            [['type'=>'float','in'=>[2.1,4,'a4',8]],2],
            [['type'=>'string'],100],
            [['type'=>'string','max'=>2],'one'],
            [['type'=>'string','min'=>6],'test'],
            [['type'=>'string','min'=>6,'max'=>10],'this a long long string'],
            [['type'=>'string','in'=>['true','false']],'TRUE'],
            [['type'=>'string','match'=>'[a-z][a-z0-9_]+@.+\..+'],'test112#xxxx.cn'],
            [['type'=>'string','match'=>'+'],'++'],
            [['type'=>'array'],null],
            [['type'=>'array'],1],
            [['type'=>'array','maxCount'=>3],[1,2,3,4]],
            [['type'=>'array','minCount'=>2],['a'=>1]],
            [['type'=>'array','minCount'=>5],['a','b','1','2']],
            [['type'=>'array','minCount'=>4,'maxCount'=>4],['a','b']],
            [['type'=>'array','subtype'=>'string'],[true,false]],
            [['type'=>'array','subtype'=>'string','max'=>5],['macBook',78]],
            [['type'=>'array','subtype'=>'int','max'=>1000],[10001,10002,10003]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000]]], ['user_id'=>'1001']],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','isRequired'=>1]]], ['uid'=>1001]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000,'isRequired'=>1],'enable'=>['type'=>'bool']]], ['user_id'=>1001,'enable'=>1]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000],'enable'=>['type'=>'bool','isRequired'=>true]]], ['user_id'=>1001,'unable'=>true]],
        ];
    }

    /** 输入正确int数据, int型验证函数的返回, 断言true
     * @param $schema
     * @param $value
     * @dataProvider intTestValidDataProvider
     * @covers Validator::intValidator
     */
    public function testIntValidatorTrue($schema, $value)
    {
        $this->assertTrue(Validator::intValidator($schema, $value, $msg));
    }

    /** 提供合法的int型数据
     * @return array
     */
    public function intTestValidDataProvider()
    {
        return [
            [['type'=>'int'],10],
            [['type'=>'int'],-10000010],
            [['type'=>'int','min'=>10],10],
            [['type'=>'int','min'=>-10],0],
            [['type'=>'int','max'=>10],10],
            [['type'=>'int','in'=>[2,4,6,8,10]],10],
        ];
    }

    /** 输入不正确int数据, int型验证函数的返回, 断言false
     * @param $schema
     * @param $value
     * @dataProvider intTestInvalidDataProvider
     * @covers Validator::intValidator
     */
    public function testIntValidatorFalse($schema, $value)
    {
        $this->assertFalse(Validator::intValidator($schema, $value, $msg));
    }

    /** 提供不合法的int型数据
     * @return array
     */
    public function intTestInvalidDataProvider()
    {
        return [
            [['type'=>'int'],'string'],
            [['type'=>'int','min'=>10],9],
            [['type'=>'int','max'=>10],11],
            [['type'=>'int','in'=>[2,4,6,8]],10],
        ];
    }

    /** 输入正确的float型数据, float型验证函数的返回, 断言true
     * @param $schema
     * @param $value
     * @dataProvider floatTestValidDataProvider
     * @covers Validator::floatValidator
     */
    public function testFloatValidatorTrue($schema, $value)
    {
        $this->assertTrue(Validator::floatValidator($schema, $value, $msg));
    }

    /** 提供合法的float型数据
     * @return array
     */
    public function floatTestValidDataProvider()
    {
        return [
            [['type'=>'float'],19.9],
            [['type'=>'float','min'=>10.1],19.9],
            [['type'=>'float','max'=>10.1],1.1],
            [['type'=>'float','in'=>[2.1,4,'a4',8]],2.1],
        ];
    }

    /** 输入不合法的float型数据, float型验证函数的返回, 断言false
     * @param $schema
     * @param $value
     * @dataProvider floatTestInvalidDataProvider
     * @covers Validator::floatValidator
     */
    public function testFloatValidatorFalse($schema, $value)
    {
        $this->assertFalse(Validator::floatValidator($schema, $value, $msg));
    }

    /** 提供不合法的float型数据
     * @return array
     */
    public function floatTestInvalidDataProvider()
    {
        return [
            [['type'=>'float'],1],
            [['type'=>'float','min'=>10.1],9.9],
            [['type'=>'float','max'=>10.1],11.1],
            [['type'=>'float','in'=>[2.1,4,'a4',8]],2],
        ];
    }

    /** 输入正确的bool型数据, bool型验证函数的返回, 断言true
     * @param $schema
     * @param $value
     * @dataProvider boolTestValidDataProvider
     * @covers Validator::boolValidator
     */
    public function testBoolValidatorTrue($schema, $value)
    {
        $this->assertTrue(Validator::boolValidator($schema, $value, $msg));
    }

    /** 提供合法的bool型数据
     * @return array
     */
    public function boolTestValidDataProvider()
    {
        return [
            [['type'=>'bool'],true],
            [['type'=>'bool'],false],
            [['type'=>'bool','max'=>100],true],
        ];
    }

    /** 输入不正确的bool型数据, bool型验证函数的返回, 断言false
     * @param $schema
     * @param $value
     * @dataProvider boolTestInvalidDataProvider
     * @covers Validator::boolValidator
     */
    public function testBoolValidatorFalse($schema, $value)
    {
        $this->assertFalse(Validator::boolValidator($schema, $value, $msg));
    }

    /** 提供不合法的bool型数据
     * @return array
     */
    public function boolTestInvalidDataProvider()
    {
        return [
            [['type'=>'bool'],1],
            [['type'=>'bool'],0],
        ];
    }

    /** 输入正确的object型数据, object型验证函数的返回, 断言true
     * @param $schema
     * @param $value
     * @dataProvider objectTestValidDataProvider
     * @covers Validator::objectValidator
     */
    public function testObjectValidatorTrue($schema, $value)
    {
        $this->assertTrue(Validator::objectValidator($schema, $value, $msg));
    }

    /** 提供合法的object型数据
     * @return array
     */
    public function objectTestValidDataProvider()
    {
        return [
            [['type'=>'object'],$this],
            [['type'=>'object'],new ArrayObject()],
        ];
    }

    /** 输入不合法的object型数据, object型验证函数的返回, 断言false
     * @param $schema
     * @param $value
     * @dataProvider objectTestInvalidDataProvider
     * @covers Validator::objectValidator
     */
    public function testObjectValidatorFalse($schema, $value)
    {
        $this->assertFalse(Validator::objectValidator($schema, $value, $msg));
    }

    /** 提供不合法的object型数据
     * @return array
     */
    public function objectTestInvalidDataProvider()
    {
        return [
            [['type'=>'object'],[]],
            [['type'=>'object'],'string'],
        ];
    }

    /** 输入正确的string型数据, string型验证函数的返回, 断言true
     * @param $schema
     * @param $value
     * @dataProvider stringTestValidDataProvider
     * @covers Validator::stringValidator
     */
    public function testStringValidatorTrue($schema, $value)
    {
        $this->assertTrue(Validator::stringValidator($schema, $value, $msg));
    }

    /** 提供正常的string型数据
     * @return array
     */
    public function stringTestValidDataProvider()
    {
        return [
            [['type'=>'string'],'1'],
            [['type'=>'string','max'=>2],'百度'],
            [['type'=>'string','min'=>4],'test'],
            [['type'=>'string','min'=>4,'max'=>4],'test'],
            [['type'=>'string','in'=>['true','false']],'true'],
            [['type'=>'string','match'=>'[a-z][a-z0-9_]+@.+\..+'],'test112@xxx.cn'],
        ];
    }

    /** 输入不合法的string型数据, string型验证函数的返回, 断言false
     * @param $schema
     * @param $value
     * @dataProvider stringTestInvalidDataProvider
     * @covers Validator::stringValidator
     */
    public function testStringValidatorFalse($schema, $value)
    {
        $this->assertFalse(Validator::stringValidator($schema, $value, $msg));
    }

    /** 提供非法的string型数据
     * @return array
     */
    public function stringTestInvalidDataProvider()
    {
        return [
            [['type'=>'string','max'=>2],'one'],
            [['type'=>'string','min'=>6],'百度'],
            [['type'=>'string','min'=>6,'max'=>10],'this a long long string'],
            [['type'=>'string','in'=>['true','false']],'TRUE'],
            [['type'=>'string','match'=>'[a-z][a-z0-9_]+@.+\..+'],'test112#xyz.cn'],
            [['type'=>'string','match'=>'+'],'++'],
        ];
    }


    /** 输入正确的array型数据, array型验证函数的返回, 断言true
     * @param $schema
     * @param $value
     * @dataProvider arrayTestValidDataProvider
     * @covers Validator::arrayValidator
     */
    public function testArrayValidatorTrue($schema, $value)
    {
        $this->assertTrue(Validator::arrayValidator($schema, $value, $msg));
    }

    /** 提供正确的array型数据
     * @return array
     */
    public function arrayTestValidDataProvider()
    {
        return [
            [['type'=>'array'],[]],
            [['type'=>'array'],['one'=>1]],
            [['type'=>'array'],[100000000]],
            [['type'=>'array','maxCount'=>3],[1,2,3]],
            [['type'=>'array','maxCount'=>3],[]],
            [['type'=>'array','minCount'=>2],['a'=>1,'b'=>2]],
            [['type'=>'array','minCount'=>2],['a','b','1','2']],
            [['type'=>'array','minCount'=>4,'maxCount'=>4],['a','b','1','2']],
            [['type'=>'array','subtype'=>'string'],['true','false']],
            [['type'=>'array','subtype'=>'string','min'=>5],['macBook','Windows']],
            [['type'=>'array','subtype'=>'int','min'=>0],[10001,10002,10003]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000]]], [1001]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000]]], ['user_id'=>1001]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000,'isRequired'=>1]]], ['user_id'=>1001]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000],'enable'=>['type'=>'bool']]], ['user_id'=>1001]],
            [['type'=>'array','subSchema'=>['enable'=>['type'=>'bool','isRequired'=>true]]], ['user_id'=>1001,'enable'=>true]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000],'enable'=>['type'=>'bool','isRequired'=>true]]], ['user_id'=>1001,'enable'=>true]],
        ];
    }

    /** 输入不正确的array型数据, array型验证函数的返回, 断言false
     * @param $schema
     * @param $value
     * @dataProvider arrayTestInvalidDataProvider
     * @covers Validator::arrayValidator
     */
    public function testArrayValidatorFalse($schema, $value)
    {
        $this->assertFalse(Validator::arrayValidator($schema, $value, $msg));
    }

    /** 提供非法的array型数据
     * @return array
     */
    public function arrayTestInvalidDataProvider()
    {
        return [
            [['type'=>'array'],null],
            [['type'=>'array'],1],
            [['type'=>'array','maxCount'=>3],[1,2,3,4]],
            [['type'=>'array','minCount'=>2],['a'=>1]],
            [['type'=>'array','minCount'=>5],['a','b','1','2']],
            [['type'=>'array','minCount'=>4,'maxCount'=>4],['a','b']],
            [['type'=>'array','subtype'=>'string'],[true,false]],
            [['type'=>'array','subtype'=>'string','max'=>5],['macBook',78]],
            [['type'=>'array','subtype'=>'int','max'=>1000],[10001,10002,10003]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000]]], ['user_id'=>'1001']],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','isRequired'=>1]]], ['uid'=>1001]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000,'isRequired'=>1],'enable'=>['type'=>'bool']]], ['user_id'=>1001,'enable'=>1]],
            [['type'=>'array','subSchema'=>['user_id'=>['type'=>'int','min'=>1000],'enable'=>['type'=>'bool','isRequired'=>true]]], ['user_id'=>1001,'unable'=>true]],
        ];
    }
}
