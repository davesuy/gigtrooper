<?php

namespace Gigtrooper\Models;

class QuoteMessage extends BaseModel
{
    /**
     * @var $source BaseModel
     */
    public $source;
    /**
     * @var $from BaseModel
     */
    public $from;
    /**
     * @var $to BaseModel
     */
    public $to;
    /**
     * @var $message BaseModel
     */
    public $message;
}