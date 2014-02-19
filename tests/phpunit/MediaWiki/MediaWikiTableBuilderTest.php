<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Wikibase\Database\MediaWiki\MediaWikiTableBuilder;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;

/**
 * @covers Wikibase\Database\MediaWiki\MediaWikiTableBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMediawiki
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiTableBuilderTest extends \PHPUnit_Framework_TestCase {

	protected function getBuilderAndDependencies() {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\Schema\TableSqlBuilder' );

		$connectionProvider = $this->getMockConnectionProvider( $connection );

		$builder = new MediaWikiTableBuilder( $connectionProvider, $tableSqlBuilder );

		return array( $builder, $connection, $tableSqlBuilder );
	}

	protected function getMockConnectionProvider( $connection ) {
		$connectionProvider = $this->getMock( 'Wikibase\Database\DBConnectionProvider' );

		$connectionProvider->expects( $this->any() )
			->method( 'getConnection' )
			->will( $this->returnValue( $connection ) );

		return $connectionProvider;
	}

	/**
	 * @dataProvider tableProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testCreateTable( TableDefinition $table ) {
		list( $builder, $connection, $tableSqlBuilder ) = $this->getBuilderAndDependencies();

		$tableSqlBuilder->expects( $this->once() )
			->method( 'getCreateTableSql' )
			->with( $this->equalTo( $table ) )
			->will( $this->returnValue( 'foo bar baz' ) );

		$connection->expects( $this->once() )
			->method( 'query' )
			->with( $this->equalTo( 'foo bar baz' ) )
			->will( $this->returnValue( true ) );

		$builder->createTable( $table );
	}

	public function tableProvider() {
		$tables = array();

		$tables[] = new TableDefinition( 'differentfieldtypes', array(
			new FieldDefinition( 'intfield', new TypeDefinition( TypeDefinition::TYPE_INTEGER ) ),
			new FieldDefinition( 'floatfield', new TypeDefinition( TypeDefinition::TYPE_FLOAT ) ),
			new FieldDefinition( 'textfield', new TypeDefinition( TypeDefinition::TYPE_BLOB ) ),
			new FieldDefinition( 'tinyintfield', new TypeDefinition( TypeDefinition::TYPE_TINYINT ) ),
		) );

		$tables[] = new TableDefinition( 'defaultfieldvalues', array(
			new FieldDefinition( 'intfield', new TypeDefinition( TypeDefinition::TYPE_INTEGER ), true, 42 ),
		) );

		$tables[] = new TableDefinition( 'defaultfieldvalues', array(
			new FieldDefinition( 'intfield', new TypeDefinition( TypeDefinition::TYPE_INTEGER ), true, 42 ),
			new FieldDefinition( 'bigintfield', new TypeDefinition( TypeDefinition::TYPE_BIGINT ), true, 42 ),
			new FieldDefinition( 'decimalfield', new TypeDefinition( TypeDefinition::TYPE_DECIMAL ), true, 0.42 ),
		) );

		$tables[] = new TableDefinition( 'notnullfields', array(
			new FieldDefinition( 'intfield', new TypeDefinition( TypeDefinition::TYPE_INTEGER ), false ),
			new FieldDefinition( 'textfield', new TypeDefinition( TypeDefinition::TYPE_BLOB ), false ),
		) );

		$argLists = array();

		foreach ( $tables as $table ) {
			$argLists[] = array( $table );
		}

		return $argLists;
	}

	/**
	 * @dataProvider tableProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testCreateTableFailure( TableDefinition $table ) {
		list( $builder, $connection, $tableSqlBuilder ) = $this->getBuilderAndDependencies();

		$connection->expects( $this->once() )
			->method( 'query' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( 'Wikibase\Database\Schema\TableCreationFailedException' );

		$builder->createTable( $table );
	}

	/**
	 * @dataProvider tableProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testDropTable( TableDefinition $table ) {
		list( $builder, $connection, $tableSqlBuilder ) = $this->getBuilderAndDependencies();

		$connection->expects( $this->once() )
			->method( 'dropTable' )
			->with( $this->equalTo( $table ) );

		$builder->dropTable( $table );
	}

	/**
	 * @dataProvider tableProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testDropTableFailure( TableDefinition $table ) {
		list( $builder, $connection, $tableSqlBuilder ) = $this->getBuilderAndDependencies();

		$connection->expects( $this->once() )
			->method( 'dropTable' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( 'Wikibase\Database\Schema\TableDeletionFailedException' );

		$builder->dropTable( $table );
	}

}
