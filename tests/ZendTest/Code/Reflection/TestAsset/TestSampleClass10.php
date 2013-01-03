<?php

namespace ZendTest\Code\Reflection\TestAsset;

/**
 * TestSampleClass10 DocBlock Short Desc
 *
 * This is a long description for
 * the docblock of this class, it
 * should be longer than 3 lines.
 * It indeed is longer than 3 lines
 * now.
 *
 * @author Ralph Schindler <ralph.schindler@zend.com>
 * @method test()
 * @property $test
 */
class TestSampleClass10
{

	/**
	 * Method ShortDescription
	 *
	 * Method LongDescription
	 * This is a long description for
	 * the docblock of this class, it
	 * should be longer than 3 lines.
	 * It indeed is longer than 3 lines
	 * now.
	 *
	 * @param int $one Description for one
	 * @param int Description for two
	 * @param string $three Description for three
	 *                      which spans multiple lines
	 * @return mixed Some return descr
	 */
	public function doSomething($one, $two = 2, $three = 'three')
	{
		return 'mixedValue';
	}

	/**
	 * Method ShortDescription
	 *
	 * @param int $one Description for one
	 * @param int Description for two
	 * @param string $three Description for three
	 *                      which spans multiple lines
	 * @return int
	 */
	public function doSomethingElse($one, $two = 2, $three = 'three')
	{
		return 'mixedValue';
	}

}
