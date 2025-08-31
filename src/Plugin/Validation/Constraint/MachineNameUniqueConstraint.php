<?php

namespace Drupal\machine_name\Plugin\Validation\Constraint;

use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Checks if an entity field has a unique value.
 *
 * @Constraint(
 *   id = "MachineNameUnique",
 *   label = @Translation("Unique machine name constraint", context = "Validation"),
 * )
 */
class MachineNameUniqueConstraint extends SymfonyConstraint {

  /**
   * The default violation message.
   *
   * @var string
   */
  public $message = 'The machine name %value is already in use. It must be unique.';

  /**
   * This constraint is case-sensitive.
   *
   * @var bool
   */
  public $caseSensitive = TRUE;

  /**
   * {@inheritdoc}
   */
  public function validatedBy(): string {
    return '\Drupal\machine_name\Plugin\Validation\Constraint\MachineNameUniqueValidator';
  }

}
