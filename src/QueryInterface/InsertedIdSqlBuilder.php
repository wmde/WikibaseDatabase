<?php

namespace Wikibase\Database\QueryInterface;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface InsertedIdSqlBuilder {

	/**
	 * @return string
	 */
	public function getSqlToGetTheInsertedId();

}
