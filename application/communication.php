<?php

use think\Db;

function strexists($string, $find) {
    return !(strpos($string, $find) === FALSE);
}


function random($length, $numeric = FALSE) {
    $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    if ($numeric) {
        $hash = '';
    } else {
        $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length--;
    }
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}



/**
 * @param string $tablename
 * @return array
 * 获取表相关字段 索引等
 */
function db_table_schema($tablename = '') {
    $result =Db::query("SHOW TABLE STATUS LIKE '" .$tablename. "'");
    if(empty($result) || empty($result[0]['Create_time'])) {
        return array();
    }
    $result =$result[0];
    $ret['tablename'] = $result['Name'];
    $ret['charset'] = $result['Collation'];
    $ret['engine'] = $result['Engine'];
    $ret['increment'] = $result['Auto_increment'];
    $result = Db::query('SHOW FULL COLUMNS FROM ' .$tablename);
    foreach($result as $value) {
        $temp = array();
        $type = explode(" ", $value['Type'], 2);
        $temp['name'] = $value['Field'];
        $pieces = explode('(', $type[0], 2);
        $temp['type'] = $pieces[0];
        $temp['length'] = rtrim($pieces[1], ')');
        $temp['null'] = $value['Null'] != 'NO';
        $temp['signed'] = empty($type[1]);
        $temp['increment'] = $value['Extra'] == 'auto_increment';
        $ret['fields'][$value['Field']] = $temp;
    }
    $result = Db::query("SHOW INDEX FROM " . $tablename);
    foreach($result as $value) {
        //  $ret['indexes'][$value['Key_name']]['name'] = $value['Key_name'];
        $item['type'] = ($value['Key_name'] == 'PRIMARY') ? 'primary' : ($value['Non_unique'] == 0 ? 'unique' : 'index');
        $item['fields'][] = $value['Column_name'];
        $ret['indexes'][]=$item;
    }
    return $ret;
}


function db_table_create_sql($schema) {
    $pieces = explode('_', $schema['charset']);
    $charset = $pieces[0];
    $engine = $schema['engine'];
    //$schema['tablename'] = str_replace('diygw_', config('prefix'), $schema['tablename']);
    $sql = "CREATE TABLE IF NOT EXISTS `{$schema['tablename']}` (\n";
    foreach ($schema['fields'] as $value) {
        $piece = _db_build_field_sql($value);
        $sql .= "`{$value['name']}` {$piece},\n";
    }
    foreach ($schema['indexes'] as $value) {
        $fields = implode('`,`', $value['fields']);
        if($value['type'] == 'index') {
            $sql .= "KEY `{$value['name']}` (`{$fields}`),\n";
        }
        if($value['type'] == 'unique') {
            $sql .= "UNIQUE KEY `{$value['name']}` (`{$fields}`),\n";
        }
        if($value['type'] == 'primary') {
            $sql .= "PRIMARY KEY (`{$fields}`),\n";
        }
    }
    $sql = rtrim($sql);
    $sql = rtrim($sql, ',');

    $sql .= "\n) ENGINE=$engine DEFAULT CHARSET=$charset;\n\n";
    return $sql;
}


function db_schema_compare($table1, $table2) {
    $table1['charset'] == $table2['charset'] ? '' : $ret['diffs']['charset'] = true;

    $fields1 = array_keys($table1['fields']);
    $fields2 = array_keys($table2['fields']);
    $diffs = array_diff($fields1, $fields2);
    if(!empty($diffs)) {
        $ret['fields']['greater'] = array_values($diffs);
    }
    $diffs = array_diff($fields2, $fields1);
    if(!empty($diffs)) {
        $ret['fields']['less'] = array_values($diffs);
    }
    $diffs = array();
    $intersects = array_intersect($fields1, $fields2);
    if(!empty($intersects)) {
        foreach($intersects as $field) {
            if($table1['fields'][$field] != $table2['fields'][$field]) {
                $diffs[] = $field;
            }
        }
    }
    if(!empty($diffs)) {
        $ret['fields']['diff'] = array_values($diffs);
    }

    $indexes1 = array_keys($table1['indexes']);
    $indexes2 = array_keys($table2['indexes']);
    $diffs = array_diff($indexes1, $indexes2);
    if(!empty($diffs)) {
        $ret['indexes']['greater'] = array_values($diffs);
    }
    $diffs = array_diff($indexes2, $indexes1);
    if(!empty($diffs)) {
        $ret['indexes']['less'] = array_values($diffs);
    }
    $diffs = array();
    $intersects = array_intersect($indexes1, $indexes2);
    if(!empty($intersects)) {
        foreach($intersects as $index) {
            if($table1['indexes'][$index] != $table2['indexes'][$index]) {
                $diffs[] = $index;
            }
        }
    }
    if(!empty($diffs)) {
        $ret['indexes']['diff'] = array_values($diffs);
    }

    return $ret;
}

