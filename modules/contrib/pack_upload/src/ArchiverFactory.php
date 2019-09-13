<?php

namespace Drupal\pack_upload;

/**
 * Class Drupal\pack_upload\ArchiverFactory.
 */
class ArchiverFactory {

  /**
   * The Archiver class.
   *
   * @var mixed
   */
  private $archiver;

  /**
   * Get an appropriate archiver class for the file.
   *
   * @param string $file
   *   The file path.
   */
  public function getArchiver($file) {
    $extension = strstr(pathinfo($file)['basename'], '.');
    switch ($extension) {
      case '.tar.gz':
      case '.tar':
        $this->archiver = new \PharData($file);
        break;

      case '.zip':
        $this->archiver = new \ZipArchive($file);
        $this->archiver->open($file);
      default:
        break;
    }
    return $this->archiver;
  }

}
