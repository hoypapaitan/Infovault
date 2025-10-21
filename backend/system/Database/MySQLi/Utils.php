<?php

/**
 * This file is part of the CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CodeIgniter\Database\MySQLi;

use CodeIgniter\Database\BaseUtils;
use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * Utils for MySQLi
 */
class Utils extends BaseUtils
{
	/**
	 * List databases statement
	 *
	 * @var string
	 */
	protected $listDatabases = 'SHOW DATABASES';

	/**
	 * OPTIMIZE TABLE statement
	 *
	 * @var string
	 */
	protected $optimizeTable = 'OPTIMIZE TABLE %s';

	//--------------------------------------------------------------------

	/**
	 * Platform dependent version of the backup function.
	 *
	 * @param array|null $prefs
	 *
	 * @return mixed
	 */
	public function _backup(array $prefs = null)
	{
		// throw new DatabaseException('Unsupported feature of the database platform you are using.');
		// Currently unsupported
		//---return $this->db->display_error('db_unsuported_feature');

		if (count($prefs) == 0)
		{
			return FALSE;
		}

		// Extract the prefs for simplicity
		extract($prefs);

		// Build the output
		$output = '';
		foreach ((array)$tables as $table)
		{
			// Is the table in the "ignore" list?
			if (in_array($table, (array)$ignore, TRUE))
			{
				continue;
			}

			// Get the table schema
			$query = $this->db->query("SHOW CREATE TABLE `".$this->db->database.'`.`'.$table.'`');

			// No result means the table name was invalid
			if ($query === FALSE)
			{
				continue;
			}

			// Write out the table schema
			$output .= '#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;

			if ($add_drop == TRUE)
			{
				$output .= 'DROP TABLE IF EXISTS '.$table.';'.$newline.$newline;
			}

			$i = 0;
			$result = $query->getResult();
			foreach ($result[0] as $val)
			{
				if ($i++ % 2)
				{
					$output .= $val.';'.$newline.$newline;
				}
			}

			// If inserts are not needed we're done...
			if ($add_insert == FALSE)
			{
				continue;
			}

			// Grab all the data from the current table
			$query = $this->db->query("SELECT * FROM $table");

			if ($query->getNumRows() == 0)
			{
				continue;
			}

			// Fetch the field names and determine if the field is an
			// integer type.  We use this info to decide whether to
			// surround the data with quotes or not

			$i = 0;
			$field_str = '';
			$is_int = array();
			while ($field = $query->getFieldData() and $i < count($field))
			{
				
				// Most versions of MySQL store timestamp as a string
				$is_int[$i] = (in_array(
				strtolower($field[$i]->type_name),
				array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'long', 'timestamp', 'blob', 'var_string'), //, 'timestamp'),
				TRUE)
				) ? TRUE : FALSE;

				// Create a string of field names
				$field_str .= '`'.$field[$i]->name.'`, ';
				$i++;
			}

			// Trim off the end comma
			$field_str = preg_replace( "/, $/" , "" , $field_str);


			// Build the insert string
			foreach ($query->getResult() as $row)
			{
				$val_str = '';

				$i = 0;
				foreach ($row as $v)
				{
					// Is the value NULL?
					if ($v === NULL)
					{
						$val_str .= 'NULL';
					}
					else
					{
						// Escape the data if it's not an integer
						if ($is_int[$i] == FALSE)
						{
							$val_str .= $this->db->escape($v);
						}
						else
						{
							$val_str .= $v;
						}
					}

					// Append a comma
					$val_str .= ', ';
					$i++;
				}

				// Remove the comma at the end of the string
				$val_str = preg_replace( "/, $/" , "" , $val_str);

				// Build the INSERT string
				$output .= 'INSERT INTO '.$table.' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
			}

			$output .= $newline.$newline;
		}

		return $output;
	}

	//--------------------------------------------------------------------
}
