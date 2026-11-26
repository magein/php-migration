# 附件 / 文件表

上传文件记录：文件名、原始名、路径、大小、后缀、mime、hash。

**表名：`file`**

```php
$builder = $this->builder('附件文件表');
$builder->id();
$builder->string('filename', '文件名称', 150);
$builder->string('origin_name', '原名称', 150);
$builder->string('filepath', '文件路径');
$builder->bigInteger('filesize', '文件大小')->default(0);
$builder->string('ext', '文件后缀', 16)->default('');
$builder->string('mime', 'mime类型', 60)->default('');
$builder->string('hash', '哈希值')->default('');
$this->create($builder);
```
