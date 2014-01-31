<?php

namespace Wikibase\Database\Tests\TestDoubles\Fakes;

use Wikibase\Database\IdentifierEscaper;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FakeIdentifierEscaper implements IdentifierEscaper {

	public function getEscapedIdentifier( $identifier ) {
		return '~' . $identifier . '~';
	}

}
