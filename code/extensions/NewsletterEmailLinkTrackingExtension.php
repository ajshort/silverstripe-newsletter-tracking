<?php
/**
 * Rewrites anchor links in email content with a link to a controller which
 * tracks the click and then redirects the user.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterEmailLinkTrackingExtension extends Extension {

	public function onBeforeSend() {
		$email  = $this->owner;
		$letter = $email->Newsletter();
		$body   = new SS_HTMLValue($email->Body()->forTemplate());
		$links  = array();
		$member = null;

		if (!$body || !$letter) {
			return;
		}

		if ($email->To()) {
			$member = DataObject::get_one('Member', sprintf(
				'"Email" = \'%s\'', Convert::raw2sql($email->To())
			));
		}

		// First build up a set of all the unique links within the newsletter,
		// along with the elements that link to them.
		foreach ($body->getElementsByTagName('a') as $link) {
			$href = $link->getAttribute('href');
			if ((strpos($href, '{$') !== false) || (strpos($href, 'mailto:') !== false)) {
				// ignore links with keywords
				continue;
			}
			if (array_key_exists($href, $links)) {
				$links[$href][] = $link;
			} else {
				$links[$href] = array($link);
			}
		}

		// Then actually do the processing. Create a unique tracking object for
		// each link. Attempt to embed a member-specific tracking token if
		// the newsletter is being sent to a member.
		foreach ($links as $href => $elements) {
			$track = DataObject::get_one('Newsletter_TrackedLink', sprintf(
				'"NewsletterID" = %d AND "Original" = \'%s\'',
				$letter->ID,
				Convert::raw2sql($href)
			));

			if (!$track) {
				$track = new Newsletter_TrackedLink();
				$track->Original     = $href;
				$track->NewsletterID = $letter->ID;
				$track->write();
			}

			if ($member) {
				$trackHref = Controller::join_links(
					Director::baseURL(),
					'newsletter-link',
					$member->NewsletterTrackingToken,
					$track->Hash
				);
			} else {
				$trackHref = Controller::join_links(
					Director::baseURL(), 'newsletter-link', $track->Hash
				);
			}

			foreach ($elements as $element) {
				$element->setAttribute('href', $trackHref);
			}
		}

		$dom = $body->getDocument();
		$email->setBody(DBField::create('HTMLText', $dom->saveHTML()));
	}
}
