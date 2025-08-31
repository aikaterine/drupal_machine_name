<?php

namespace Drupal\Tests\machine_name\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Test class for Machine Name "unique value" form option.
 *
 * @group machine_name
 */
class MachineNameUniqueValueTest extends FieldKernelTestBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'machine_name',
  ];

  /**
   * The form display of the entity.
   *
   * @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface
   */
  protected $formDisplay;

  /**
   * The base entity with already set value.
   *
   * @var \Drupal\Core\Entity\EntityBase
   */
  protected $baseEntity;

  /**
   * The machine name field value.
   *
   * @var string
   */
  protected $value;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create a machine_name field storage and field for validation.
    FieldStorageConfig::create([
      'field_name' => 'field_test',
      'entity_type' => 'entity_test',
      'type' => 'machine_name',
    ])->save();
    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_test',
      'bundle' => 'entity_test',
    ])->save();

    $this->formDisplay = \Drupal::service('entity_display.repository')
      ->getFormDisplay('entity_test', 'entity_test');

    $this->value = 'machine_name_test';
    // Create a base entity with already set value.
    $this->baseEntity = EntityTest::create();
    $this->baseEntity->name->value = 'Base entity';
    $this->baseEntity->field_test = $this->value;
    $this->baseEntity->save();
  }

  /**
   * Test disabled "unique value" widget option for the machine name field.
   *
   * On entity create.
   */
  public function testUniqueValueWidgetOptionDisabledOnCreate() {
    // Set settings for the "unique" option.
    $this->formDisplay->setComponent('field_test', [
      'settings' => [
        'editable' => 0,
        'unique' => 0,
      ],
    ])->save();

    // Creates the new entity and set actually the same value,
    // as for the base entity.
    $new_entity = EntityTest::create();
    $new_entity->name->value = 'New entity';
    $new_entity->field_test = $this->value;

    $violations = $new_entity->validate();
    $this->assertCount(0, $violations);

    $new_entity->delete();
  }

  /**
   * Test disabled "unique value" widget option for the machine name field.
   *
   * On entity update.
   */
  public function testUniqueValueWidgetOptionDisabledOnUpdate() {
    // Set settings for the "unique" option.
    $this->formDisplay->setComponent('field_test', [
      'settings' => [
        'editable' => 0,
        'unique' => 0,
      ],
    ])->save();

    // Creates new entity with unique value.
    $new_entity = EntityTest::create();
    $new_entity->name->value = 'New entity';
    $new_entity->field_test = $this->randomMachineName();
    $new_entity->save();

    // Set already exists value and validate.
    $new_entity->field_test = $this->value;
    $violations = $new_entity->validate();
    $this->assertCount(0, $violations);

    $new_entity->delete();
  }

  /**
   * Test active "unique value" widget option for the machine name field.
   *
   * On entity create.
   */
  public function testUniqueValueWidgetOptionActiveOnCreate() {
    // Set settings for the "unique" option.
    $this->formDisplay->setComponent('field_test', [
      'settings' => [
        'editable' => 0,
        'unique' => 1,
      ],
    ])->save();

    $new_entity = EntityTest::create();
    $new_entity->name->value = 'New entity';
    $new_entity->field_test = $this->value;

    $violations = $new_entity->validate();
    $this->assertCount(1, $violations);
    $this->assertEquals($this->t('The machine name %value is already in use. It must be unique.', [
      '%value' => 'machine_name_test',
    ]), $violations[0]->getMessage());
    $this->assertEquals('field_test.0', $violations[0]->getPropertyPath());

    $new_entity->delete();
  }

  /**
   * Test active "unique value" widget option for the machine name field.
   *
   * On entity update.
   */
  public function testUniqueValueWidgetOptionActiveOnUpdate() {
    // Set settings for the "unique" option.
    $this->formDisplay->setComponent('field_test', [
      'settings' => [
        'editable' => 0,
        'unique' => 1,
      ],
    ])->save();

    // Creates new entity with unique value.
    $new_entity = EntityTest::create();
    $new_entity->name->value = 'New entity';
    $new_entity->field_test = $this->randomMachineName();
    $new_entity->save();

    // Set already exists value and validate.
    $new_entity->field_test = $this->value;
    $violations = $new_entity->validate();
    $this->assertCount(1, $violations);
    $this->assertEquals($this->t('The machine name %value is already in use. It must be unique.', [
      '%value' => 'machine_name_test',
    ]), $violations[0]->getMessage());
    $this->assertEquals('field_test.0', $violations[0]->getPropertyPath());

    $new_entity->delete();
  }

}
