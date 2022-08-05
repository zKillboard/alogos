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
	$result = Db::query("select * from al_alliances where logoReleased is null");

	$count = 0;
	foreach($result as $row) {
		$count ++;
		$id = $row["allianceID"];
		$name = $row["allianceName"];

        $url = "https://images.evetech.net/alliances/$id/logo?size=128";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Fetcher for http://logos.zzeve.com");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout in seconds
		$logo = curl_exec($ch);
        $md5 = md5($logo);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		Db::execute("update al_alliances set lastChecked = now() where allianceID = :id", array(":id" => $id));
		if ($httpCode == 302) {
			Db::execute("update al_alliances set logoReleased = null where allianceID = :id", array(":id" => $id));
		}
		else if ($httpCode == 200 && $md5 != "ba1d1c8d4b383b6fff6355687c5b0733") {
            echo "$id $name $md5\n";
			Db::execute("update al_alliances set logoReleased = now() where allianceID = :id and logoReleased is null", array(":id" => $id));
		}
	}
}

function updateAlliances() {
	$allianceCount = 0;

	$alliRaw = @file_get_contents("https://esi.evetech.net/v1/alliances/");
	$alliances = json_decode($alliRaw, true);
	Db::execute("update al_alliances set memberCount = 0");
	foreach ($alliances as $allianceID) {
		$allianceCount++;

		$allianceRaw = @file_get_contents("https://esi.evetech.net/v3/alliances/$allianceID/");
		$alliance = json_decode($allianceRaw, true);
		if (!isset($alliance['name'])) continue;

		$shortName = $alliance['ticker'];
		$name = $alliance['name'];
		$startDate = $alliance['date_founded'];
		Db::execute("insert into al_alliances (allianceID, allianceName, allianceCreation, shortName) 
				values (:id, :name, :date, :shortName)
				on duplicate key update shortName = :shortName, allianceName = :name",
				array(":id" => $allianceID, ":name" => $name, ":date" => $startDate, ":shortName" => $shortName));
	}
}
