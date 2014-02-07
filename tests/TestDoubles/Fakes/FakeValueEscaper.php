<?php

namespace Wikibase\Database\Tests\TestDoubles\Fakes;

use Wikibase\Database\ValueEscaper;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FakeValueEscaper implements ValueEscaper {

	public function getEscapedValue( $value ) {
		return '|' . $value . '|';
	}

}
