<?php
/**
 * @ Author: Bill Minozzi
 * @ Copyright: 2020 www.BillMinozzi.com
 * @ Modified time: 2020-11-02 10:46:16 2024-06-17
 */
if (!defined("ABSPATH")) {
    exit();
}
class STOPBADBOTS_SSP
{
    /**
     * Create the data output array for the DataTables rows
     *
     *  @param  array $columns Column information array
     *  @param  array $data    Data from the SQL get
     *  @return array          Formatted data in a row based format
     */
    static function data_output($columns, $data)
    {
        $out = [];
        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = [];
            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];
                // Is there a formatter?
                if (isset($column["formatter"])) {
                    $row[$column["dt"]] = $column["formatter"](
                        $data[$i][$column["db"]],
                        $data[$i]
                    );
                } else {
                    $row[$column["dt"]] = $data[$i][$columns[$j]["db"]];
                }
            }
            $out[] = $row;
        }
        return $out;
    }
    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit($request, $columns)
    {
        $limit = "";
        if (isset($request["start"]) && $request["length"] != -1) {
            $limit =
                intval($request["start"]) . ", " . intval($request["length"]);
        }
        return $limit;
    }
    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL order by clause
     */

    static function order($request, $columns)
    {
        $order = "";
        if (isset($request["order"]) && count($request["order"])) {
            $orderBy = [];
            $dtColumns = self::pluck($columns, "dt");
            for ($i = 0, $ien = count($request["order"]); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request["order"][$i]["column"]);
                $requestColumn = $request["columns"][$columnIdx];
                $columnIdx = array_search($requestColumn["data"], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn["orderable"] == "true") {
                    $dir =
                        sanitize_text_field($request["order"][$i]["dir"]) ===
                        "asc"
                            ? "ASC"
                            : "DESC";
                    // Sanitize
                    if (trim(strlen($column["db"])) < 11) {
                        $orderBy[] = "`" . $column["db"] . "` " . $dir;
                    }
                }
            }
            if (count($orderBy)) {
                // $order = 'ORDER BY ' . implode(', ', $orderBy);
                $order = implode($orderBy);
                // error_log($order);
            }
        }
        return $order;
    }

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array  $request Data sent to server by DataTables
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array  $columns Column information array
     *  @return array          Server-side processing response array
     */
    static function simple($request, $table, $primaryKey, $columns)
    {
        global $wpdb;
        $str = trim($request["search"]["value"]);
        $limit = self::limit($request, $columns);
        $order = self::order($request, $columns);

        $orderfull = trim(str_replace("`", "", $order));

        $pos = strpos($orderfull, " ");
        $order = substr($orderfull, 0, $pos);
        // sanitize
        if (strlen($order > 11)) {
            $order = "date";
        }

        // sanitize
        $orderDirection = sanitize_sql_orderby(substr($orderfull, $pos + 1));
        $table = esc_sql($table);
        $order = sanitize_sql_orderby($order);
        $access = "1";

		$str = esc_sql($str);



        // Main query to actually get the data
        if (empty($str)) {
            // $limit = 5;
            /*
			$data = $wpdb->get_results(
			
			/*
			don-t work...
			$data = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT * FROM %i 
					WHERE access NOT LIKE %s 
					ORDER BY %s %s 
					LIMIT $limit",
					$table,
					'%' . $access . '%',
					$order,
					$orderDirection
				),
				ARRAY_A
			);
			*/

            $data = $wpdb->get_results(
                $wpdb->prepare(
                    "
					SELECT * FROM %i 
					WHERE access NOT LIKE %s 
					ORDER BY $order $orderDirection
					LIMIT $limit",
                    $table,
                    "%" . $access . "%"
                ),
                ARRAY_A
            );
 
		} else {

			$data = $wpdb->get_results(
				$wpdb->prepare(
					"
		        SELECT * FROM %i 
			WHERE 
				(date like %s or
				access like %s or
				referer like %s or
				url like %s or
				ua like %s or 
				method like %s or
				response like %s or 
				reason like %s or 
				ip like %s) and 
				access NOT LIKE %s
				ORDER BY $order $orderDirection
			LIMIT $limit ",
			        $table,
					'%' . $str . '%',
					'%' . $str . '%',
					'%' . $str . '%',
					'%' . $str . '%',
					'%' . $str . '%',
					'%' . $str . '%',
					'%' . $str . '%',
					'%' . $str . '%',
					'%' . $str . '%',
					'%' . $access . '%'
				),
				ARRAY_A
			);
		}


		$recordsFiltered = $wpdb->get_var(
			$wpdb->prepare(
				"
		SELECT   COUNT(*)  FROM %i 
		WHERE 
			(
			date like %s or
			access like %s or
			referer like %s or
			url like %s or
			ua like %s or 
			method like %s or
			response like %s or 
			reason like %s or 
			ip like %s) and 
			access NOT LIKE %s
					",
				$table,
				'%' . $str . '%',
				'%' . $str . '%',
				'%' . $str . '%',
				'%' . $str . '%',
				'%' . $str . '%',
				'%' . $str . '%',
				'%' . $str . '%',
				'%' . $str . '%',
				'%' . $str . '%',
				'%' . $access . '%'
			)
		);


		/*
		$recordsTotal = $wpdb->get_var(
			$wpdb->prepare("
		SELECT  COUNT(%s)  FROM  %i WHERE access NOT LIKE %s",
				$table,
				`$primaryKey`,
				'%' . $access . '%'
			)
		);
		*/

		/*
		$recordsTotal = $wpdb->get_var(
			$wpdb->prepare("
		SELECT  COUNT(%s)  FROM `$table` WHERE access NOT LIKE %s",
				`$primaryKey`,
				'%' . $access . '%'
			)
		);
		*/

		$recordsTotal = $wpdb->get_var(
			$wpdb->prepare("
				SELECT  COUNT(%s)  FROM %i WHERE access NOT LIKE %s",
				$primaryKey,
				$table,
				$access. '%'
			)
		);
		







		/*
		 * Output
		 */
		return array(
			'draw'            => isset( $request['draw'] ) ?
				intval( $request['draw'] ) :
				0,
			'recordsTotal'    => intval( $recordsTotal ),
			'recordsFiltered' => intval( $recordsFiltered ),
			'data'            => self::data_output( $columns, $data ),
		);
	}


    /*
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */
    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    static function fatal($msg)
    {
        echo wp_json_encode([
            "error" => esc_attr($msg),
        ]);
        exit(0);
    }
    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    static function pluck($a, $prop)
    {
        $out = [];
        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }
        return $out;
    }
    /**
     * Return a string from an array or a string
     *
     * @param  array|string $a Array to join
     * @param  string       $join Glue for the concatenation
     * @return string Joined string
     */
    static function _flatten($a, $join = " AND ")
    {
        if (!$a) {
            return "";
        } elseif ($a && is_array($a)) {
            return implode($join, $a);
        }
        return $a;
    }
}
