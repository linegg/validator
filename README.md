# validator - 快速参数验证器

## 说明

- 3行代码完成多维度参数检查
- 简洁轻量，但却能在验证参数时省去很多时间
- 只包含number，string，boolean三种类型验证
- PHP >= 7.0

## 安装
```
composer require linegg/validator
```

## 样例
```php
<?php
// 当然必须use
use Linegg\Validator\Validator;

$w = new Validator();
// 对两个key设置规则
$w->setRule('test1')->setType('number')->setNumRange(1,3)->notEmpty()->setLength(1,3)->setRegex('/x/');
$w->setRule('test2')->setType('number')->setNumRange(1,3)->notEmpty()->setLength(1,3)->setRegex('/x/');

// 然后就可以验证参数
$w->validate(['test1' => '1', 'test2' => 9]);
// 单个参数验证
$w->validate(9, 'test1');

// 或者也可以这样
// 在数组中依然找到key进行验证
$w->validate(['b' => [0 => ['test1' => '1', 'test2' => 9], 1]]);

// 使用isError和getErrors获取是否错误和错误信息
var_dump($w->isError());
var_dump($w->getErrors());

/** 它们打印的格式如下
bool(true)
Array
(
    [test1] => value can not match regex.
    [test2] => value is bigger than 3
)
*/

// 默认getErrors()只保留最近一次validate()的错误
// 如果你希望多次validate()的错误积累，可以使用
$w->emptyError = false;