function db_table_fix_sql($schema1, $schema2, $strict = false) {
    if(empty($schema1)) {
        return array(db_table_create_sql($schema2));
    }
    $diff = $result = db_schema_compare($schema1, $schema2);
    if(!empty($diff['diffs']['tablename'])) {
        return array(db_table_create_sql($schema2));
    }
    $sqls = array();
    if(!empty($diff['diffs']['engine'])) {
        $sqls[] = "ALTER TABLE `{$schema1['tablename']}` ENGINE = {$schema2['engine']}";
    }

    if(!empty($diff['diffs']['charset'])) {
        $pieces = explode('_', $schema2['charset']);
        $charset = $pieces[0];
        $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DEFAULT CHARSET = {$charset}";
    }

    if(!empty($diff['fields'])) {
        if(!empty($diff['fields']['less'])) {
            foreach($diff['fields']['less'] as $fieldname) {
                $field = $schema2['fields'][$fieldname];
                $piece = _db_build_field_sql($field);
                if(!empty($field['rename']) && !empty($schema1['fields'][$field['rename']])) {
                    $sql = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['rename']}` `{$field['name']}` {$piece}";
                    unset($schema1['fields'][$field['rename']]);
                } else {
                    if($field['position']) {
                        $pos = ' ' . $field['position'];
                    }
                    $sql = "ALTER TABLE `{$schema1['tablename']}` ADD `{$field['name']}` {$piece}{$pos}";
                }
                $primary = array();
                $isincrement = array();
                if (strexists($sql, 'AUTO_INCREMENT')) {
                    $isincrement = $field;
                    $sql =  str_replace('AUTO_INCREMENT', '', $sql);
                    foreach ($schema1['fields'] as $field) {
                        if ($field['increment'] == 1) {
                            $primary = $field;
                            break;
                        }
                    }
                    if (!empty($primary)) {
                        $piece = _db_build_field_sql($primary);
                        if (!empty($piece)) {
                            $piece = str_replace('AUTO_INCREMENT', '', $piece);
                        }
                        $sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$primary['name']}` `{$primary['name']}` {$piece}";
                    }
                }
                $sqls[] = $sql;
            }
        }
        if(!empty($diff['fields']['diff'])) {
            foreach($diff['fields']['diff'] as $fieldname) {
                $field = $schema2['fields'][$fieldname];
                $piece = _db_build_field_sql($field);
                if(!empty($schema1['fields'][$fieldname])) {
                    $sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['name']}` `{$field['name']}` {$piece}";
                }
            }
        }
        if($strict && !empty($diff['fields']['greater'])) {
            foreach($diff['fields']['greater'] as $fieldname) {
                if(!empty($schema1['fields'][$fieldname])) {
                    $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$fieldname}`";
                }
            }
        }
    }

    if(!empty($diff['indexes'])) {
        if(!empty($diff['indexes']['less'])) {
            foreach($diff['indexes']['less'] as $indexname) {
                $index = $schema2['indexes'][$indexname];
                $piece = _db_build_index_sql($index);
                $sqls[] = "ALTER TABLE `{$schema1['tablename']}` ADD {$piece}";
            }
        }
        if(!empty($diff['indexes']['diff'])) {
            foreach($diff['indexes']['diff'] as $indexname) {
                $index = $schema2['indexes'][$indexname];
                $piece = _db_build_index_sql($index);

                $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP ".($indexname == 'PRIMARY' ? " PRIMARY KEY " : "INDEX {$indexname}").", ADD {$piece}";
            }
        }
        if($strict && !empty($diff['indexes']['greater'])) {
            foreach($diff['indexes']['greater'] as $indexname) {
                $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$indexname}`";
            }
        }
    }
    if (!empty($isincrement)) {
        $piece = _db_build_field_sql($isincrement);
        $sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$isincrement['name']}` `{$isincrement['name']}` {$piece}";
    }
    return $sqls;
}

