<?php

namespace Drupal\grid_field_formatter\Form;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Settings form for Grid Field Formatter module.
 */
class GridFieldFormatterSettingsForm extends ConfigFormBase {

  /**
   * The field type plugin manager.
   *
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface
   */
  protected $fieldTypePluginManager;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_plugin_manager
   *   The field type plugin manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, FieldTypePluginManagerInterface $field_type_plugin_manager) {
    parent::__construct($config_factory);
    $this->fieldTypePluginManager = $field_type_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.field.field_type')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'grid_field_formatter_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'grid_field_formatter.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('grid_field_formatter.settings');

    // Gather valid field types.
    $field_type_options = [];
    foreach ($this->fieldTypePluginManager->getUiDefinitions() as $name => $field_type) {
      $field_type_options[$name] = $field_type['label'];
    }

    $form['field_types'] = array(
      '#title' => $this->t('Please select the field type for which Grid Field Formatter should be available.'),
      '#type' => 'checkboxes',
      '#options' => $field_type_options,
      '#default_value' => $config->get('field_types'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('grid_field_formatter.settings');
    $config->set('field_types', $form_state->getValue('field_types'))
      ->save();
  }

}
