<?php
/**
 * @package silverstripe-newsletter-tracking
 */

Director::addRules(50, array(
	'newsletter-link' => 'NewsletterLinkController'
));

Object::remove_extension('NewsletterEmail', 'TrackingLinksEmail');

Object::add_extension('Member', 'NewsletterTrackingMemberExtension');
Object::add_extension('Newsletter', 'NewsletterTrackingExtension');
Object::add_extension('NewsletterEmail', 'NewsletterEmailLinkTrackingExtension');
Object::add_extension('Newsletter_TrackedLink', 'NewsletterTrackedLinkTrackingExtension');
