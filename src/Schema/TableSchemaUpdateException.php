<?php

namespace Wikibase\Database\Schema;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableSchemaUpdateException extends \Exception {

	public function __construct( $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		// TODO: define fields
	}

}