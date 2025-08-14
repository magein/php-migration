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

        $article->title();
        $article->from();
        $this->builder->integer('cate_id', '分类id');
        $this->builder->string('author', '作者');
        $this->builder->string('intro', '简介', 60)->default('');
        $article->content();
        $article->thumb();
        $article->builder->string('source_url ', '转载地址');
        $article->builder->dateTime('published_at', '发布时间');
        $article->sort();

        $is = new MFIs($this->builder);

        $is->isOriginal();
        $is->isHot();
        $is->isRecommend();
        $is->isPublished();
        $this->builder->integer('read_num', '阅读数量', 0);
        $this->builder->integer('collect_num', '收藏数量', 0);
        $this->builder->integer('star_num', '点赞数量', 0);

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
        $order->orderNo();
        $gen->from();
        $gen->channel();
        $order->buyerId();
        $order->buyerName();
        $order->buyerPhone();
        $order->receiverName();
        $order->receiverPhone();
        $order->receivedAt();
        $this->regionAddress();
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
     * @return $this
     */
    public function voice()
    {
        $gen = new MFGen($this->builder);

        $gen->type('发票类型 0 个人 personal 1 公司普通发票 company 2 专用发票 vat');
        $gen->title();
        $gen->content();
        $this->builder->string('order_no', '关联订单编号')->default('');
        $this->builder->string('tax', ' 纳税人识别号', 60);

        return $this;
    }

    /**
     * @return $this
     */
    public function user(): static
    {
        $user = new MFUser($this->builder);

        $user->username();
        $user->password();
        $user->nickname();
        $user->avatar();
        $user->phone();
        $user->email();
        $user->gender();
        $user->avatar();
        $user->birthday();

        return $this;
    }

    /**
     * @return $this
     */
    public function file(): static
    {
        $file = new MFFile($this->builder);

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
        $address = new MFAddr($this->builder);

        $address->province();
        $address->city();
        $address->district();

        return $this;
    }

    /**
     * @return $this
     */
    public function regionAddress(): static
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