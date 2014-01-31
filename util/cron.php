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

require_once __DIR__ . "/../init.php";

updateAlliances();
detectLogos();

function detectLogos() {
	$result = Db::query("select * from al_alliances where logoReleased is null and memberCount > 0 order by allianceID desc, lastChecked", array(), 0);
	$size = count($result);
	//echo "$size to check...\n";
	$count = 0;
	foreach($result as $row) {
		$count ++;
		//echo ".";
		$id = $row["allianceID"];
		$name = $row["allianceName"];
		$logo = @file_get_contents("https://image.eveonline.com/Alliance/{$id}_128.png");
		if (strlen($logo) == 0) continue;
		$md5 = md5($logo);
		Db::execute("update al_alliances set lastChecked = now() where allianceID = :id", array(":id" => $id));
		//if ($count % 100 == 0) echo "\n$count / $size\n";
		if ($md5 == "3d691b2e000df264270745a68fdf047c") continue;
		echo "\n$id $name\n";
		Db::execute("update al_alliances set logoReleased = date(now()) where allianceID = :id", array(":id" => $id));
	}
}

function updateAlliances() {
		$allianceCount = 0;
		$corporationCount = 0;

		$pheal = Util::getPheal();
		$pheal->scope = "eve";
		$list = null;
		$exception = null;
		try {
				$list = $pheal->AllianceList();
		} catch (Exception $ex) {
				$exception = $ex;
		}
		if ($list != null && count($list->alliances) > 0) {
				foreach ($list->alliances as $alliance) {
						$allianceCount++;
						$allianceID = $alliance['allianceID'];
						$shortName = $alliance['shortName'];
						$name = $alliance['name'];
						$startDate = $alliance['startDate'];
						$cnt = $alliance["memberCount"];
						Db::execute("insert into al_alliances (allianceID, allianceName, allianceCreation, memberCount, shortName) 
										values (:id, :name, :date, :cnt, :shortName)
										on duplicate key update memberCount = :cnt, shortName = :shortName",
							array(":id" => $allianceID, ":name" => $name, ":date" => $startDate, ":cnt" => $cnt, ":shortName" => $shortName));
				}
		}
}
