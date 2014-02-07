<?php

namespace Wikibase\Database\Tests\TestDoubles\Fakes;

use Wikibase\Database\TableNameFormatter;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FakeTableNameFormatter implements TableNameFormatter {

	public function formatTableName( $tableName ) {
		return 'prefix_' . $tableName;
	}

}
