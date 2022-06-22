<?php

namespace Gigtrooper\Elements;


class PageElement extends BaseElement
{
	public function getName()
	{
		return "Pages";
	}

	public function defineModel()
	{
		return "Page";
	}
}