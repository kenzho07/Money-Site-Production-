<?php

namespace Drupal\Tests\social_auth_slack\Functional;

use Drupal\Tests\social_auth\Functional\SocialAuthTestBase;

/**
 * Test Social Auth Slack settings form.
 *
 * @group social_auth
 *
 * @ingroup social_auth_slack
 */
class SocialAuthSlackSettingsFormTest extends SocialAuthTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['social_auth_slack'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $this->module = 'social_auth_slack';
    $this->provider = 'slack';
    $this->moduleType = 'social-auth';

    parent::setUp();
  }

  /**
   * Test if implementer is shown in the integration list.
   */
  public function testIsAvailableInIntegrationList() {
    $this->fields = ['client_id', 'client_secret'];

    $this->checkIsAvailableInIntegrationList();
  }

  /**
   * Test if permissions are set correctly for settings page.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testPermissionForSettingsPage() {
    $this->checkPermissionForSettingsPage();
  }

  /**
   * Test settings form submission.
   */
  public function testSettingsFormSubmission() {
    $this->edit = [
      'client_id' => $this->randomString(10),
      'client_secret' => $this->randomString(10),
    ];

    $this->checkSettingsFormSubmission();
  }

}
