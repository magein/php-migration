<?php

use magein\migration\Migrator;

/**
 * 建表：全字段类型测试表
 *
 * 覆盖 Builder 的全部列类型原语，一个方法对应一列，用来核对最终生成的 SQL 类型。
 * 环境：MySQL >= 5.7.8（json）、空间类型 >= 5.7；表使用 InnoDB + utf8mb4。
 *
 * 两组方法写成文件末尾的注释，没有直接启用：
 *   - bigIncrements() / integerIncrements()：一张表只允许一个 AUTO_INCREMENT 列，本表用 id() 生成主键；
 *   - 5 个 multi* / geometryCollection()：类型串不在 Phinx MysqlAdapter::getSqlType() 映射内，
 *     启用会抛 UnsupportedColumnTypeException（与 MySQL 版本无关）。
 */
class CreateAllTypes extends Migrator
{
    protected $name = 'all_types';

    public function change()
    {
        $builder = $this->builder('全字段类型测试表');
        $builder->innoDB();
        $builder->setCollation('utf8mb4_unicode_ci');
        $builder->id();

        // 标识与枚举
        $builder->uuid('uuid_col', 'UUID');
        $builder->enum('enum_col', ['draft', 'published', 'archived']);

        // 整数
        $builder->unsignedBigInteger('ubig_col', '无符号大整数');
        $builder->bigInteger('big_col', '大整数');
        $builder->unsignedInteger('uint_col', '无符号整数');
        $builder->integer('int_col', '整数');
        $builder->tinyInteger('tiny_int_col', '微小整数');
        $builder->smallInteger('small_int_col', '小整数');
        $builder->mediumInteger('medium_int_col', '中整数');

        // 字符串、文本、JSON 与二进制
        $builder->char('char_col', '定长字符串', 32);
        $builder->string('string_col', '变长字符串', 64);
        $builder->text('text_col', '正文');
        $builder->tinyText('tiny_text_col', '短文本');
        $builder->mediumText('medium_text_col', '中文本');
        $builder->longText('long_text_col', '长文本');
        $builder->json('json_col', '扩展数据');
        $builder->blob('blob_col', '二进制内容');

        // 小数与布尔
        $builder->float('float_col', '单精度');
        $builder->floatUnsigned('float_unsigned_col', '单精度无符号');
        $builder->double('double_col', '双精度');
        $builder->doubleUnsigned('double_unsigned_col', '双精度无符号');
        $builder->decimal('decimal_col', '定点数');
        $builder->decimalUnsigned('decimal_unsigned_col', '定点数无符号');
        $builder->boolean('bool_col', '布尔');

        // 日期与时间
        $builder->date('date_col', '日期');
        $builder->dateTime('datetime_col', '日期时间');
        $builder->time('time_col', '时间');
        $builder->timestamp('ts_col', '时间戳');
        $builder->year('year_col', '年份');

        // 空间类型（getSqlType() 已映射的 4 个）
        $builder->geometry('geometry_col', '任意几何值');
        // point() 的第三参 srid 可选，需要限定坐标系（MySQL 8.0+）时才传
        $builder->point('point_col', '坐标点');
        $builder->lineString('linestring_col', '线');
        $builder->polygon('polygon_col', '面');

        // 自增写法：与上面的 id() 二选一，启用任一行前先删掉 id()。
        // $builder->integerIncrements('id', 'int 自增主键');
        // $builder->bigIncrements('id', 'bigint 自增主键');

        // 以下 5 个类型串 Phinx 未映射，启用会在 addColumn() 阶段被拒：
        // $builder->geometryCollection('geometrycollection_col', '几何集合');
        // $builder->multiPoint('multipoint_col', '多点');
        // $builder->multiLineString('multilinestring_col', '多线');
        // $builder->multiPolygon('multipolygon_col', '多面');
        // $builder->multiPolygonZ('multipolygonz_col', '带 Z 坐标的多面');

        $this->create($builder);
    }
}
