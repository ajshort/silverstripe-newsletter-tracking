<?php
/**
 * Embeds a tracker gif in emails before sending to allow view tracking.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterEmailViewTrackingExtension extends Extension {

	public function onBeforeSend() {
		if (!$to = $this->owner->To()) {
			return;
		}

		$member = DataObject::get_one('Member', sprintf(
			'"Email" = \'%s\'', Convert::raw2sql($to)
		));

		if (!$member) {
			return;
		}

		$body = $this->owner->Body()->forTemplate();
		$pos  = strrpos($body, '</body>');

		$img = sprintf('<img src="%s" alt="" width="1" height="1">', Controller::join_links(
			Director::absoluteBaseURL(),
			'newsletter-view',
			$this->owner->Newsletter()->Token,
			"$member->NewsletterTrackingToken.gif"
		));

		if ($pos) {
			$body = substr($body, 0, $pos) . $img . substr($body, $pos);
		} else {
			$body .= $img;
		}

		$this->owner->setBody(DBField::create('HTMLText', $body));
	}

}
