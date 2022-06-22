<?php

namespace Gigtrooper\Elements;


class MemberCategoryElement extends BaseElement
{

	public function getName()
	{
		return "Member Categories";
	}

	public function defineModel()
	{
		return "MemberCategory";
	}
}