<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

/**
* @group functional
*/
class phpbb_functional_registration_test extends phpbb_functional_test_case
{
	public function test_disable_captcha_on_registration()
	{
		$this->login();
		$this->admin_login();

		$crawler = self::request('GET', "adm/index.php?i=acp_board&mode=registration&sid={$this->sid}");
		$form = $crawler->selectButton('Submit')->form();
		$form['config[enable_confirm]']->setValue('0');
		$crawler = self::submit($form);

		$this->assertContainsLang('CONFIG_UPDATED', $crawler->filter('#main .successbox')->text());
	}

	/**
	* @depends test_disable_captcha_on_registration
	*/
	public function test_register_new_account()
	{
		$this->add_lang('ucp');

		// Check that we can't skip
		self::request('GET', 'ucp.php?mode=register&agreed=1');
		$this->assertContainsLang('AGREE', $this->get_content());

		$crawler = self::request('GET', 'ucp.php?mode=register');
		$this->assertContainsLang('REGISTRATION', $crawler->filter('div.content h2')->text());

		$form = $crawler->selectButton('I agree to these terms')->form();
		$crawler = self::submit($form);

		$form = $crawler->selectButton('Submit')->form(array(
			'username'			=> 'user-reg-test',
			'email'				=> 'user-reg-test@phpbb.com',
			'new_password'		=> 'user-reg-testuser-reg-test',
			'password_confirm'	=> 'user-reg-testuser-reg-test',
		));
		$form['tz']->select('Europe/Berlin');
		$crawler = self::submit($form);

		$this->assertContainsLang('ACCOUNT_ADDED', $crawler->filter('#message')->text());
	}

	/**
	 * @depends test_register_new_account
	 */
	public function test_default_subscription_options()
	{
		$this->login('user-reg-test');
		$crawler = self::request('GET', 'ucp.php?i=ucp_notifications&mode=notification_options&sid=' . $this->sid);
		$this->assert_checkbox_is_checked($crawler, 'notification.type.post_notification.method.email');
		$this->assert_checkbox_is_checked($crawler, 'notification.type.topic_notification.method.email');
	}

	/**
	 * @depends test_disable_captcha_on_registration
	 */
	public function test_register_coppa_account()
	{
		$this->login();
		$this->admin_login();

		$crawler = self::request('GET', "adm/index.php?i=acp_board&mode=registration&sid={$this->sid}");
		$form = $crawler->selectButton('Submit')->form();
		$form['config[coppa_enable]']->setValue('1');
		$crawler = self::submit($form);

		$this->assertContainsLang('CONFIG_UPDATED', $crawler->filter('#main .successbox')->text());
		$this->logout();

		$this->add_lang('ucp');

		// Check that we can't skip
		$crawler = self::request('GET', 'ucp.php?mode=register&coppa=1');
		$this->assertContainsLang('COPPA_BIRTHDAY', $crawler->html());

		$agreement_url = $crawler->filter('#agreement')->filter('a')->links()[0]->getUri();
		preg_match('/(&hash=\w+)/', $agreement_url, $matches);
		$crawler = self::request('GET', 'ucp.php?mode=register&coppa=1' . $matches[1]);

		$this->assertContainsLang('REGISTRATION', $crawler->filter('div.content h2')->text());

		$form = $crawler->selectButton('I agree to these terms')->form();
		$crawler = self::submit($form);

		$form = $crawler->selectButton('Submit')->form(array(
			'username'			=> 'user-coppa-test',
			'email'				=> 'user-coppa-test@phpbb.com',
			'new_password'		=> 'user-coppa-testuser-coppa-test',
			'password_confirm'	=> 'user-coppa-testuser-coppa-test',
		));
		$form['tz']->select('Europe/Berlin');
		$crawler = self::submit($form);

		$this->assertContainsLang('ACCOUNT_COPPA', $crawler->filter('#message')->text());

		$this->login();
		$this->admin_login();

		$crawler = self::request('GET', "adm/index.php?i=acp_board&mode=registration&sid={$this->sid}");
		$form = $crawler->selectButton('Submit')->form();
		$form['config[coppa_enable]']->setValue('0');
		$crawler = self::submit($form);
	}
}
