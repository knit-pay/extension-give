<?php

namespace Pronamic\WordPress\Pay\Extensions\Give;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: Give Gulden gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version unreleased
 * @since   unreleased
 */
class GuldenGateway extends Gateway {
	/**
	 * Constructs and initialize Gulden gateway.
	 */
	public function __construct() {
		parent::__construct(
			'pronamic_pay_gulden',
			PaymentMethods::get_name( PaymentMethods::GULDEN ),
			PaymentMethods::GULDEN
		);
	}
}
