<?php

namespace Gigtrooper\Elements;


class PostElement extends BaseElement
{
    public function getName()
    {
        return "Post";
    }

    public function defineModel()
    {
        return "Post";
    }
}