<?php

namespace magein\migration\field;

use magein\migration\Builder;

class MFComb
{

    public Builder $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return $this
     */
    public function category(): static
    {
        $gen = new MFGen($this->builder);

        $this->builder->id();
        $gen->name();
        $gen->parentId();
        $gen->status();
        $gen->sort();
        $gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function article(): static
    {
        $article = new MFGen($this->builder);

        $this->builder->id();
        $article->title();
        $article->from();
        $this->builder->integer('cate_id', '分类id');
        $this->builder->string('author', '作者');
        $this->builder->string('intro', '简介', 60)->default('');
        $article->content();
        $article->thumb();
        $this->builder->string('source_url', '转载地址');
        $this->builder->dateTime('published_at', '发布时间');
        $article->sort();

        $is = new MFIs($this->builder);

        $is->isOriginal();
        $is->isHot();
        $is->isRecommend();
        $is->isPublished();
        $this->builder->integerTiny('open_comment', '开启评论 0 禁止 forbid 1 开启 open', 0);
        $this->builder->integer('read_num', '阅读数量', 0);
        $this->builder->integer('collect_num', '收藏数量', 0);
        $this->builder->integer('star_num', '点赞数量', 0);
        $this->builder->integer('comment_num', '点赞数量', 0);

        return $this;
    }

    /**
     * 订单数据
     * @return $this
     */
    public function order(): static
    {
        $gen = new MFGen($this->builder);

        $order = new MFOrder($this->builder);

        $this->builder->id();
        $order->orderNo();
        $gen->from();
        $gen->channel();
        $order->buyerId();
        $order->buyerName();
        $order->buyerPhone();
        $order->receiverName();
        $order->receiverPhone();
        $order->receivedAt();
        $this->zoneAddress();
        $order->orderAmount();
        $order->goodsAmount();
        $order->discountAmount();
        $order->actualAmount();
        $order->freightAmount();
        $order->refundAmount();
        $order->refundedAt();
        $order->orderStatus();
        $order->payStatus();
        $order->paidAt();
        $order->payMethod();
        $order->couponId();
        $order->couponAmount();
        $order->invoice();
        $order->deliveryCompany();
        $order->deliveryNo();
        $order->deliveryTime();
        $order->finishedAt();

        $gen->remark();

        return $this;
    }

    /**
     * 支付订单
     * @return $this
     */
    public function payOrder(): static
    {
        $gen = new MFGen($this->builder);

        $order = new MFOrder($this->builder);

        $this->builder->id();
        $this->builder->char('pay_no', '支付编号', 28)->unique();
        $order->orderNo();
        $gen->channel();
        $this->builder->string('platform_id', '支付平台');
        $this->builder->integerTiny('scene', '场景 1 支付订单 order 2 充值 recharge')->default(1);
        $this->builder->integerTiny('user_id', '用户标识');
        $this->builder->string('subject', '交易主题')->default();
        $this->builder->string('appid', '收款账号');
        $this->builder->string('trade_no', '第三方交易编号')->default();
        $this->builder->string('trade_type', '平台交易方式 支付宝的签约产品product_code，微信的jsapi、native等')->default();
        $this->builder->integer('total_amount', '交易总金额 单位分');
        $this->builder->integer('coupon_amount', '优惠金额 单位分');
        $this->builder->text('coupon_info', '优惠信息')->nullable();
        $this->builder->integerTiny('result', '交易结果 0 等待中 pending 1 成功 success -1 失败 fail')->default(0);
        $this->builder->string('reason', '交易失败原因')->default('');
        $this->builder->string('remark', '交易备注')->default('');
        $this->builder->dateTime('notify_date', '异步通知时间')->nullable();
        $this->builder->string('notify_url', '支付完成后通知的地址')->default();
        $this->builder->string('return_url', '支付完成后跳转的地址')->default();

        return $this;

    }

    /**
     * @return $this
     */
    public function couponRule(): MFComb
    {
        $gen = new MFGen($this->builder);

        $this->builder->id();
        $gen->name('优惠卷名称');
        $gen->description();
        $gen->type('类型 1 满减 full 2 折扣 discount 3 固定金额 fixed')->default(1);
        $this->datetime();
        $gen->money('抵扣金额')->default(0);
        $this->builder->integer('min_money', '最低使用金额')->default(0);
        $this->builder->string('goods', '可用商品')->default('');
        $gen->status();

        return $this;
    }


    /**
     * @return $this
     */
    public function voice(): static
    {
        $gen = new MFGen($this->builder);

        $this->builder->id();
        $gen->type('发票类型 0 个人 personal 1 公司普通发票 company 2 专用发票 vat');
        $gen->title();
        $gen->content();
        $this->builder->string('tax', ' 纳税人识别号', 60);
        $this->builder->string('order_no', '关联订单编号')->default('');
        $this->builder->integerTiny('status', '0 未开票 pending 1 已开 completed 2 作废 void')->default(0);
        $gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function business(): static
    {
        $gen = new MFGen($this->builder);

        $this->builder->id();
        $this->builder->string('name', '企业名称');
        $this->builder->string('alias', '简称');
        $this->builder->string('code', '企业代码')->unique();
        $this->builder->integerTiny('industry', '所属行业')->default(0);
        $this->builder->integerTiny('attribute', '单位属性')->default(0);
        $gen->scale();
        $gen->contact();
        $gen->tel();
        $this->zoneAddress();
        $this->statusSort();
        $gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function employee()
    {

        $gen = new MFGen($this->builder);
        $user = new MFUser($this->builder);

        $this->builder->id();
        $this->builder->string('name', '员工名称');
        $this->builder->string('nickname', '昵称');
        $this->builder->string('code', '员工代码', 8)->unique();
        $user->avatar();
        $user->phone();
        $user->email();
        $user->gender();
        $user->birthday();
        $user->nativePlace();
        $user->ethnic();
        $user->idType();
        $user->idNumber();
        $this->zone();
        $this->builder->string('live_address', '居住地址');
        $this->statusSort();
        $gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function user(): static
    {
        $user = new MFUser($this->builder);

        $this->builder->id();
        $user->username();
        $user->password();
        $user->nickname();
        $user->avatar();
        $user->phone();
        $user->email();
        $user->gender();
        $user->birthday();

        return $this;
    }

    /**
     * 轮播图
     * @return $this
     */
    public function carousel(): static
    {
        $gen = new MFGen($this->builder);

        $this->builder->id();
        $gen->title();
        $this->builder->string('url', '文件路径 本地或者远程地址');
        $this->builder->string('redirect', '跳转地址');
        $this->builder->string('target', '打开方式')->default('_blank');
        $this->datetime();
        $this->builder->string('scene', '使用场景 多个逗号隔开');
        $gen->sort();
        $gen->status();
        $gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function file(): static
    {
        $file = new MFFile($this->builder);

        $this->builder->id();
        $file->filename();
        $file->originName();
        $file->filepath();
        $file->filesize();
        $file->ext();
        $file->mime();
        $file->hash();

        return $this;
    }

    /**
     * @return $this
     */
    public function region(): static
    {
        $gen = new MFGen($this->builder);
        $this->builder->id();
        $this->builder->string('code', '行政区划代码', 20);
        $this->builder->string('parent_code', '父级code', 20);
        $gen->name('名称');
        $this->builder->string('pinyin', '拼音', 20)->default();
        $this->builder->string('first_char', '首字母', 20)->default();
        $this->builder->integerTiny('level', '层级 1 省 province 2 市 city 3 县/区 area')->default(1);
        $this->statusSort();

        return $this;
    }

    /**
     * @return $this
     */
    public function zone(): static
    {
        $address = new MFAddr($this->builder);

        $address->province();
        $address->city();
        $address->district();

        return $this;
    }

    /**
     * @return $this
     */
    public function zoneAddress(): static
    {
        $address = new MFAddr($this->builder);

        $address->province();
        $address->city();
        $address->district();
        $address->address();
        $address->location();

        return $this;
    }

    /**
     * @return $this
     */
    public function userAddress(): static
    {
        $address = new MFAddr($this->builder);

        $address->builder->integer('user_id', '用户id');
        $address->builder->string('name', '收货人');
        $address->builder->string('phone', '联系电话', 18);
        $address->province();
        $address->city();
        $address->district();
        $address->address();
        $address->postcode();
        $address->location();
        $address->builder->integerTiny('is_default', '默认地址')->default(0);

        return $this;
    }

    /**
     * @return $this
     */
    public function bank(): static
    {
        $gen = new MFGen($this->builder);
        $this->builder->id();
        $gen->name('银行名称');
        $this->builder->string('alias', '别名');
        $this->builder->string('icon', '图标')->default();
        $this->builder->string('first_letter', '首字母');
        $this->statusSort();

        return $this;
    }

    /**
     * @return $this
     */
    public function industry(): static
    {
        $gen = new MFGen($this->builder);
        $this->builder->id();
        $gen->parentId();
        $gen->name('行业名称');
        $this->builder->string('alias', '别名');
        $this->statusSort();

        return $this;
    }

    /**
     * 状态排序
     * @return $this
     */
    public function statusSort(): static
    {
        $gen = new MFGen($this->builder);

        $gen->status();
        $gen->sort();

        return $this;
    }

    /**
     * 时间
     * @return $this
     */
    public function datetime(): static
    {
        $gen = new MFGen($this->builder);

        $gen->beginTime();
        $gen->endTime();

        return $this;
    }
}