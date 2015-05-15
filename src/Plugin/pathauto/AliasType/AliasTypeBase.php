<?php

/**
 * @file
 * Contains Drupal\pathauto\Plugin\pathauto\AliasType\AliasTypeBase
 */

namespace Drupal\pathauto\Plugin\pathauto\AliasType;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\pathauto\AliasTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A base class for Alias Type plugins.
 */
abstract class AliasTypeBase extends PluginBase implements AliasTypeInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Constructs a Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $string_translation) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('string_translation')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = NestedArray::mergeDeep(
      $this->defaultConfiguration(),
      $configuration
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    $definition = $this->getPluginDefinition();
    // Cast the admin label to a string since it is an object.
    // @see \Drupal\Core\StringTranslation\TranslationWrapper
    return (string) $definition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getTokenTypes() {
    $definition = $this->getPluginDefinition();
    return $definition['types'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = array(
      '#type' => 'details',
      '#title' => $this->getLabel(),
      '#open' => TRUE,
      '#tree' => TRUE,
    );

    // Prompt for the default pattern for this module.
    $key = 'default';

    $form[$key] = array(
      '#type' => 'textfield',
      '#title' => $this->getPatternDescription(),
      '#default_value' => $this->configuration['default'],
      '#size' => 65,
      '#maxlength' => 1280,
      '#element_validate' => array('token_element_validate'),
      '#after_build' => array('token_element_validate'),
      '#token_types' => $this->getTokenTypes(),
      '#min_tokens' => 1,
    );

    // If the module supports a set of specialized patterns, set
    // them up here.
    $patterns = $this->getPatterns();
    foreach ($patterns as $itemname => $itemlabel) {
      $key = 'default';
      $form['bundles'][$itemname][$key] = array(
        '#type' => 'textfield',
        '#title' => $itemlabel,
        '#default_value' => isset($this->configuration[$itemname . '.' . $key]) ? $this->configuration[$itemname . '.' . $key] : NULL,
        '#size' => 65,
        '#maxlength' => 1280,
        '#element_validate' => array('token_element_validate'),
        '#after_build' => array('token_element_validate'),
        '#token_types' => $this->getTokenTypes(),
        '#min_tokens' => 1,
      );
    }

    // Display the user documentation of placeholders supported by
    // this module, as a description on the last pattern.
    $form['token_help'] = array(
      '#title' => t('Replacement patterns'),
      '#type' => 'details',
      '#open' => FALSE,
    );
    $form['token_help']['help'] = array(
      '#theme' => 'token_tree',
      '#token_types' => $this->getTokenTypes(),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

}
