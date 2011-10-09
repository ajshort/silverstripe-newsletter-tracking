<?php
/**
 * @package silverstripe-newsletter-tracking
 */

Object::remove_extension('NewsletterEmail', 'TrackingLinksEmail');

Object::add_extension('NewsletterEmail', 'NewsletterEmailLinkTrackingExtension');
Object::add_extension('Member', 'NewsletterTrackingMemberExtension');
