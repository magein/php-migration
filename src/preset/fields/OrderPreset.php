<?php

namespace magein\migration\preset\fields;

use magein\migration\Column;
use magein\migration\preset\PresetGroup;

class OrderPreset extends PresetGroup
{
    public function payNo(string $comment = null, int $length = 28): Column
    {
        $comment = $comment ?: '支付编号';

        return $this->builder->char('pay_no', $comment, $length)->unique();
    }

    public function orderNo(string $comment = null, int $length = 28): Column
    {
        $comment = $comment ?: '订单编号';
        return $this->builder->char('order_no', $comment, $length)->unique();
    }

    public function refundNo(string $comment = null, int $length = 28): Column
    {
        $comment = $comment ?: '退款编号';

        return $this->builder->char('refund_no', $comment, $length)->default('');
    }

    public function tradeNo(string $comment = null, int $length = 28): Column
    {
        $comment = $comment ?: '交易编号 第三方生成';

        return $this->builder->char('trade_no', $comment, $length)->default('');
    }

    /**
     * 订单金额 单位分
     * 需要计算后传递，不能设置为默认值0
     * @param string|null $comment
     * @return Column
     */
    public function orderAmount(string $comment = null): Column
    {
        $comment = $comment ?: '订单金额 单位分';
        return $this->builder->integer('order_amount', $comment);
    }

    /**
     * 商品总金额 单位分
     * 需要计算后传递，不能设置为默认值0
     * @param string|null $comment
     * @return Column
     */
    public function goodsAmount(string $comment = null): Column
    {
        $comment = $comment ?: '商品总金额 单位分';
        return $this->builder->integer('goods_amount', $comment);
    }

    public function discountAmount(string $comment = null): Column
    {
        $comment = $comment ?: '优惠金额 单位分';
        return $this->builder->integer('discount_amount', $comment)->default(0);
    }

    /**
     * 实际支付金额 单位分
     * 需要计算后传递，不能设置为默认值0
     * @param string|null $comment
     * @return Column
     */
    public function actualAmount(string $comment = null): Column
    {
        $comment = $comment ?: '实际支付金额 单位分';
        return $this->builder->integer('actual_amount', $comment);
    }

    public function freightAmount(string $comment = null): Column
    {
        $comment = $comment ?: '运费金额 单位分';
        return $this->builder->integer('freight_amount', $comment)->default(0);
    }

    public function refundAmount(string $comment = null): Column
    {
        $comment = $comment ?: '退款金额 单位分';
        return $this->builder->integer('refund_amount', $comment)->default(0);
    }

    public function refundedAt(string $comment = null): Column
    {
        $comment = $comment ?: '退款时间';
        return $this->builder->dateTime('refunded_at', $comment)->nullable();
    }

    public function orderStatus(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '订单状态 -1 取消 cancel 1 已发货 shipped 7 已完成 finished 9 已退款 refunded';
        return $this->builder->integerTiny('order_status', $comment)->default($default);
    }

    public function payStatus(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '支付状态 0 待支付 pending 1 已支付 paid';
        return $this->builder->integerTiny('pay_status', $comment)->default($default);
    }

    public function paidAt(string $comment = null): Column
    {
        $comment = $comment ?: '支付时间';
        return $this->builder->dateTime('paid_at', $comment)->nullable();
    }

    public function payMethod(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '支付方式';
        return $this->builder->integerTiny('pay_method', $comment)->default($default);
    }

    public function buyerId(string $comment = null): Column
    {
        $comment = $comment ?: '买家ID';
        return $this->builder->integer('buyer_id', $comment);
    }

    public function buyerName(string $comment = null, int $length = 50): Column
    {
        $comment = $comment ?: '买家姓名';
        return $this->builder->string('buyer_name', $comment, $length);
    }

    public function buyerPhone(string $comment = null, int $length = 20): Column
    {
        $comment = $comment ?: '买家电话';
        return $this->builder->string('buyer_phone', $comment, $length);
    }

    public function receiverName(string $comment = null, int $length = 50): Column
    {
        $comment = $comment ?: '收货人姓名';
        return $this->builder->string('receiver_name', $comment, $length);
    }

    public function receiverPhone(string $comment = null, int $length = 20): Column
    {
        $comment = $comment ?: '收货人电话';
        return $this->builder->string('receiver_phone', $comment, $length);
    }

    public function couponId(string $comment = null): Column
    {
        $comment = $comment ?: '优惠券ID';
        return $this->builder->integer('coupon_id', $comment)->default(0);
    }

    public function couponAmount(string $comment = null): Column
    {
        $comment = $comment ?: '优惠券抵扣金额 单位分';
        return $this->builder->integer('coupon_amount', $comment)->default(0);
    }

    public function deliveryCompany(string $comment = null, int $length = 50): Column
    {
        $comment = $comment ?: '物流公司';
        return $this->builder->string('delivery_company', $comment, $length)->default('');
    }

    public function deliveryNo(string $comment = null, int $length = 50): Column
    {
        $comment = $comment ?: '物流单号';
        return $this->builder->string('delivery_no', $comment, $length)->default('');
    }

    public function deliveryTime(string $comment = null): Column
    {
        $comment = $comment ?: '发货时间';
        return $this->builder->dateTime('delivery_time', $comment)->nullable();
    }

    public function receivedAt(string $comment = null): Column
    {
        $comment = $comment ?: '收货时间';
        return $this->builder->dateTime('received_at', $comment)->nullable();
    }

    public function finishedAt(string $comment = null): Column
    {
        $comment = $comment ?: '完成时间';
        return $this->builder->dateTime('finished_at', $comment)->nullable();
    }

    public function isInvoice(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否需要发票';
        return $this->builder->integerTiny('is_invoice', $comment)->default($default);
    }
}