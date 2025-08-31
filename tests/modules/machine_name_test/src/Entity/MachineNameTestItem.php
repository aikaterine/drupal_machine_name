<?php

namespace Drupal\machine_name_test\Entity;

use Drupal\entity_test\Entity\EntityTest;

/**
 * Defines the machine_name_test_item entity class.
 *
 * @ContentEntityType(
 *   id = "machine_name_test_item",
 *   label = @Translation("Machine Name Test Item"),
 *   handlers = {
 *     "form" = {
 *       "default" = "\Drupal\Core\Entity\ContentEntityForm",
 *     },
 *   },
 *   base_table = "machine_name_test_item",
 *   data_table = "machine_name_test_item_field_data",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *   }
 * )
 */
class MachineNameTestItem extends EntityTest {

}
