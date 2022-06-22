<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/27/2015
 * Time: 11:21 PM
 */

namespace Gigtrooper\Contracts;

use Gigtrooper\Models\BaseModel;

interface Field {

	public function getName();

	public function getInputHtml($name);

	public function getValue();

	public function save($handle, $fieldValue, BaseModel &$fromModel);

	public function getMatchesTax();

	public function getReturnTax();

	public function getWhereCql($value);

	public function getOrderTax();

	public function getOrderQuery($order);
}