<?php

namespace Drupal\machine_name\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates that a field is unique for the given entity type.
 */
class MachineNameUniqueValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The entity form display storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  private $entityFormDisplayStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager')->getStorage('entity_form_display'));
  }

  /**
   * MachineNameUniqueValidator constructor.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_form_display_storage
   *   Entity form display storage.
   */
  public function __construct(EntityStorageInterface $entity_form_display_storage) {
    $this->entityFormDisplayStorage = $entity_form_display_storage;
  }

  /**
   * {@inheritdoc}
   */
  public function validate($item, Constraint $constraint) {

    if ($item->isEmpty()) {
      return NULL;
    }

    $entity = $item->getEntity();
    $entity_id = $entity->id();
    $entity_type = $entity->getEntityType();
    $field_name = $item->getFieldDefinition()->getName();
    $properties = $item->getProperties();
    $form_display_id = $entity_type->id() . '.' . $entity->bundle() . '.default';
    $form_display = $this->entityFormDisplayStorage->load($form_display_id);

    if (!$form_display || !$form_display->getComponent($field_name)['settings']['unique']) {
      return NULL;
    }

    // Query to see if existing entity with machine name exists.
    $query = \Drupal::entityQuery($entity_type->id())
      ->accessCheck(FALSE);

    foreach ($properties as $property) {
      $query->condition($field_name . '.value', $property->getValue());

      if (!empty($entity_id)) {
        $query->condition($entity_type->getKey('id'), $entity_id, '<>');
      }
      $result = $query->execute();

      if (!empty($result)) {
        $this->context->addViolation($constraint->message, ['%value' => $property->getValue()]);
      }
    }
  }

}
