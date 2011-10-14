<?php
/**
 * Handles requests made to a tracker gif which records a member as viewing a
 * newsletter.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterViewController extends Controller {

	public static $url_handlers = array(
		'$Newsletter!/$User!' => 'handleView'
	);

	public static $allowed_actions = array(
		'handleView'
	);

	private static $one_px_gif = array(
		71,  73, 70, 56, 57, 97, 1, 0, 1, 0, 128, 255, 0, 192, 192, 192, 0, 0,
		0, 33, 249, 4, 1, 0, 0, 0, 0, 44, 0, 0, 0, 0, 1, 0, 1, 0, 0, 2, 2, 68,
		1, 0, 59
	);

	public function index() {
		$this->httpError(404);
	}

	public function handleView($request) {
		if ($request->getExtension() != 'gif') {
			$this->httpError(404);
		}

		$newsletter = DataObject::get_one('Newsletter', sprintf(
			'"Token" = \'%s\'', Convert::raw2sql($request->param('Newsletter'))
		));

		$user = DataObject::get_one('Member', sprintf(
			'"NewsletterTrackingToken" = \'%s\'', Convert::raw2sql($request->param('User'))
		));

		if (!$newsletter || !$user) {
			$this->httpError(404);
		}

		$view = new NewsletterView();
		$view->IP           = $request->getIP();
		$view->NewsletterID = $newsletter->ID;
		$view->MemberID     = $user->ID;
		$view->write();

		$gif = '';

		foreach (self::$one_px_gif as $byte) {
			$gif .= chr($byte);
		}

		$response = new SS_HTTPResponse();
		$response->addHeader('Content-Type', 'image/gif');
		$response->setBody($gif);
		return $response;
	}

}
