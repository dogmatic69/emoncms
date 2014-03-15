<?php 

/*

    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

*/

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

if (!db_check($mysqli, $database)) {
    db_schema_setup($mysqli, load_db_schema(), true);
}

function db_schema_setup($mysqli, $schema, $apply) {
    $operations = array();
    while ($table = key($schema)) {
        // if table exists:
        $result = $mysqli->query(sprintf('SHOW TABLES LIKE "%s"', $table));
        if ($result != null  && $result->num_rows == 1) {
            // $out[] = array('Table',$table,"ok");
            //-----------------------------------------------------
            // Check table fields from schema
            //-----------------------------------------------------
            while ($field = key($schema[$table])) {
                extract(schema_extract($schema[$table][$field]), EXTR_OVERWRITE);
                if ($default === null) {
                    unset($default);
                }

                // if field exists:
                $result = $mysqli->query(sprintf('SHOW COLUMNS FROM `%s` LIKE "%s"', $table, $field));
                if ($result->num_rows == 0) {
                    $query = sprintf('ALTER TABLE `%s` ADD `%s` %s', $table, $field, $type);
                    if ($null) {
                        $query .= ' NOT NULL';
                    }
                    if (isset($default)) {
                        $query .= sprintf(' DEFAULT "%s"', $default);
                    }
                    $operations[] = $query;
                    if ($apply) {
                        $mysqli->query($query);
                    }
                } else {
                    $result = $mysqli->query(sprintf('DESCRIBE %s `%s`', $table, $field));
                    $array = $result->fetch_array();
                    $query = '';

                    if ($array['Type'] != $type) {
                        $query .= ';';
                    }
                    if (isset($default) && $array['Default'] != $default) {
                        $query .= sprintf(' Default "%s"', $default);
                    }
                    if ($array['Null'] != $null && $null == 'NO') {
                        $query .= ' not null';
                    }
                    if ($array['Extra'] != $extra && $extra == 'auto_increment') {
                        $query .= ' auto_increment';
                    }
                    if ($array['Key'] != $key && $key == 'PRI') {
                        $query .= ' primary key';
                    }

                    if ($query) {
                        $query = "ALTER TABLE $table MODIFY `$field` $type".$query;
                        $operations[] = $query;
                        if ($apply) {
                            $mysqli->query($query);
                        }
                    }
                } 

                next($schema[$table]);
            }
        } else {
            //-----------------------------------------------------
            // Create table from schema
            //-----------------------------------------------------
            $inner = '';
            while ($field = key($schema[$table])) {
                extract(schema_extract($schema[$table][$field]), EXTR_OVERWRITE);

                $inner .= sprintf('`%s` %s', $field, $type);
                if ($default) {
                    $inner .= sprintf(' Default %s', $default);
                }
                if ($null == 'NO') {
                    $inner .= ' not null';
                }
                if ($extra) {
                    $inner .= ' auto_increment';
                }
                if ($key) {
                    $inner .= ' primary key';
                }

                next($schema[$table]);
                if (key($schema[$table])) {
                    $inner .= ', ';
                }
            }
            if ($inner) {
                $query = sprintf('CREATE TABLE %s (%s) ENGINE=MYISAM', $table, $inner);
                $operations[] = $query;
                if ($apply) {
                    $mysqli->query($query);
                }
            }
        }
        next($schema);
    }
    return $operations;
}

/**
 * extract the details of the schema
 *
 * @param array $field the field details
 *
 * @return array
 */
function schema_extract($field) {
    return array(
        'type' => $field['type'],
        'null' => isset($field['Null']) ? $field['Null'] : 'YES',
        'key' => isset($field['Key']) ? $field['Key'] : null,
        'default' => isset($field['default']) ? $field['default'] : null,
        'extra' => isset($field['Extra']) ? $field['Extra'] : null,
    );
}