function _db_build_index_sql($index) {
    $piece = '';
    $fields = implode('`,`', $index['fields']);
    if($index['type'] == 'index') {
        $piece .= " INDEX `{$index['name']}` (`{$fields}`)";
    }
    if($index['type'] == 'unique') {
        $piece .= "UNIQUE `{$index['name']}` (`{$fields}`)";
    }
    if($index['type'] == 'primary') {
        $piece .= "PRIMARY KEY (`{$fields}`)";
    }
    return $piece;
}

function _db_build_field_sql($field) {
    if(!empty($field['length'])) {
        $length = "({$field['length']})";
    } else {
        $length = '';
    }
    if (strpos(strtolower($field['type']), 'int') !== false || in_array(strtolower($field['type']) , array('decimal', 'float', 'dobule'))) {
        $signed = empty($field['signed']) ? ' unsigned' : '';
    } else {
        $signed = '';
    }
    if(empty($field['null'])) {
        $null = ' NOT NULL';
    } else {
        $null = '';
    }
    if(isset($field['default'])) {
        $default = " DEFAULT '" . $field['default'] . "'";
    } else {
        $default = '';
    }
    if($field['increment']) {
        $increment = ' AUTO_INCREMENT';
    } else {
        $increment = '';
    }
    return "{$field['type']}{$length}{$signed}{$null}{$default}{$increment}";
}

function db_table_schemas($table) {
    $dump = "DROP TABLE IF EXISTS {$table};\n";
    $sql = "SHOW CREATE TABLE {$table}";
    $row = pdo_fetch($sql);
    $dump .= $row['Create Table'];
    $dump .= ";\n\n";
    return $dump;
}

function db_table_insert_sql($tablename, $start, $size) {
    $data = '';
    $tmp = '';
    $sql = "SELECT * FROM {$tablename} LIMIT {$start}, {$size}";
    $result = db()->query($sql);
    if (!empty($result)) {
        foreach($result as $row) {
            $tmp .= '(';
            foreach($row as $k => $v) {
                $value = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $v);
                $tmp .= "'" . $value . "',";
            }
            $tmp = rtrim($tmp, ',');
            $tmp .= "),\n";
        }
        $tmp = rtrim($tmp, ",\n");
        $data .= "INSERT INTO {$tablename} VALUES \n{$tmp};\n";
        $datas = array (
            'data' => $data,
            'result' => $result
        );
        return $datas;
    } else {
        return false ;
    }
}

function is_error($data) {
    if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
        return false;
    } else {
        return true;
    }
}
function error($errno, $message = '') {
    return array(
        'errno' => $errno,
        'message' => $message,
    );
}
// +----------------------------------------------------------------------
// | Diygw
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 DIY官网 [ http://www.diygw.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.diygw.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/html580/diygw
// +----------------------------------------------------------------------

function ihttp_request($url, $post = '', $extra = array(), $timeout = 60) {
			if (function_exists('curl_init') && function_exists('curl_exec') && $timeout > 0) {
		$ch = ihttp_build_curl($url, $post, $extra, $timeout);
		if (is_error($ch)) {
			return $ch;
		}
		$data = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);
		if ($errno || empty($data)) {
			return error($errno, $error);
		} else {
			return ihttp_response_parse($data);
		}
	}
	$urlset = ihttp_parse_url($url, true);
	if (!empty($urlset['ip'])) {
		$urlset['host'] = $urlset['ip'];
	}
	
	$body = ihttp_build_httpbody($url, $post, $extra);
	
	if ($urlset['scheme'] == 'https') {
		$fp = ihttp_socketopen('ssl://' . $urlset['host'], $urlset['port'], $errno, $error);
	} else {
		$fp = ihttp_socketopen($urlset['host'], $urlset['port'], $errno, $error);
	}
	stream_set_blocking($fp, $timeout > 0 ? true : false);
	stream_set_timeout($fp, ini_get('default_socket_timeout'));
	if (!$fp) {
		return error(1, $error);
	} else {
		fwrite($fp, $body);
		$content = '';
		if($timeout > 0) {
			while (!feof($fp)) {
				$content .= fgets($fp, 512);
			}
		}
		fclose($fp);
		return ihttp_response_parse($content, true);
	}
}


