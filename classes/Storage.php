<?php

class Storage
{
	public static function retrieve($locker, $default = null)
	{
		$contents = Db::queryField("select contents from skq_storage where locker = :locker", "contents", array(":locker" => $locker));
		if ($contents === null) return $default;
		return $contents;
	}

	public static function store($locker, $contents)
	{
		return Db::execute("replace into skq_storage (locker, contents) values (:locker, :contents)", array(":locker" => $locker, ":contents" => $contents));
	}
}
