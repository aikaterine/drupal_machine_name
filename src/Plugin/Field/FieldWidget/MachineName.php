<?php

namespace Drupal\machine_name\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * The Machine name field widget implementation.
 *
 * @FieldWidget(
 *   id = "machine_name",
 *   label = @Translation("Machine name"),
 *   field_types = {
 *     "machine_name"
 *   },
 * )
 */
class MachineName extends WidgetBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'editable' => FALSE,
      'unique' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $disabled = FALSE;

    if (!$this->getSetting('editable') && isset($items[$delta]->value)) {
      $entity = $items->getEntity();
      if ($entity && !$entity->isNew()) {
        $disabled = TRUE;
      }
    }

    $widget = [
      '#type' => 'machine_name',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#maxlength' => 64,
      '#disabled' => $disabled,
      '#machine_name' => [
        'exists' => [$this, 'exists'],
      ],
    ];

    $element['value'] = $element + $widget;
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['editable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Editable'),
      '#description' => $this->t('Allows field to be editable in saved entity.'),
      '#default_value' => $this->getSetting('editable'),
    ];

    $elements['unique'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Unique'),
      '#description' => $this->t('Check the value to be unique.'),
      '#default_value' => $this->getSetting('unique'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    if (!empty($this->getSetting('editable'))) {
      $summary[] = $this->t('Editable: @editable', ['@editable' => $this->getSetting('editable') ? 'yes' : 'no']);
    }
    if (!empty($this->getSetting('unique'))) {
      $summary[] = $this->t('Unique: @unique', ['@unique' => $this->getSetting('unique') ? 'yes' : 'no']);
    }

    return $summary;
  }

  /**
   * This method needs to exist, but the constrain does the actual validation.
   *
   * @param string $value
   *   The input value.
   *
   * @return bool
   *   As the MachineNameUnique constraint will do the actual validation, always
   *   return FALSE to skip validation here.
   */
  public function exists($value) {
    return FALSE;
  }

}
