<?php
namespace WP2_Test\Simulators;

use PDO;

/**
 * In-memory $wpdb simulator using SQLite for contract/service tests.
 */
class WPDB {
	/** @var PDO */
	protected $pdo;

	public function __construct() {
		$this->pdo = new PDO( 'sqlite::memory:' );
		$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$this->initialize_schema();
	}

	protected function initialize_schema() {
		// Minimal schemas for users, posts, options (expand as needed)
		$this->pdo->exec( 'CREATE TABLE users (ID INTEGER PRIMARY KEY, user_login TEXT, user_pass TEXT, user_email TEXT)' );
		$this->pdo->exec( 'CREATE TABLE posts (ID INTEGER PRIMARY KEY, post_title TEXT, post_content TEXT, post_status TEXT)' );
		$this->pdo->exec( 'CREATE TABLE options (option_id INTEGER PRIMARY KEY, option_name TEXT, option_value TEXT)' );
	}

	public function get_results( $query ) {
		return $this->pdo->query( $query )->fetchAll( PDO::FETCH_OBJ );
	}

	public function get_var( $query ) {
		$result = $this->pdo->query( $query )->fetch( PDO::FETCH_NUM );
		return $result ? $result[0] : null;
	}

	/**
	 * A simplified, insecure mock of the prepare method for testing purposes only.
	 *
	 * WARNING: This method uses a simple vsprintf and does NOT provide real database
	 * parameterization or sanitization. It is inherently insecure and must not
	 * be used as a reference for production code.
	 *
	 * @param string $query The SQL query with sprintf-style placeholders (%s, %d).
	 * @param mixed ...$args The values to substitute into the query placeholders.
	 * @return string The formatted SQL string.
	 */
	public function prepare( $query, ...$args ) {
		// Simple sprintf-based formatting (not for production, but fine for tests)
		return vsprintf( $query, array_map( [ $this->pdo, 'quote' ], $args ) );
	}

	public function insert( $table, $data ) {
		$fields = array_keys( $data );
		$placeholders = array_fill( 0, count( $fields ), '?' );
		$sql = sprintf(
			'INSERT INTO %s (%s) VALUES (%s)',
			$table,
			implode( ',', $fields ),
			implode( ',', $placeholders )
		);
		$stmt = $this->pdo->prepare( $sql );
		return $stmt->execute( array_values( $data ) );
	}

	public function update( $table, $data, $where ) {
		$fields = array_keys( $data );
		$set = implode( ',', array_map( fn( $f ) => "$f = ?", $fields ) );
		$where_fields = array_keys( $where );
		$where_clause = implode( ' AND ', array_map( fn( $f ) => "$f = ?", $where_fields ) );
		$sql = sprintf( 'UPDATE %s SET %s WHERE %s', $table, $set, $where_clause );
		$stmt = $this->pdo->prepare( $sql );
		return $stmt->execute( array_merge( array_values( $data ), array_values( $where ) ) );
	}

	public function delete( $table, $where ) {
		$where_fields = array_keys( $where );
		$where_clause = implode( ' AND ', array_map( fn( $f ) => "$f = ?", $where_fields ) );
		$sql = sprintf( 'DELETE FROM %s WHERE %s', $table, $where_clause );
		$stmt = $this->pdo->prepare( $sql );
		return $stmt->execute( array_values( $where ) );
	}
}
