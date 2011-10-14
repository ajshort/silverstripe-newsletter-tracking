<?php
/**
 * Records an individual view of a newsletter.
 *
 * @package silverstripe-newsletter-tracking
 */
class NewsletterView extends DataObject {

	public static $db = array(
		'IP' => 'Varchar(39)'
	);

	public static $has_one = array(
		'Newsletter' => 'Newsletter',
		'Member'     => 'Member'
	);

	public static $summary_fields = array(
		'Member.ID'    => 'Member ID',
		'CreatedNice'  => 'Time',
		'Member.Email' => 'Email',
		'Member.Name'  => 'Name',
		'IP'           => 'IP Address'
	);

	public function getCreatedNice() {
		return $this->obj('Created')->Nice();
	}

}
