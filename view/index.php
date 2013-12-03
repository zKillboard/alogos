<?php

$maxDate = Db::queryField("select max(logoReleased) maxDate from al_alliances", "maxDate", array(), 300);
$latest = Db::query("select allianceID, shortName, allianceName from al_alliances where logoReleased = :maxDate order by allianceCreation", array(":maxDate" => $maxDate), 300);

$allis = Db::query("select allianceID, shortName, allianceName, if (allianceCreation is null, null, concat(year(allianceCreation), ' ', monthname(allianceCreation))) logoReleased from al_alliances where logoReleased is not null order by allianceCreation desc", array(), 300);

$app->render("index.html", array("allis" => $allis, "latest" => $latest, "maxDate" => $maxDate));
