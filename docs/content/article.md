# 文章表

内容发布：标题、来源、分类、作者、简介、正文、缩略图、发布时间，以及推荐/热门/发布标记与互动计数。

**表名：`article`**

> 计数与开关列都给了 `->default(0)`；`integer()` 的第三参是 `signed`，不要拿它当默认值。

```php
$builder = $this->builder('文章表');
$builder->id();
$builder->string('title', '标题', 30);
$builder->tinyInteger('from', '来源')->default(1);
$builder->string('cate_id', '所属分类')->default('');
$builder->string('author', '作者', 30)->default('');
$builder->string('intro', '简介', 140)->default('');
$builder->text('content', '内容')->nullable();
$builder->string('thumb', '缩略图')->default('');
$builder->string('source_url', '转载地址');
$builder->dateTime('published_at', '发布时间');
$builder->smallInteger('sort', '排序 正序')->default(99);
$builder->tinyInteger('is_original', '是否原创 0 否 no 1 是 yes')->default(0);
$builder->tinyInteger('is_hot', '是否热门 0 否 no 1 是 yes')->default(0);
$builder->tinyInteger('is_recommend', '是否推荐 0 否 no 1 是 yes')->default(0);
$builder->tinyInteger('is_published', '是否发布 0 否 no 1 是 yes')->default(0);
$builder->tinyInteger('open_comment', '开启评论 0 禁止 1 开启')->default(0);
$builder->integer('read_num', '阅读数量')->default(0);
$builder->integer('collect_num', '收藏数量')->default(0);
$builder->integer('star_num', '点赞数量')->default(0);
$builder->integer('comment_num', '评论数量')->default(0);
$this->create($builder);
```
