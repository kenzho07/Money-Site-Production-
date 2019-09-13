<?php

namespace Drupal\pack_upload\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileSystem;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\pack_upload\ArchiverFactory;

/**
 * Class Drupal\pack_upload\Form\UploadForm.
 */
class UploadForm extends FormBase {

  /**
   * Drupal File System.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

  /**
   * Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Archiver.
   *
   * @var \Drupal\pack_upload\ArchiverFactory
   */
  protected $archiverFactory;

  /**
   * Constructor for \Drupal\pack_upload\Form\UploadForm class.
   *
   * @param \Drupal\Core\File\FileSystem $file_system
   *   The Form Builder.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity type manager.
   * @param \Drupal\pack_upload\ArchiverFactory $archiver_factory
   *   The Archiver Factory.
   */
  public function __construct(FileSystem $file_system,
  EntityTypeManagerInterface $entity_type_manager,
  ArchiverFactory $archiver_factory) {
    $this->fileSystem = $file_system;
    $this->entityTypeManager = $entity_type_manager;
    $this->archiverFactory = $archiver_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('entity_type.manager'),
      $container->get('pack_upload.archiver_factory')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Form\FormInterface::getFormId()
   */
  public function getFormId() {
    return 'pack_upload_upload_form';
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Form\FormInterface::buildForm()
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['panel'] = [
      '#title' => $this->t('Bulk Media Uploader'),
      '#type' => 'fieldset',
    ];
    $config = $this->config('pack_upload.settings');
    $path = file_build_uri($config->get('path'));

    $form['panel']['file'] = [
      '#title' => $this->t('Upload file'),
      '#type' => 'managed_file',
      '#required' => TRUE,
      '#upload_location' => $path,
      '#upload_validators' => [
        'file_validate_extensions' => ['zip tar tar.gz'],
      ],
      '#description' => $this->t('Create package of media files, for e.g., PDFs, images, text files and upload to Drupal. Valid extensions are .zip, .tar.gz, .tar. All files will be extracted to :directory', [':directory' => $path]),
    ];

    $form['panel']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Form\FormInterface::submitForm()
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $uri = file_build_uri($this->config('pack_upload.settings')->get('path'));
    $uploaded_file = $form_state->getValue('file');

    $file_storage = $this->entityTypeManager->getStorage('file');
    if ($file = $file_storage->load($uploaded_file[0])) {
      $archived_file_path = $this->fileSystem->realpath($file->getFileUri());
      $extract_dir = $this->fileSystem->realpath($uri);

      // Load the appropriate archiver and extarct the archive.
      $archiver = $this->archiverFactory->getArchiver($archived_file_path);
      $result = $archiver->extractTo($extract_dir);

      if ($result === TRUE) {
        drupal_set_message($this->t('All media have been extracted to %path',
          ['%path' => $extract_dir]));
        // Delete the archive file that was uploaded.
        file_delete($file->id());
      }
      else {
        drupal_set_message($this->t('There is some problem related to extraction
          of the file. Please upload and try again.'), 'error', FALSE);
      }
    }
    else {
      drupal_set_message($this->t("No file was uploaded."), 'error', FALSE);
    }
  }

}
