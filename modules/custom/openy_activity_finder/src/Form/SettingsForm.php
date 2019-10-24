<?php

namespace Drupal\openy_activity_finder\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings Form for daxko.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($config_factory);
    $this->moduleHandler = $module_handler;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'openy_activity_finder_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'openy_activity_finder.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('openy_activity_finder.settings');

    $form_state->setCached(FALSE);

    $backend_options = [
      'openy_activity_finder.solr_backend' => 'Solr Backend (local db)',
    ];

    if ($this->moduleHandler->moduleExists('openy_daxko2')){
      $backend_options['openy_daxko2.openy_activity_finder_backend'] = $this->t('Daxko 2 (live API calls)');
    }

    $form['backend'] = [
      '#type' => 'select',
      '#options' => $backend_options,
      '#required' => TRUE,
      '#title' => $this->t('Backend for Activity Finder'),
      '#default_value' => $config->get('backend'),
      '#description' => $this->t('Search API backend for Activity Finder'),
    ];

    $search_api_indexes = $this->entityTypeManager
      ->getStorage('search_api_index')->loadByProperties();

    $indexes = [];
    if ($search_api_indexes) {
      foreach ($search_api_indexes as $index) {
        $indexes[$index->id()] = $index->get('name');
      }
    }

    $form['index'] = [
      '#type' => 'select',
      '#options' => $indexes,
      '#title' => $this->t('Search API index'),
      '#default_value' => $config->get('index'),
      '#description' => $this->t('Search API Index to use for SOLR backend.'),
      '#states' => [
        'visible' => [
          ':input[name="backend"]' => ['value' => 'openy_activity_finder.solr_backend'],
        ],
        'required' => [
          ':input[name="backend"]' => ['value' => 'openy_activity_finder.solr_backend'],
        ],
      ],
    ];

    $form['ages'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Ages'),
      '#default_value' => $config->get('ages'),
      '#description' => $this->t('Ages mapping. One per line. "<number of months>,<age display label>". Example: "660,55+"'),
    ];


    $form['exclude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Exclude category -- so we do not display Group Exercises'),
      '#default_value' => $config->get('exclude'),
      '#description' => $this->t('Provide ID of the Program Subcategory to exclude. You do not need to provide this if you use Daxko. Needed only for Solr backend.'),
    ];

    $form['disable_search_box'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable Search Box'),
      '#default_value' => $config->get('disable_search_box'),
      '#description' => $this->t('When checked hides search text box (both for Activity Finder and Results page).'),
    ];

    $form['disable_spots_available'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable Spots Available'),
      '#default_value' => $config->get('disable_spots_available'),
      '#description' => $this->t('When checked disables Spots Available feature on Results page.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('openy_activity_finder.settings');

    $config->set('backend', $form_state->getValue('backend'))->save();

    $config->set('index', $form_state->getValue('index'))->save();

    $config->set('ages', $form_state->getValue('ages'))->save();

    $config->set('exclude', $form_state->getValue('exclude'))->save();

    $config->set('disable_search_box', $form_state->getValue('disable_search_box'))->save();

    $config->set('disable_spots_available', $form_state->getValue('disable_spots_available'))->save();

    parent::submitForm($form, $form_state);
  }

}
