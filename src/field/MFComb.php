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
        $article = new MFArticle($this->builder);


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