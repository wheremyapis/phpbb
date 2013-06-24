<?php
/**
*
* @package testing
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_functions_validate_data_helper
{
	protected $test_case;

	public function __construct($test_case)
	{
		$this->test_case = $test_case;
	}

	/**
	* Test provided input data with supplied checks and compare to expected
	* results
	*
	* @param array $data Array containing one or more subarrays with the
	*		test data. The first element of a subarray is the
	*		expected result, the second one is the input, and the
	*		third is the data that should be passed to the function
	*		validate_data().
	*/
	public function assert_valid_data($data)
	{
		foreach ($data as $key => $test)
		{
			$this->test_case->assertEquals($test[0], validate_data(array($test[1]), array($test[2])));
		}
	}
}
