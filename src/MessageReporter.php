<?php

namespace Wikibase\Database;

/**
 * Interface for objects that can report messages.
 *
 * @since 0.1
 * @file
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface MessageReporter {

	/**
	 * @param string $message
	 */
	public function reportMessage( $message );

}
