<?php

/**
 * @file
 * Contains Drupal\NonEntityTestAliasType
 */

namespace Drupal\pathauto\Plugin\pathauto\AliasType;

/**
 * Provides an AliasType plugin for contact forms.
 */
class NonEntityTestAliasType extends AliasTypeBase {

  /**
   * {@inheritdoc}
   */
  public function getPatternDescription() {
    return $this->t('Pattern for forums and forum containers');
  }

  /**
   * {@inheritdoc}
   */
  public function getPatterns() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getSourcePrefix() {
    return 'non-entity/';
  }

}
