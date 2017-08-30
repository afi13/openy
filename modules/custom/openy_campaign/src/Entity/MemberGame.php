<?php
/**
 *
 */

namespace Drupal\openy_campaign\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;

use Drupal\openy_campaign\MemberCampaignActivityInterface;

/**
 * Track participant's check-ins to facilities.
 *
 * @ingroup openy_campaign
 *
 * @ContentEntityType(
 *   id = "openy_campaign_member_game",
 *   label = @Translation("Member Game entity"),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 *   base_table = "openy_campaign_member_game",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class MemberGame extends ContentEntityBase implements MemberCampaignActivityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Game entity.'))
      ->setReadOnly(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time the record was created.'));

    $fields['date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Date'))
      ->setDescription(t('The timestamp for the day when game was played.'));

    $fields['member'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Member'))
      ->setDescription(t('Member who can play the game. Tied to campaign participant.'))
      ->setSettings([
        'target_type' => 'openy_campaign_member_campaign',
        'default_value' => 0,
      ]);

    $fields['result'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Result'))
      ->setDescription(t('Whether member won or not.'));

    $fields['log'] = BaseFieldDefinition::create('text')
      ->setLabel(t('Log'))
      ->setDescription(t('Detailed log for the draw result'));

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel('UUID')
      ->setReadOnly(TRUE);

    return $fields;
  }

}
