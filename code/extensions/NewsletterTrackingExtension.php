<?php
/**
 * Shows tracking information when viewing a newsletter.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterTrackingExtension extends DataObjectDecorator {

	public function extraStatics() {
		return array(
			'db'       => array('Token' => 'Varchar(32)'),
			'has_many' => array('Views' => 'NewsletterView')
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

		$viewers = new TableListField(
			'Views',
			'NewsletterView',
			null,
			'"NewsletterID" = ' . $this->owner->ID,
			'"Created" DESC'
		);
		$viewers->setPermissions(array('show', 'export'));

		$fields->addFieldsToTab('Root.ViewedBy', array(
			new LiteralField('ViewsNote', '<p>The viewed by list may not be '
				. 'accurate, as many email clients block images used for '
				. 'tracking by default.</p>'),
			$viewers
		));
	}

	public function onBeforeWrite() {
		if (!$this->owner->NewsletterTrackingToken) {
			$generator = new RandomGenerator();
			$this->owner->Token = $generator->generateHash('md5');
		}
	}

}
