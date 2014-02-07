<?php

namespace Wikibase\Database\Tests\TestDoubles\Fakes;

use Wikibase\Database\Escaper;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FakeEscaper implements Escaper {

	public function getEscapedValue( $value ) {
		return '|' . $value . '|';
	}

	public function getEscapedIdentifier( $identifier ) {
		return '~' . $identifier . '~';
	}

}
