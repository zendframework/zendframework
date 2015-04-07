<?php
namespace ZendTest\Code\Reflection\TestAsset;

//issue #7428
trait TestTraitClass3
{
	/**
	* @var bool
	*/
	protected $dummy = false;

	/**
	* @return bool
	*/
	public function getDummy()
	{
		return $this->dummy;
	}

	/**
	* @param bool $autoFetchingAllowed
	* @return Model_AbstractModel
	*/
	public function setDummy($dummy)
	{
		$this->dummy = boolval($dummy);
		return $this;
	}
}
