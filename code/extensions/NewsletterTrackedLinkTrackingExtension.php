<?php
/**
 * Adds a relationship between link tracking and member so that clicks can be
 * recorded per member.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterTrackedLinkTrackingExtension extends DataObjectDecorator {

	public function extraStatics() {
		return array('many_many' => array(
			'ViewedMembers' => 'Member'
		));
	}

	public function updateCMSFields(FieldSet $fields) {
		$fields->removeByName('Hash');

		$fields->replaceField('ViewedMembers', $members = new TableListField(
			'ViewedMembers',
			'Member',
			array(
				'Name'  => 'Name',
				'Email' => 'Email'
			),
			'"Newsletter_TrackedLinkID" = ' . $this->owner->ID,
			null,
			'LEFT JOIN "Newsletter_TrackedLink_ViewedMembers" ON "MemberID" = "Member"."ID"'
		));
		$members->setPermissions(array('show'));
	}

}
