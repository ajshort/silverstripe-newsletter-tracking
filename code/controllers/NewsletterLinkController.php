<?php
/**
 * Handles redirecting and tracking links in newsletters.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterLinkController extends Controller {

	public static $url_handlers = array(
		'$User!/$Hash!' => 'handleUserLink',
		'$Hash!'        => 'handleLink'
	);

	public static $allowed_actions = array(
		'handleLink',
		'handleUserLink'
	);

	protected $link;

	public function index() {
		$this->httpError(404);
	}

	/**
	 * Handles redirecting and tracking newsletter links without a user hash.
	 */
	public function handleLink($request) {
		$this->link = DataObject::get_one('Newsletter_TrackedLink', sprintf(
			'"Hash" = \'%s\'', Convert::raw2sql($request->param('Hash'))
		));

		if (!$this->link) {
			$this->httpError(404);
		}

		if (!Cookie::get("NewsletterLink-{$this->link->Hash}")) {
			$this->link->Visits++;
			$this->link->write();

			Cookie::set("NewsletterLink-{$this->link->Hash}", true);
		}

		return $this->redirect($this->link->Original, 301);
	}

	/**
	 * Handles redirecting and tracking newsletter links with a token also
	 * identifying the user.
	 *
	 * @uses NewsletterLinkController::handleLink
	 */
	public function handleUserLink($request) {
		$response = $this->handleLink($request);

		$user = DataObject::get_one('Member', sprintf(
			'"NewsletterTrackingToken" = \'%s\'', Convert::raw2sql($request->param('User'))
		));

		if ($user) {
			$this->link->Newsletter()->ViewedMembers()->add($user);
		}

		return $response;
	}

}
