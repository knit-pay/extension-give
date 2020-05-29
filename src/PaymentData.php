<?php
/**
 * Payment data
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\Give
 */

namespace Pronamic\WordPress\Pay\Extensions\Give;

use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Pay\Payments\PaymentData as Core_PaymentData;
use Pronamic\WordPress\Pay\Payments\Item;
use Pronamic\WordPress\Pay\Payments\Items;

/**
 * Title: WordPress pay Give payment data
 * Description:
 * Copyright: 2005-2020 Pronamic
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version 2.0.3
 * @since   1.0.0
 */
class PaymentData extends Core_PaymentData {
	/**
	 * The donation ID.
	 *
	 * @var int
	 */
	private $donation_id;

	/**
	 * The gateway.
	 *
	 * @since 1.0.3
	 *
	 * @var Gateway
	 */
	private $gateway;

	/**
	 * Constructs and initializes an Give payment data object.
	 *
	 * @param int     $donation_id Donation ID.
	 * @param Gateway $gateway     Gateway.
	 */
	public function __construct( $donation_id, $gateway ) {
		parent::__construct();

		$this->donation_id = $donation_id;
		$this->gateway     = $gateway;

		$this->user_info = give_get_payment_meta_user_info( $donation_id );
		$this->post = get_post( $donation_id );
	}

	/**
	 * Get source indicator
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_source()
	 * @return string
	 */
	public function get_source() {
		return 'give';
	}

	/**
	 * Get source ID.
	 *
	 * @return int
	 */
	public function get_source_id() {
		return $this->donation_id;
	}

	/**
	 * Get title.
	 *
	 * @return string
	 */
	public function get_title() {
		return sprintf(
			/* translators: %s: order id */
			__( 'Give donation %s', 'pronamic_ideal' ),
			$this->get_order_id()
		);
	}

	/**
	 * Get description
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_description()
	 * @return string
	 */
	public function get_description() {
		$search = array(
			'{donation_id}',
		);

		$replace = array(
			$this->get_order_id(),
		);

		$description = $this->gateway->get_transaction_description();

		if ( '' === $description ) {
			$description = $this->get_title();
		}

		return str_replace( $search, $replace, $description );
	}

	/**
	 * Get order ID
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_order_id()
	 * @return string
	 */
	public function get_order_id() {
	    return $this->post->post_title;
	}

	/**
	 * Get items
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_items()
	 * @return Items
	 */
	public function get_items() {
		// Items.
		$items = new Items();

		// Item
		// We only add one total item, because iDEAL cant work with negative price items (discount).
		$price = new Money( \give_donation_amount( $this->donation_id ) );

		$item = new Item();
		$item->set_number( $this->get_order_id() );
		$item->set_description( $this->get_description() );
		$item->set_price( $price->get_value() );
		$item->set_quantity( 1 );

		$items->add_item( $item );

		return $items;
	}

	/**
	 * Get currency
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_currency_alphabetic_code()
	 * @return string
	 */
	public function get_currency_alphabetic_code() {
		return give_get_payment_currency_code( $this->donation_id );
	}

	public function get_email() {
		return give_get_payment_user_email( $this->donation_id );
	}

	public function get_first_name() {
		if ( isset( $this->user_info['first_name'] ) ) {
			return $this->user_info['first_name'];
		}
	}

	public function get_last_name() {
		if ( isset( $this->user_info['last_name'] ) ) {
			return $this->user_info['last_name'];
		}
	}

	public function get_customer_name() {
		return $this->user_info['first_name'] . ' ' . $this->user_info['last_name'];
	}

	public function get_address() {
		$address = null;

		if ( ! empty( $this->user_info['address'] ) ) {
			$address = sprintf(
				'%s %s',
				$this->user_info['address']['line1'],
				$this->user_info['address']['line2']
			);
		}

		return $address;
	}

	public function get_city() {
		$city = null;

		if ( ! empty( $this->user_info['address'] ) ) {
			$city = $this->user_info['address']['city'];
		}

		return $city;
	}

	public function get_zip() {
		$zip = null;

		if ( ! empty( $this->user_info['address'] ) ) {
			$zip = $this->user_info['address']['zip'];
		}

		return $zip;
	}

	/**
	 * Get Telephone Number.
	 *
	 * @return null|string
	 */
	public function get_telephone_number(){
	    return give_get_meta( $this->donation_id, 'give_phone', true );
	}

	/**
	 * Get normal return URL.
	 *
	 * @link https://github.com/woothemes/woocommerce/blob/v2.1.3/includes/abstracts/abstract-wc-payment-gateway.php#L52
	 * @return string
	 */
	public function get_normal_return_url() {
	}

	public function get_cancel_url() {
	}

	public function get_success_url() {
	}

	public function get_error_url() {
	}
}
