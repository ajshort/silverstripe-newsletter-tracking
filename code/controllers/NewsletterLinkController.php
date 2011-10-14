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

	protected $view;

	public function index() {
		$this->httpError(404);
	}

	/**
	 * Handles redirecting and tracking newsletter links without a user hash.
	 */
	public function handleLink($request) {
		$link = DataObject::get_one('Newsletter_TrackedLink', sprintf(
			'"Hash" = \'%s\'', Convert::raw2sql($request->param('Hash'))
		));

		if (!$link) {
			$this->httpError(404);
		}

		$this->view = new NewsletterLinkView();
		$this->view->IP     = $request->getIP();
		$this->view->LinkID = $link->ID;
		$this->view->write();

		return $this->redirect($link->Original, 301);
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
			$this->view->MemberID = $user->ID;
			$this->view->write();
		}

		return $response;
	}

}
