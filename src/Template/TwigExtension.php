<?php

namespace Drupal\svgtoolkit\Template;

use Drupal\file\Entity\File;
use Drupal\file\Plugin\Field\FieldType\FileItem;

class TwigExtension extends \Twig_Extension {

  public function getName() {
    return 'svgtoolkit';
  }

  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('include_file_entity', array($this, 'includeFileEntity')),
    ];
  }

  public function includeFileEntity(FileItem $item)
  {
    /** @var File $entity */
    $entity = $item->entity;

    if ($entity->getMimeType() !== 'image/svg+xml') {
      throw new \RuntimeException('File must be SVG');
    }

    return file_get_contents($entity->getFileUri());
  }
}
