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
        $this->builder->id();
        $this->builder->gen->name();
        $this->builder->gen->parentId();
        $this->builder->gen->status();
        $this->builder->gen->sort();
        $this->builder->gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function article(): static
    {
        $this->builder->id();
        $this->builder->gen->title();
        $this->builder->gen->from();
        $this->builder->gen->cateIdMultiple();
        $this->builder->gen->author();
        $this->builder->gen->intro();
        $this->builder->gen->content();
        $this->builder->gen->thumb();
        $this->builder->string('source_url', '转载地址');
        $this->builder->dateTime('published_at', '发布时间');
        $this->builder->gen->sort();

        $this->builder->is->isOriginal();
        $this->builder->is->isHot();
        $this->builder->is->isRecommend();
        $this->builder->is->isPublished();
        $this->builder->integerTiny('open_comment', '开启评论 0 禁止 forbid 1 开启 open', 0);
        $this->builder->integer('read_num', '阅读数量', 0);
        $this->builder->integer('collect_num', '收藏数量', 0);
        $this->builder->integer('star_num', '点赞数量', 0);
        $this->builder->integer('comment_num', '评论数量', 0);

        return $this;
    }

    /**
     * 订单数据
     * @return $this
     */
    public function order(): static
    {
        $this->builder->id();
        $this->builder->order->orderNo();
        $this->builder->gen->from();
        $this->builder->gen->channel();
        $this->builder->order->buyerId();
        $this->builder->order->buyerName();
        $this->builder->order->buyerPhone();
        $this->builder->order->receiverName();
        $this->builder->order->receiverPhone();
        $this->builder->order->receivedAt();
        $this->zoneAddress();
        $this->builder->order->orderAmount();
        $this->builder->order->goodsAmount();
        $this->builder->order->discountAmount();
        $this->builder->order->actualAmount();
        $this->builder->order->freightAmount();
        $this->builder->order->refundAmount();
        $this->builder->order->refundedAt();
        $this->builder->order->orderStatus();
        $this->builder->order->payStatus();
        $this->builder->order->paidAt();
        $this->builder->order->payMethod();
        $this->builder->order->couponId();
        $this->builder->order->couponAmount();
        $this->builder->order->invoice();
        $this->builder->order->deliveryCompany();
        $this->builder->order->deliveryNo();
        $this->builder->order->deliveryTime();
        $this->builder->order->finishedAt();
        $this->builder->gen->remark();

        return $this;
    }

    /**
     * 支付订单
     * @return $this
     */
    public function payOrder(): static
    {
        $this->builder->id();
        $this->builder->order->payNo();
        $this->builder->order->orderNo();
        $this->builder->gen->channel();
        $this->builder->gen->parentId('支付平台');
        $this->builder->gen->scene('场景 1 支付订单 order 2 充值 recharge');
        $this->builder->user->userId();
        $this->builder->string('subject', '交易主题')->default();
        $this->builder->string('appid', '收款账号');
        $this->builder->order->tradeNo();
        $this->builder->string('trade_type', '平台交易方式 支付宝的签约产品product_code，微信的jsapi、native等')->default();
        $this->builder->integer('total_amount', '交易总金额 单位分');
        $this->builder->integer('coupon_amount', '优惠金额 单位分');
        $this->builder->text('coupon_info', '优惠信息')->nullable();
        $this->builder->gen->result('交易结果 0 等待中 pending 1 成功 success -1 失败 fail');
        $this->builder->gen->reason('交易失败原因');
        $this->builder->gen->remark('交易备注');
        $this->builder->dateTime('notified_at', '异步通知时间')->nullable();
        $this->builder->string('notify_url', '支付完成后通知的地址')->default();
        $this->builder->string('return_url', '支付完成后跳转的地址')->default();

        return $this;
    }

    /**
     * @return $this
     */
    public function couponRule(): MFComb
    {
        $this->builder->id();
        $this->builder->gen->name('优惠卷名称');
        $this->builder->gen->description();
        $this->builder->gen->type('类型 1 满减 full 2 折扣 discount 3 固定金额 fixed')->default(1);
        $this->datetime();
        $this->builder->gen->money('抵扣金额')->default(0);
        $this->builder->integer('min_money', '最低使用金额')->default(0);
        $this->builder->string('goods', '可用商品')->default('');
        $this->builder->gen->status();

        return $this;
    }


    /**
     * @return $this
     */
    public function voice(): static
    {
        $this->builder->id();
        $this->builder->gen->type('发票类型 0 个人 personal 1 公司普通发票 company 2 专用发票 vat');
        $this->builder->gen->title();
        $this->builder->gen->content();
        $this->builder->string('tax', ' 纳税人识别号', 60);
        $this->builder->string('order_no', '关联订单编号')->default('');
        $this->builder->gen->status('0 未开票 pending 1 已开 completed 2 作废 void')->default(0);
        $this->builder->gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function business(): static
    {
        $this->builder->id();
        $this->builder->gen->name('企业名称');
        $this->builder->gen->alias('简称');
        $this->builder->gen->code('企业代码');
        $this->builder->gen->industry();
        $this->builder->gen->attribute();
        $this->builder->gen->scale();
        $this->builder->gen->contact();
        $this->builder->gen->tel();
        $this->zoneAddress();
        $this->statusSort();
        $this->builder->gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function employee(): static
    {
        $this->builder->id();
        $this->builder->gen->name('员工名称');
        $this->builder->user->nickname('昵称');
        $this->builder->gen->code('员工代码');
        $this->builder->user->avatar();
        $this->builder->user->phone();
        $this->builder->user->email();
        $this->builder->user->gender();
        $this->builder->user->birthday();
        $this->builder->user->nativePlace();
        $this->builder->user->ethnic();
        $this->builder->user->idType();
        $this->builder->user->idNumber();
        $this->zone();
        $this->builder->string('live_address', '居住地址');
        $this->statusSort();
        $this->builder->gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function user(): static
    {
        $this->builder->id();
        $this->builder->user->username();
        $this->builder->user->password();
        $this->builder->user->nickname();
        $this->builder->user->avatar();
        $this->builder->user->phone();
        $this->builder->user->email();
        $this->builder->user->gender();
        $this->builder->user->birthday();

        return $this;
    }

    /**
     * 轮播图
     * @return $this
     */
    public function carousel(): static
    {
        $this->builder->id();
        $this->builder->gen->title();
        $this->builder->gen->url('文件路径 本地或者远程地址');
        $this->builder->string('redirect', '跳转地址');
        $this->builder->string('target', '打开方式')->default('_blank');
        $this->datetime();
        $this->builder->gen->sceneMultiple();
        $this->builder->gen->sort();
        $this->builder->gen->status();
        $this->builder->gen->remark();

        return $this;
    }

    /**
     * @return $this
     */
    public function file(): static
    {

        $this->builder->id();
        $this->builder->file->filename();
        $this->builder->file->originName();
        $this->builder->file->filepath();
        $this->builder->file->filesize();
        $this->builder->file->ext();
        $this->builder->file->mime();
        $this->builder->file->hash();

        return $this;
    }

    /**
     * @return $this
     */
    public function region(): static
    {
        $this->builder->id();
        $this->builder->gen->code('行政区划');
        $this->builder->string('parent_code', '父级code', 20);
        $this->builder->gen->name();
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
        $this->builder->addr->province();
        $this->builder->addr->city();
        $this->builder->addr->district();

        return $this;
    }

    /**
     * @return $this
     */
    public function zoneAddress(): static
    {
        $this->builder->addr->province();
        $this->builder->addr->city();
        $this->builder->addr->district();
        $this->builder->addr->address();
        $this->builder->addr->location();

        return $this;
    }

    /**
     * @return $this
     */
    public function userAddress(): static
    {
        $address = new MFAddr($this->builder);

        $address->builder->user->userId();
        $address->builder->gen->name('收货人');
        $address->builder->gen->phone('联系电话', 18);
        $this->builder->addr->province();
        $this->builder->addr->city();
        $this->builder->addr->district();
        $this->builder->addr->address();
        $this->builder->addr->postcode();
        $this->builder->addr->location();
        $address->builder->integerTiny('is_default', '默认地址')->default(0);

        return $this;
    }

    /**
     * @return $this
     */
    public function bank(): static
    {
        $this->builder->id();
        $this->builder->gen->name('银行名称');
        $this->builder->gen->alias('别名');
        $this->builder->gen->icon('图标');
        $this->builder->string('first_letter', '首字母');
        $this->statusSort();

        return $this;
    }

    /**
     * @return $this
     */
    public function industry(): static
    {
        $this->builder->id();
        $this->builder->gen->parentId();
        $this->builder->gen->name('行业名称');
        $this->builder->gen->alias('别名');
        $this->statusSort();

        return $this;
    }

    /**
     * 状态排序
     * @return $this
     */
    public function statusSort(): static
    {
        $this->builder->gen->status();
        $this->builder->gen->sort();

        return $this;
    }

    /**
     * 时间
     * @return $this
     */
    public function datetime(): static
    {
        $this->builder->gen->beginTime();
        $this->builder->gen->endTime();

        return $this;
    }
}