# 员工表

人员档案：基本信息、证件（含正反面）、籍贯民族、居住地址、状态排序。

**表名：`employee`**

```php
$builder = $this->builder('员工表');
$builder->id();
$builder->string('name', '员工名称', 60);
$builder->string('nickname', '昵称', 60)->default('');
$builder->string('code', '员工代码', 12);
$builder->string('avatar', '头像')->default('');
$builder->char('phone', '手机号码', 11);
$builder->string('email', '邮箱地址', 60)->default('');
$builder->enum('gender', ['0', '1', '2'])->comment('性别 0 未知 secret 1 男 man 2 女 woman')->default('0');
$builder->date('birthday', '出生年月')->nullable();
$builder->string('native_place', '籍贯', 60)->default('');
$builder->tinyInteger('ethnic_id', '民族')->default(1);
$builder->tinyInteger('id_type', '证件类型 1 身份证 id 2 港澳通行证 hk_macao 3 tai_wan  4 护照 passport 99 其他 other')->default(1);
$builder->string('id_number', '证件号码', 30)->default('');
$builder->string('id_front', '证件正面', 60)->default('');
$builder->string('id_back', '证件反面', 60)->default('');
$builder->string('province', '省份', 30)->default('');
$builder->string('city', '城市', 30)->default('');
$builder->string('district', '县区', 30)->default('');
$builder->string('live_address', '居住地址');
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->smallInteger('sort', '排序 正序')->default(99);
$builder->string('remark', '备注')->default('');
$this->create($builder);
```
