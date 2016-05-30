# Pandalog - php日志组件

## 安装
```bash
$ composer require pandalog/pandalog
```

## 简易用法
```php
<?php

use Pandalog\Logger;
use Pandalog\Handler\FileHandler;

// 创建日志对象
$log = new Logger('name', new FileHandler('path/to/your.log'));

// 支持 debug、info、notice、warning、error、critical、alert、emergency
$log->info('info');

// 更简洁的用法
$params = [
    'name' => 'test',
    'path' => '/tmp/test.log',
    'product' => 'supply', //产品名称
    'module'  => 'order' //产品模块
];
Logger::quickInit($params)->warning('test');
```

## 高级用法

```php
<?php

use Pandalog\Logger;
use Pandalog\Handler\FileHandler;
use Pandalog\Formatter\JsonFormatter;
use Pandalog\Processor\PushProcessor;
use Pandalog\Processor\LogIdProcessor;

// 创建日志对象
$log = new Logger('name');

// 生成驱动
$handler = new FileHander();
$handler->useDaily('/tmp/test.log'); // 设置日志切割 每天切割一次
$handler->setLink('/tmp/test.log'); // 设置日志路径 不切割 setLink useDaily 二选一
$handler->setLock(false); // 关闭文件锁 默认开启
$handler->setFormatter(new JsonFormatter()); //设置内容存储格式 默认是json
$handler->handle(['test' => 'test']); //也可单独写文件使用

// 注入文件驱动
$log->pushHandler($handler);

// 出入数据处理器
$log->pushProcessor(new LogIdProcessor()); // 添加logid
$log->pushProcessor(new PushProcessor(['host_ip', 'product'], ['12312', '123'])); // 自定义存储内容

// 写入日志
$log->info('info');

```
## Author

SherlockRen - <sherlock_ren@icloud.com> - <https://github.com/SherlockRen><br />
项目地址(https://github.com/SherlockRen/Pandalog)

## License

Pandalog is licensed under the MIT License - see the `LICENSE` file for details
