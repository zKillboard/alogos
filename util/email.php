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

// Check for queues going to end in the next 24 hours
$queues = Db::query("select * from skq_character_info where queueFinishes > now() and queueFinishes < date_add(now(), interval 24 hour)");
foreach($queues as $queue) {
	// Need to get the user info
	$name = $queue["characterName"];
	$api = Db::queryRow("select * from skq_api where keyRowID = :keyRowID", array(":keyRowID" => $queue["keyRowID"]));
	$userID = $api["userID"];
	$userInfo = Db::queryRow("select * from skq_users where id = :userID", array(":userID" => $userID));
	$email = $userInfo["email"];
	$subject = "$name skill notification";
	$body = "Your character, $name, has less than 24 hours remaining in their skill queue.\n\n-- SkillQ.net";
	$event = "24hr:$name";
	try {
		Db::execute("insert into skq_email_history (email, event) values (:email, :event)", array(":email" => $email, ":event" => $event));
		Email::create($email, $subject, $body);
	} catch (Exception $ex) {
		continue;
	}
}
