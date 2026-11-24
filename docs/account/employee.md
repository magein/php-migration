# 员工表

人员档案：基本信息、证件（含正反面）、籍贯民族、居住地址、状态排序。

**表名：`employee`**

```php
$builder = $this->builder('员工表');
$builder->id();
$builder->basic->name('员工名称');
$builder->user->nickname('昵称');
$builder->string('code', '员工代码', 12);
$builder->user->avatar();
$builder->contact->phone();
$builder->contact->email();
$builder->user->gender();
$builder->user->birthday();
$builder->user->nativePlace();
$builder->user->ethnicId();
$builder->user->idType();
$builder->user->idNumber();
$builder->user->idFront();
$builder->user->idBack();
$builder->addr->province();
$builder->addr->city();
$builder->addr->district();
$builder->string('live_address', '居住地址');
$builder->basic->status();
$builder->basic->sort();
$builder->basic->remark();
$this->create($builder);
```
