# 文章表

内容发布：标题、来源、分类、作者、简介、正文、缩略图、发布时间，以及推荐/热门/发布标记与互动计数。

**表名：`article`**

> 计数与开关列都给了 `->default(0)`；`integer()` 的第三参是 `signed`，不要拿它当默认值。

```php
$builder = $this->builder('文章表');
$builder->id();
$builder->article->title();
$builder->tinyInteger('from', '来源')->default(1);
$builder->string('cate_id', '所属分类')->default('');
$builder->article->author();
$builder->article->intro();
$builder->article->content();
$builder->image->thumb();
$builder->string('source_url', '转载地址');
$builder->dateTime('published_at', '发布时间');
$builder->basic->sort();
$builder->flag->isOriginal();
$builder->flag->isHot();
$builder->flag->isRecommend();
$builder->flag->isPublished();
$builder->tinyInteger('open_comment', '开启评论 0 禁止 1 开启')->default(0);
$builder->integer('read_num', '阅读数量')->default(0);
$builder->integer('collect_num', '收藏数量')->default(0);
$builder->integer('star_num', '点赞数量')->default(0);
$builder->integer('comment_num', '评论数量')->default(0);
$this->create($builder);
```
