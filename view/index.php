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

$maxDate = Db::queryField("select max(logoReleased) maxDate from al_alliances", "maxDate", array(), 300);
$latest = Db::query("select allianceID, shortName, allianceName from al_alliances where logoReleased = :maxDate order by allianceCreation", array(":maxDate" => $maxDate), 300);

$allis = Db::query("select allianceID, shortName, allianceName, if (allianceCreation is null, null, concat(year(allianceCreation), ' ', monthname(allianceCreation))) logoReleased from al_alliances where logoReleased is not null order by allianceCreation desc", array(), 300);

$app->render("index.html", array("allis" => $allis, "latest" => $latest, "maxDate" => $maxDate));
