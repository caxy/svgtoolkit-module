<?php

namespace Drupal\svgtoolkit;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\Image;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\ImageToolkit\ImageToolkitInterface;
use Drupal\system\Plugin\ImageToolkit\GDToolkit;
use Svg\Document;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

class AbstractSvgToolkit extends PluginBase {

  /**
   * @var ImageToolkitInterface
   */
  protected $toolkit;
  /**
   * @var bool
   */
  protected $svg = FALSE;
  /**
   * @var \SimpleXMLElement
   */
  protected $xml;
  /**
   * @var Document
   */
  protected $document;
  /**
   * @var MimeTypeGuesserInterface
   */
  protected $guesser;
  /**
   * @var string
   */
  protected $source = '';
  /**
   * @var array
   */
  protected $dimensions;

  public function __construct(
    array $configuration,
    $plugin_id,
    array $plugin_definition,
    ImageToolkitInterface $toolkit,
    MimeTypeGuesserInterface $guesser
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->toolkit = $toolkit;
    $this->guesser = $guesser;
  }

  public static function isAvailable() {
    return GDToolkit::isAvailable();
  }

  public static function getSupportedExtensions() {
    return array_merge(GDToolkit::getSupportedExtensions(), ['svg']);
  }

  public function setSource($source) {
    if ($this->source) {
      throw new \BadMethodCallException(
        __METHOD__ . '() may only be called once'
      );
    }

    $this->source = $source;
    if ($this->guesser->guess($source) === 'image/svg+xml') {
      $this->svg = TRUE;
    }
    else {
      $this->toolkit->setSource($source);
    }

    return $this;
  }

  public function getSource() {
    return $this->svg ? $this->source : $this->toolkit->getSource();
  }

  public function isValid() {
    return $this->svg ? TRUE : $this->toolkit->isValid();
  }

  public function save($destination) {
    if ($this->svg) {
      return $this->xml->asXML($destination);
    }
    else {
      return $this->toolkit->save($destination);
    }
  }

  public function parseFile() {
    if ($this->svg) {
      $this->document = new Document();
      $uri = \Drupal::service('file_system')->realpath($this->source);
      $this->document->loadFile($uri);
      $this->document->getDimensions();
      $this->dimensions = [
        'width' => round($this->document->getWidth()),
        'height' => round($this->document->getHeight()),
      ];

      $this->xml = simplexml_load_file($uri);

      return TRUE;
    }
    else {
      return $this->toolkit->parseFile();
    }
  }

  public function getHeight() {
    return $this->svg ? round(
      $this->document->getHeight()
    ) : $this->toolkit->getHeight();
  }

  public function getWidth() {
    return $this->svg ? round(
      $this->document->getWidth()
    ) : $this->toolkit->getWidth();
  }

  public function getMimeType() {
    return $this->svg ? 'image/svg+xml' : $this->toolkit->getMimeType();
  }

  public function buildConfigurationForm(
    array $form,
    FormStateInterface $form_state
  ) {
    return $this->toolkit->buildConfigurationForm($form, $form_state);
  }

  public function submitConfigurationForm(
    array &$form,
    FormStateInterface $form_state
  ) {
    $this->toolkit->submitConfigurationForm($form, $form_state);
  }

  public function getRequirements() {
    return $this->toolkit->getRequirements();
  }

  public function apply($operation, array $arguments = array()) {
    if ($this->svg) {
      switch ($operation) {
        case 'scale':
          Image::scaleDimensions(
            $this->dimensions,
            $arguments['width'],
            $arguments['height'],
            $arguments['upscale']
          );
      }
      return true;
    }
    else {
      return $this->toolkit->apply($operation, $arguments);
    }
  }

  public function validateConfigurationForm(
    array &$form,
    FormStateInterface $form_state
  ) {
    $this->toolkit->validateConfigurationForm($form, $form_state);
  }
}