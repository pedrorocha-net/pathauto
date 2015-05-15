<?php

/**
 * @file
 * Contains \Drupal\pathauto\Plugin\AliasType\EntityAliasTypeBase.
 */

namespace Drupal\pathauto\Plugin\pathauto\AliasType;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\pathauto\AliasTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A base class for Alias Type plugins.
 */
abstract class EntityAliasTypeBase extends AliasTypeBase implements AliasTypeInterface {

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a NodeAliasType instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $module_handler, LanguageManagerInterface $language_manager, EntityManagerInterface $entity_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $module_handler);
    $this->moduleHandler = $module_handler;
    $this->languageManager = $language_manager;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('language_manager'),
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPatterns() {
    $patterns = [];
    $languages = $this->languageManager->getLanguages();
    if ($this->entityManager->getDefinition($this->getPluginId())->hasKey('bundle')) {
      foreach ($this->getBundles() as $bundle => $bundle_label) {
        if (count($languages) && $this->isContentTranslationEnabled($bundle)) {
          $patterns[$bundle] = $this->t('Default path pattern for @bundle (applies to all @bundle fields with blank patterns below)', array('@bundle' => $bundle_label));
          foreach ($languages as $language) {
            $patterns[$bundle . '_' . $language->getId()] = $this->t('Pattern for all @language @bundle paths', array(
              '@bundle' => $bundle_label,
              '@language' => $language->getName(),
            ));
          }
        }
        else {
          $patterns[$bundle] = $this->t('Pattern for all @bundle paths', array('@bundle' => $bundle_label));
        }
      }
    }
    return $patterns;
  }

  /**
   * Returns bundles.
   *
   * @return string[]
   *   An array of bundle labels, keyed by bundle.
   */
  protected function getBundles() {
    return array_map(function ($bundle_info) {
      return $bundle_info['label'];
    }, $this->entityManager->getBundleInfo($this->getPluginId()));
  }

  /**
   * Checks if a bundle is enabled for translation.
   *
   * @param string $bundle
   *   The bundle.
   *
   * @return bool
   *   TRUE if content translation is enabled for the bundle.
   */
  protected function isContentTranslationEnabled($bundle) {
    return $this->moduleHandler->moduleExists('content_translation') && \Drupal::service('content_translation.manager')->isEnabled($this->getPluginId(), $bundle);
  }

}
