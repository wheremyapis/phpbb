<?php

/**
*
* @package testing
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace foo\bar\ucp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\foo\bar\ucp\main_module',
			'title'		=> 'ACP_FOOBAR_TITLE',
			'modes'		=> array(
				'mode'		=> array('title' => 'ACP_FOOBAR_MODE', 'auth' => '', 'cat' => array('ACP_FOOBAR_TITLE')),
			),
		);
	}
}
