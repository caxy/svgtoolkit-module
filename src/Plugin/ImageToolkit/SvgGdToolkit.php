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
 *   id = "svg_gd",
 *   title = @Translation("SVG+GD2 image manipulation toolkit")
 * )
 */
class SvgGdToolkit extends AbstractSvgToolkit implements ImageToolkitInterface {


  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $toolkit = $container->get('image.toolkit.manager')->createInstance('gd');
    /** @var MimeTypeGuesserInterface $guesser */
    $guesser = $container->get('file.mime_type.guesser');

    return new static($configuration, $plugin_id, $plugin_definition, $toolkit, $guesser);
  }

}
