# 附件 / 文件表

上传文件记录：文件名、原始名、路径、大小、后缀、mime、hash。

**表名：`file`**

```php
$builder = $this->builder('附件文件表');
$builder->id();
$builder->file->filename();
$builder->file->originName();
$builder->file->filepath();
$builder->file->filesize();
$builder->string('ext', '文件后缀', 16)->default('');
$builder->file->mime();
$builder->file->hash();
$this->create($builder);
```
