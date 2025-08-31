<?php

namespace Drupal\Tests\machine_name\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\machine_name_test\Entity\MachineNameTestItem;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * Test class for Machine Name form widget cases.
 *
 * @group machine_name
 */
class MachineNameFormWidgetTest extends FieldKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'machine_name',
    'machine_name_test',
    'options',
    'node',
  ];

  /**
   * The name of the Machine name field to use for testing.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * The name of the entity type that is used in the test.
   *
   * @var string
   */
  protected $entityType;

  /**
   * The name of the node entity type.
   *
   * @var string
   */
  protected $nodeEntityType;

  /**
   * The name of the node bundle.
   *
   * @var string
   */
  protected $nodeEntityBundle;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig([
      'system',
      'field',
      'node',
    ]);
    $this->installEntitySchema('machine_name_test_item');
    $this->installEntitySchema('node');

    $this->fieldName = 'field_test';
    // Entity, that doesn't use "edit" operation for their edit form.
    $this->entityType = 'machine_name_test_item';
    // Entity, that use "edit" operation for their edit form.
    $this->nodeEntityType = 'node';
    $this->nodeEntityBundle = 'article';

    // Create a machine_name field storage and field for validation.
    FieldStorageConfig::create([
      'field_name' => $this->fieldName,
      'entity_type' => $this->entityType,
      'type' => 'machine_name',
    ])->save();

    FieldConfig::create([
      'field_name' => $this->fieldName,
      'entity_type' => $this->entityType,
      'bundle' => $this->entityType,
    ])->save();

    NodeType::create([
      'type' => $this->nodeEntityBundle,
      'name' => 'Article',
    ])->save();

    // Create machine_name field storage and field for the node entity.
    FieldStorageConfig::create([
      'field_name' => $this->fieldName,
      'entity_type' => $this->nodeEntityType,
      'type' => 'machine_name',
    ])->save();

    FieldConfig::create([
      'entity_type' => $this->nodeEntityType,
      'field_name' => $this->fieldName,
      'bundle' => 'article',
    ])->save();
  }

  /**
   * Test of the Machine name widget settings.
   *
   * Tested for the entity that doesn't use "edit"
   * operation for their edit form.
   */
  public function testCommentEntityWidgetSettings() {
    // Get machine_name_test_item form display, and set field settings.
    $form_display = \Drupal::service('entity_display.repository')->getFormDisplay($this->entityType, $this->entityType);
    $form_display->setComponent($this->fieldName, [
      'settings' => [
        'editable' => 0,
      ],
    ])->save();

    // Create comment entity.
    $entity = MachineNameTestItem::create([
      'label' => 'Test',
    ]);

    // Set field value.
    $value = 'machine_name_test';
    $entity->field_test = $value;
    $entity->save();

    // Get machine_name_test entity edit form, and check editable setting.
    $entity_edit_form = \Drupal::service('entity.form_builder')->getForm($entity);
    $field_disabled = $entity_edit_form[$this->fieldName]['widget'][0]['value']['#disabled'];

    $this->assertTrue($field_disabled);
  }

  /**
   * Test of the Machine name widget settings.
   *
   * Tested for the entity that use "edit"
   * operation for their edit form.
   *
   * Selected Node entity as example.
   */
  public function testNodeEntityWidgetSettings() {
    // Get node form display, and set field settings.
    $form_display = \Drupal::service('entity_display.repository')->getFormDisplay($this->nodeEntityType, $this->nodeEntityBundle);
    $form_display->setComponent($this->fieldName, [
      'settings' => [
        'editable' => 0,
      ],
    ])->save();

    $entity = Node::create([
      'title' => $this->randomString(),
      'type' => $this->nodeEntityBundle,
      'uid' => 1,
    ]);

    // Set field value.
    $entity->field_test = 'machine_name_test';

    // Get node entity create form, and check editable setting.
    $entity_create_form = \Drupal::service('entity.form_builder')->getForm($entity);
    $field_disabled = $entity_create_form[$this->fieldName]['widget'][0]['value']['#disabled'];

    $this->assertFalse($field_disabled);

    // Save entity to reproduce creation form submit.
    $entity->save();
    // Get node entity edit form, and check editable setting.
    $entity_edit_form = \Drupal::service('entity.form_builder')->getForm($entity, 'edit');
    $field_disabled = $entity_edit_form[$this->fieldName]['widget'][0]['value']['#disabled'];

    $this->assertTrue($field_disabled);
  }

}
