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

class UserGlobals extends Twig_Extension
{
	public function getName()
	{
		return "UserGlobals";
	}

	public function getGlobals()
	{
		$result = array();
		if (isset($_SESSION["loggedin"])) {
			$u = User::getUserInfo();
			$this->addGlobal($result, "sessionusername", $u["username"]);
			$this->addGlobal($result, "sessionuserid", $u["id"]);
			$this->addGlobal($result, "sessionadmin", (bool)$u["admin"]);
			$this->addGlobal($result, "sessionmoderator", (bool)$u["moderator"]);
			$this->addGlobal($result, "sessiontheme", UserConfig::get("theme"), "cyborg");
			global $characters;
			$characters = Db::query("select characterID, characterName from skq_api a left join skq_character_info i on (a.keyRowID = i.keyRowID) where a.userID = :userID and display = 1 order by skillsTrained desc, skillPoints desc, characterName",
								array(":userID" => $u["id"]), 1);
			$this->addGlobal($result, "characters", $characters);
		}
		return $result;
	}

	private function addGlobal(&$array, $key, $value, $defaultValue = null)
	{
		if ($value == null && $defaultValue == null) return;
		else if ($value == null) $array[$key] = $defaultValue;
		else $array[$key] = $value;
	}
}
