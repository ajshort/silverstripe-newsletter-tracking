<?php
/**
 * Shows tracking information when viewing a newsletter.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterTrackingExtension extends DataObjectDecorator {

	public function extraStatics() {
		return array('db' => array(
			'Token' => 'Varchar(32)'
		));
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
	}

	public function onBeforeWrite() {
		if (!$this->owner->NewsletterTrackingToken) {
			$generator = new RandomGenerator();
			$this->owner->Token = $generator->generateHash('md5');
		}
	}

}
