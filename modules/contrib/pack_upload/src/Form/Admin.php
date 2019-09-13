<?php

namespace Drupal\pack_upload\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class Drupal\pack_upload\Form\Admin.
 */
class Admin extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'pack_upload.settings',
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Form\FormInterface::getFormId()
   */
  public function getFormId() {
    return 'pack_upload_admin_settings';
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Form\ConfigFormBase::buildForm()
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['panel'] = [
      '#title' => $this->t('Bulk Media Uploader'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
    ];
    $config = $this->config('pack_upload.settings');

    $form['panel']['path'] = [
      '#title' => $this->t('Bulk Media Extraction Directory'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#description' => $this->t('Name of the directory where you want all files to be extracted.'),
      '#default_value' => $config->get('path'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Form\ConfigFormBase::submitForm()
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('pack_upload.settings')
      ->set('path', $form_state->getValue('path'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