function ihttp_get($url) {
	return ihttp_request($url);
}


function ihttp_post($url, $data) {
	$headers = array('Content-Type' => 'application/x-www-form-urlencoded');
	return ihttp_request($url, $data, $headers);
}


function ihttp_multi_request($urls, $posts = array(), $extra = array(), $timeout = 60) {
	if (!is_array($urls)) {
		return error(1, '请使用ihttp_request函数');
	}
	$curl_multi = curl_multi_init();
	$curl_client = $response = array();

	foreach ($urls as $i => $url) {
		if (isset($posts[$i]) && is_array($posts[$i])) {
			$post = $posts[$i];
		} else {
			$post = $posts;
		}
		if (!empty($url)) {
			$curl = ihttp_build_curl($url, $post, $extra, $timeout);
			if (is_error($curl)) {
				continue;
			}
			if (curl_multi_add_handle($curl_multi, $curl) === CURLM_OK) {
								$curl_client[] = $curl;
			}
		}
	}
	if (!empty($curl_client)) {
		$active = null;
		do {
			$mrc = curl_multi_exec($curl_multi, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);

		while ($active && $mrc == CURLM_OK) {
			if (curl_multi_select($curl_multi) != -1) {
				do {
					$mrc = curl_multi_exec($curl_multi, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			} else {
				return error(2, '请求失败，请检查URL');
			}
		}
	}
	
	foreach ($curl_client as $i => $curl) {
		$response[$i] = curl_multi_getcontent($curl);
		curl_multi_remove_handle($curl_multi, $curl);
	}
	curl_multi_close($curl_multi);
	return $response;
}

function ihttp_socketopen($hostname, $port = 80, &$errno, &$errstr, $timeout = 15) {
	$fp = '';
	if(function_exists('fsockopen')) {
		$fp = @fsockopen($hostname, $port, $errno, $errstr, $timeout);
	} elseif(function_exists('pfsockopen')) {
		$fp = @pfsockopen($hostname, $port, $errno, $errstr, $timeout);
	} elseif(function_exists('stream_socket_client')) {
		$fp = @stream_socket_client($hostname.':'.$port, $errno, $errstr, $timeout);
	}
	return $fp;
}


function ihttp_response_parse($data, $chunked = false) {
	$rlt = array();
	$headermeta = explode('HTTP/', $data);
	if (count($headermeta) > 2) {
		$data = 'HTTP/' . array_pop($headermeta);
	}
	$pos = strpos($data, "\r\n\r\n");
	$split1[0] = substr($data, 0, $pos);
	$split1[1] = substr($data, $pos + 4, strlen($data));
	
	$split2 = explode("\r\n", $split1[0], 2);
	preg_match('/^(\S+) (\S+) (.*)$/', $split2[0], $matches);
	$rlt['code'] = $matches[2];
	$rlt['status'] = $matches[3];
	$rlt['responseline'] = $split2[0];
	$header = explode("\r\n", $split2[1]);
	$isgzip = false;
	$ischunk = false;
	foreach ($header as $v) {
		$pos = strpos($v, ':');
		$key = substr($v, 0, $pos);
		$value = trim(substr($v, $pos + 1));
		if (is_array($rlt['headers'][$key])) {
			$rlt['headers'][$key][] = $value;
		} elseif (!empty($rlt['headers'][$key])) {
			$temp = $rlt['headers'][$key];
			unset($rlt['headers'][$key]);
			$rlt['headers'][$key][] = $temp;
			$rlt['headers'][$key][] = $value;
		} else {
			$rlt['headers'][$key] = $value;
		}
		if(!$isgzip && strtolower($key) == 'content-encoding' && strtolower($value) == 'gzip') {
			$isgzip = true;
		}
		if(!$ischunk && strtolower($key) == 'transfer-encoding' && strtolower($value) == 'chunked') {
			$ischunk = true;
		}
	}
	if($chunked && $ischunk) {
		$rlt['content'] = ihttp_response_parse_unchunk($split1[1]);
	} else {
		$rlt['content'] = $split1[1];
	}
	if($isgzip && function_exists('gzdecode')) {
		$rlt['content'] = gzdecode($rlt['content']);
	}

	$rlt['meta'] = $data;
	if($rlt['code'] == '100') {
		return ihttp_response_parse($rlt['content']);
	}
	return $rlt;
}

function ihttp_response_parse_unchunk($str = null) {
	if(!is_string($str) or strlen($str) < 1) {
		return false; 
	}
	$eol = "\r\n";
	$add = strlen($eol);
	$tmp = $str;
	$str = '';
	do {
		$tmp = ltrim($tmp);
		$pos = strpos($tmp, $eol);
		if($pos === false) {
			return false;
		}
		$len = hexdec(substr($tmp, 0, $pos));
		if(!is_numeric($len) or $len < 0) {
			return false;
		}
		$str .= substr($tmp, ($pos + $add), $len);
		$tmp  = substr($tmp, ($len + $pos + $add));
		$check = trim($tmp);
	} while(!empty($check));
	unset($tmp);
	return $str;
}


function ihttp_parse_url($url, $set_default_port = false) {
	if (empty($url)) {
		return error(1);
	}
	$urlset = parse_url($url);
	if (!empty($urlset['scheme']) && !in_array($urlset['scheme'], array('http', 'https'))) {
		return error(1, '只能使用 http 及 https 协议');
	}
	if (empty($urlset['path'])) {
		$urlset['path'] = '/';
	}
	if (!empty($urlset['query'])) {
		$urlset['query'] = "?{$urlset['query']}";
	}
	if (strexists($url, 'https://') && !extension_loaded('openssl')) {
		if (!extension_loaded("openssl")) {
			return error(1,'请开启您PHP环境的openssl', '');
		}
	}
	if (empty($urlset['host'])) {
		$current_url = parse_url($GLOBALS['_W']['siteroot']);
		$urlset['host'] = $current_url['host'];
		$urlset['scheme'] = $current_url['scheme'];
		$urlset['path'] = $current_url['path'] . 'web/' . str_replace('./', '', $urlset['path']);
		$urlset['ip'] = '127.0.0.1';
	} else if (! ihttp_allow_host($urlset['host'])){
		return error(1, 'host 非法');
	}
	
	if ($set_default_port && empty($urlset['port'])) {
		$urlset['port'] = $urlset['scheme'] == 'https' ? '443' : '80';
	}
	return $urlset;
}


function ihttp_allow_host($host) {
	global $_W;
	if (strexists($host, '@')) {
		return false;
	}
	$pattern = "/^(10|172|192|127)/";
	if (preg_match($pattern, $host) && isset($_W['setting']['ip_white_list'])) {
		$ip_white_list = $_W['setting']['ip_white_list'];
		if ($ip_white_list && isset($ip_white_list[$host]) && !$ip_white_list[$host]['status']) {
			return false;
		}
	}
	return true;
}


function ihttp_build_curl($url, $post, $extra, $timeout) {
	if (!function_exists('curl_init') || !function_exists('curl_exec')) {
		return error(1, 'curl扩展未开启');
	}
	
	$urlset = ihttp_parse_url($url);
	if (is_error($urlset)) {
		return $urlset;
	}
	
	if (!empty($urlset['ip'])) {
		$extra['ip'] = $urlset['ip'];
	}
	
	$ch = curl_init();
	if (!empty($extra['ip'])) {
		$extra['Host'] = $urlset['host'];
		$urlset['host'] = $extra['ip'];
		unset($extra['ip']);
	}
	curl_setopt($ch, CURLOPT_URL, $urlset['scheme'] . '://' . $urlset['host'] . ($urlset['port'] == '80' || empty($urlset['port']) ? '' : ':' . $urlset['port']) . $urlset['path'] . $urlset['query']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	@curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	if ($post) {
		if (is_array($post)) {
			$filepost = false;
						foreach ($post as $name => &$value) {
				if (version_compare(phpversion(), '5.5') >= 0 && is_string($value) && substr($value, 0, 1) == '@') {
					$post[$name] = new CURLFile(ltrim($value, '@'));
				}
				if ((is_string($value) && substr($value, 0, 1) == '@') || (class_exists('CURLFile') && $value instanceof CURLFile)) {
					$filepost = true;
				}
			}
			if (!$filepost) {
				$post = http_build_query($post);
			}
		}
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSLVERSION, 1);
	if (defined('CURL_SSLVERSION_TLSv1')) {
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
	}
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
	if (!empty($extra) && is_array($extra)) {
		$headers = array();
		foreach ($extra as $opt => $value) {
			if (strexists($opt, 'CURLOPT_')) {
				curl_setopt($ch, constant($opt), $value);
			} elseif (is_numeric($opt)) {
				curl_setopt($ch, $opt, $value);
			} else {
				$headers[] = "{$opt}: {$value}";
			}
		}
		if (!empty($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
	}
	return $ch;
}

function ihttp_build_httpbody($url, $post, $extra) {
	$urlset = ihttp_parse_url($url, true);
	if (is_error($urlset)) {
		return $urlset;
	}
	
	if (!empty($urlset['ip'])) {
		$extra['ip'] = $urlset['ip'];
	}
	
	$body = '';
	if (!empty($post) && is_array($post)) {
		$filepost = false;
		$boundary = random(40);
		foreach ($post as $name => &$value) {
			if ((is_string($value) && substr($value, 0, 1) == '@') && file_exists(ltrim($value, '@'))) {
				$filepost = true;
				$file = ltrim($value, '@');
	
				$body .= "--$boundary\r\n";
				$body .= 'Content-Disposition: form-data; name="'.$name.'"; filename="'.basename($file).'"; Content-Type: application/octet-stream'."\r\n\r\n";
				$body .= file_get_contents($file)."\r\n";
			} else {
				$body .= "--$boundary\r\n";
				$body .= 'Content-Disposition: form-data; name="'.$name.'"'."\r\n\r\n";
				$body .= $value."\r\n";
			}
		}
		if (!$filepost) {
			$body = http_build_query($post, '', '&');
		} else {
			$body .= "--$boundary\r\n";
		}
	}
	
	$method = empty($post) ? 'GET' : 'POST';
	$fdata = "{$method} {$urlset['path']}{$urlset['query']} HTTP/1.1\r\n";
	$fdata .= "Accept: */*\r\n";
	$fdata .= "Accept-Language: zh-cn\r\n";
	if ($method == 'POST') {
		$fdata .= empty($filepost) ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data; boundary=$boundary\r\n";
	}
	$fdata .= "Host: {$urlset['host']}\r\n";
	$fdata .= "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1\r\n";
	if (function_exists('gzdecode')) {
		$fdata .= "Accept-Encoding: gzip, deflate\r\n";
	}
	$fdata .= "Connection: close\r\n";
	if (!empty($extra) && is_array($extra)) {
		foreach ($extra as $opt => $value) {
			if (!strexists($opt, 'CURLOPT_')) {
				$fdata .= "{$opt}: {$value}\r\n";
			}
		}
	}
	if ($body) {
		$fdata .= 'Content-Length: ' . strlen($body) . "\r\n\r\n{$body}";
	} else {
		$fdata .= "\r\n";
	}
	return $fdata;
}


/*
    参数：
    $sql_path:sql文件路径；
    $old_prefix:原表前缀；
    $new_prefix:新表前缀；
    $separator:分隔符 参数可为";\n"或";\r\n"或";\r"
*/
function get_mysql_data($sql_path, $old_prefix = "", $new_prefix = "", $separator = ";\n")
{

    $commenter = array('#', '--');
    //判断文件是否存在
    if (!file_exists($sql_path))
        return false;

    $content = file_get_contents($sql_path);   //读取sql文件
    $content = str_replace(array($old_prefix, "\r"), array($new_prefix, "\n"), $content);//替换前缀

    //通过sql语法的语句分割符进行分割
    $segment = explode($separator, trim($content));

    //去掉注释和多余的空行
    $data = array();
    foreach ($segment as $statement) {
        $sentence = explode("\n", $statement);
        $newStatement = array();
        foreach ($sentence as $subSentence) {
            if ('' != trim($subSentence)) {
                //判断是会否是注释
                $isComment = false;
                foreach ($commenter as $comer) {
                    if (preg_match("/^(" . $comer . ")/is", trim($subSentence))) {
                        $isComment = true;
                        break;
                    }
                }
                //如果不是注释，则认为是sql语句
                if (!$isComment)
                    $newStatement[] = $subSentence;
            }
        }
        $data[] = $newStatement;
    }

    //组合sql语句
    foreach ($data as $statement) {
        $newStmt = '';
        foreach ($statement as $sentence) {
            $newStmt = $newStmt . trim($sentence) . "\n";
        }
        if (!empty($newStmt)) {
            $result[] = $newStmt;
        }
    }
    return $result;
}
