<?php

/**
 * @file
 * Contains Drupal\pathauto\Plugin\Annotation\AliasType
 */

namespace Drupal\pathauto\Plugin\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an AliasType annotation.
 */
class AliasType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the action plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * The token types.
   *
   * @var string[]
   */
  public $types = array();

}
