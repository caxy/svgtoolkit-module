<?php

namespace Drupal\svgtoolkit\Plugin\ImageToolkit;

use Drupal\Core\ImageToolkit\ImageToolkitInterface;
use Drupal\svgtoolkit\AbstractSvgToolkit;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

/**
 * Class GdSvgToolkit.
 *
 * @ImageToolkit(
 *   id = "svg_imagemagick",
 *   title = @Translation("SVG+Imagemagick image manipulation toolkit")
 * )
 */
class SvgImagemagickToolkit extends AbstractSvgToolkit implements ImageToolkitInterface {


  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $toolkit = $container->get('image.toolkit.manager')->createInstance('imagemagick');
    /** @var MimeTypeGuesserInterface $guesser */
    $guesser = $container->get('file.mime_type.guesser');

    return new static($configuration, $plugin_id, $plugin_definition, $toolkit, $guesser);
  }

}
