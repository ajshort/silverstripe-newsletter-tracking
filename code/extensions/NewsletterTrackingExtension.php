<?php
/**
 * Shows tracking information when viewing a newsletter.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterTrackingExtension extends DataObjectDecorator {

	public function extraStatics() {
		return array(
			'db'        => array('Token' => 'Varchar(32)'),
			'many_many' => array('ViewedMembers' => 'Member')
		);
	}

	public function updateCMSFields(FieldSet $fields) {
		$fields->replaceField('TrackedLinks', $tracked = new ComplexTableField(
			$this->owner,
			'TrackedLinks',
			'Newsletter_TrackedLink',
			array(
				'Original' => 'Link',
				'Visits'   => 'Visits'
			),
			null,
			null,
			'"Visits" DESC'
		));
		$tracked->setPermissions(array('show'));

		$fields->addFieldsToTab('Root.ViewedBy', array(
			new LiteralField('ViewedMembersNote', '<p>The viewed by list may '
				. 'not be accurate, as many email clients block images used '
				. 'for tracking by default.</p>'),
			$viewers = new TableListField(
				'ViewedMembers',
				'Member',
				array(
					'Name'  => 'Name',
					'Email' => 'Email'
				),
				'"NewsletterID" = ' . $this->owner->ID,
				null,
				'LEFT JOIN "Newsletter_ViewedMembers" ON "MemberID" = "Member"."ID"'
			)
		));
		$viewers->setPermissions(array('show'));
	}

	public function onBeforeWrite() {
		if (!$this->owner->NewsletterTrackingToken) {
			$generator = new RandomGenerator();
			$this->owner->Token = $generator->generateHash('md5');
		}
	}

}
