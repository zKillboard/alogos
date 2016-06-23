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
	$result = Db::query("select * from al_alliances where memberCount > 0 and lastChecked < date_sub(now(), interval 12 hour) and logoReleased is null", [], 0);

	$count = 0;
	foreach($result as $row) {
		$count ++;
		$id = $row["allianceID"];
		$name = $row["allianceName"];

		$url = "https://image.eveonline.com/Alliance/{$id}_128.png";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Fetcher for http://logos.zzeve.com");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout in seconds
		$logo = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		Db::execute("update al_alliances set lastChecked = now() where allianceID = :id", array(":id" => $id));
		echo "$id $httpCode\n";
		if ($httpCode == 302) {
			Db::execute("update al_alliances set logoReleased = null where allianceID = :id", array(":id" => $id));
			continue;
		}
		Db::execute("update al_alliances set logoReleased = now() where allianceID = :id and logoReleased is null", array(":id" => $id));
	}
}

function updateAlliances() {
	$allianceCount = 0;

	Db::execute("update al_alliances set memberCount = 0");
	$pheal = new Pheal();
	$pheal->scope = "eve";

	$list = $pheal->AllianceList();
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
