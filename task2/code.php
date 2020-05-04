<?php

/**
 * The script does not perform any noticeable/valuable actions.
 */
for ($i = 0; $i < 10000000; $i++) {
	/**
	 * Creates tamporary table to store select query results.
	 *
	 * But there are conflicting conditions in the inner join statement.
	 * u.id Value can't be equal AND greater than u1.id at the same time.
	 *
	 * Conclusion: select query result would be empty.
	 */
	$result = DB::query('
		CREATE TEMPORARY TABLE tmp1 AS 
		SELECT u.id
		FROM users as u
		INNER JOIN users as u1 ON (u.id=u1.id AND u.id>u1.id)
		WHERE u.email=u1.email AND u.status=1 AND u1.status=1
		LIMIT 100');

	/**
	 * Calculate count of rows in temporary table from query #1.
	 *
	 * But the SELECT INTO statement does not return data to the client.
	 *
	 * Conclusion: $result_count variable would not contain count.
	 */
	$result_count = DB::query('SELECT COUNT(*) INTO return_count FROM tm1', 'one');

	/**
	 * Update user status for users from temp table in query #1.
	 *
	 * But result of select query #1 would be empty.
	 *
	 * Conclusion: no users would be updated.
	 */
	$result = DB::query('
		UPDATE users AS u
		SET status = 2
		FROM (
			SELECT id FROM tmp1
		) AS t
		WHERE u.id=t.id;');

	/**
	 * Delete temporary table created in query #1
	 */
	$result = DB::query('DROP TABLE tmp1');

	/**
	 * $result_count would not contain actual count.
	 *
	 * Conclusion: loop would stop after first iteration
	 */
	if ($result_count < 100) break;
	// echo $i . '';
}
