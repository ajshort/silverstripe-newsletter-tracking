<?php
/**
 * Shows tracking information when viewing a newsletter.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterTrackingExtension extends DataObjectDecorator {

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

}
