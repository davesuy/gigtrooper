<?php

namespace Gigtrooper\Elements;


class UserElement extends BaseElement
{
    public function getName()
    {
        return "Users";
    }

    public function defineModel()
    {
        return "User";
    }
}