<?php
/* Alliance Logos
 * Copyright (C) 2013 SquizzLabs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Password
{
	public static function genPassword($password)
	{
		for ($i = 0; $i <= 87421; $i++)
		{
			if ($i == 0)
				$pw = hash("sha256", $password);
			else
				$pw = hash("sha256", $pw);
		}
		return $pw;
	}

	public static function updatePassword($password)
	{
		$userID = user::getUserID();
		$password = self::genPassword($password);
		Db::execute("UPDATE zz_users SET password = :password WHERE id = :userID", array(":password" => $password, ":userID" => $userID));
		return "Updated password";
	}

	public static function checkPassword($password)
	{
		$userID = user::getUserID();
		$password = self::genPassword($password);
		$pw = Db::queryField("SELECT password FROM zz_users WHERE id = :userID", "password", array(":userID" => $userID));
		if ($pw == $password)
			return true;
		else
			return false;
	}
}
