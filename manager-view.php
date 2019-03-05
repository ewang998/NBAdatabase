<style>
 .header img {
  float: left;
  width: 40px;
  height: 80px;
  background: #555;
}

.header h1 {
	position: relative;
	top: -15px;
  left: 20px;
  font-family:Helvetica;
}
</style>

<head>
        <title>Manage Database</title>
</head>

<body>
<div class="header">
  <img src="https://theundefeated.com/wp-content/uploads/2017/05/nba-logo.png" alt="NBA logo" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <h1>Manage Database</h1>
</div>
<form method="POST" action="home.php">
<p><input type="submit" value="Return to Home" name="portal"></p>
</form>
<br>

<p>To reset the database, press the "Reset" button.</p>

<form method="POST" action="manager-view.php">
<p><input type="submit" value="Reset" name="reset"></p>
</form>
<br>
<p>Otherwise, select what you would like to do:</p>

<form method="POST" action="manager-players.php">
<p><input type="submit" value="Manage Players" name="showplayers"></p>
</form>

<form method="POST" action="manager-coaches.php">
<p><input type="submit" value="Manage Coaches" name="showcoaches"></p>
</form>

<form method="POST" action="manager-contracts.php">
<p><input type="submit" value="Manage Contracts" name="showcontracts"></p>
</form>

<form method="POST" action="manager-teams.php">
<p><input type="submit" value="Teams (for debugging only)" name="showteams"></p>
</form>

<form method="POST" action="manager-playerview.php">
<p><input type="submit" value="View Players" name="viewplayers"></p>
</form>

<form method="POST" action="manager-playerstats.php">
<p><input type="submit" value="Update Player Stats" name="updateplayerstats"></p>
</form>

<form method="POST" action="manager-teamstats.php">
<p><input type="submit" value="Update Team Stats" name="updateteamstats"></p>
</form>

<form method="POST" action="manager-search.php">
<p><input type="submit" value="Search" name="search"></p></p>
</form>

</form>
<form method="POST" action="manager-groupby.php">
<p><input type="submit" value="Search for average salary" name="searchgroup"></p></p>
</form>

<?php

//this tells the system that it's no longer just parsing
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_u0n1b", "a12399168", "dbhost.ugrad.cs.ubc.ca:1522/ug");

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	//echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn); // For OCIParse errors pass the
		// connection handle
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		$e = oci_error($statement); // For OCIExecute errors pass the statementhandle
		echo htmlentities($e['message']);
		$success = False;
	} else {

	}
	return $statement;

}

function executeBoundSQL($cmdstr, $list) {
	/* Sometimes the same statement will be executed for several times ... only
	 the value of variables need to be changed.
	 In this case, you don't need to create the statement several times;
	 using bind variables can make the statement be shared and just parsed once.
	 This is also very useful in protecting against SQL injection.
      See the sample code below for how this functions is used */

	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	foreach ($list as $tuple) {
		foreach ($tuple as $bind => $val) {
			//echo $val;
			//echo "<br>".$bind."<br>";
			OCIBindByName($statement, $bind, $val);
			unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

		}
		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($statement); // For OCIExecute errors pass the statement handle
			echo htmlentities($e['message']);
			echo "<br>";
			$success = False;
		}
	}

}

function printResult($result) { //prints results from a select statement
	echo "<br>Got data from table tab1:<br>";
	echo "<table>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["PLAYERID"] . "</td><td>" . $row["TEAM"] . "</td><td>" . $row["FNAME"] . "</td><td>" . $row["LNAME"] . "</td><td>" . $row["POS"] . "</td><td>" . $row["AGE"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('reset', $_POST)) {
		// Drop old tables...
		echo "<br> dropping tables <br>";
		executePlainSQL("Drop table Player_Contract");
		executePlainSQL("Drop table Player_Stats");
		executePlainSQL("Drop table Game");
		executePlainSQL("Drop table Team_Stats");
		executePlainSQL("Drop table Player");
		executePlainSQL("Drop table Team");
		executePlainSQL("Drop table Coach");
		executePlainSQL("Drop view Positions");
		// Create new table...
		echo "<br> creating new tables <br>";
		executePlainSQL("create table Coach (coachID varchar2(30) not NULL, name varchar2(50) not NULL, experience number, primary key (coachID))");
		executePlainSQL("insert into Coach values ('hc1', 'Dwane Casey', 10)");
		executePlainSQL("insert into Coach values ('hc2', 'Brad Stevens', 6)");
		executePlainSQL("insert into Coach values ('hc3', 'Brett Brown', 6)");
		executePlainSQL("insert into Coach values ('hc4', 'Tyronn Lue', 4)");
		executePlainSQL("insert into Coach values ('hc5', 'Mike DAntoni', 15)");
		executePlainSQL("insert into Coach values ('hc6', 'Steve Kerr', 5)");
		executePlainSQL("insert into Coach values ('hc7', 'Terry Stotts', 11)");
		executePlainSQL("insert into Coach values ('hc8', 'Billy Donovan', 4)");
		executePlainSQL("insert into Coach values ('hc9', 'Gregg Popovich', 23)");
		executePlainSQL("insert into Coach values ('hc10', 'Stan Van Gundy', 12)");
		executePlainSQL("insert into Coach values ('hc11', 'Byron Scott', 15)");
		executePlainSQL("insert into Coach values ('hc12', 'George Karl', 27)");
		executePlainSQL("create table Team (teamID varchar2(50) not NULL, coachID varchar2(30), teamName varchar2(50) not NULL, location varchar2(50) not NULL, primary key (teamID), foreign key (coachID) references Coach(coachID) on delete set null)");
		executePlainSQL("insert into Team values ( 'TOR', 'hc1', 'Raptors', 'Toronto')");
		executePlainSQL("insert into Team values ( 'BOS', 'hc2', 'Celtics', 'Boston')");
		executePlainSQL("insert into Team values ( 'PHI', 'hc3', '76ers', 'Philadelphia')");
		executePlainSQL("insert into Team values ( 'CLE', 'hc4', 'Cavaliers', 'Cleveland')");
		executePlainSQL("insert into Team values ( 'HOU', 'hc5', 'Rockets', 'Houston')");
		executePlainSQL("insert into Team values ( 'GSW', 'hc6', 'Warriors', 'Oakland')");
		executePlainSQL("insert into Team values ( 'POR', 'hc7', 'Trailblazers', 'Portland')");
		executePlainSQL("insert into Team values ( 'OKC', 'hc8', 'Thunder', 'Oklahoma City')");
		executePlainSQL("insert into Team values ( 'SAS', 'hc9', 'Spurs', 'San Antonio')");
		executePlainSQL("insert into Team values ( 'DET', 'hc10', 'Pistons', 'Detroit')");
		executePlainSQL("create table Player (playerID varchar2(50) not NULL, team varchar2(50) not NULL, fname varchar2(50) not NULL, lname varchar2(50) not NULL, pos varchar2(50), age number not NULL, primary key (playerID), foreign key (team) references Team(teamID) on delete cascade)");
		executePlainSQL("insert into Player values ('tor3', 'TOR', 'OG', 'Anunoby', 'SF', 20)");
		executePlainSQL("insert into Player values ('tor7', 'TOR', 'Kyle', 'Lowry', 'PG', 31)");
		executePlainSQL("insert into Player values ('tor9', 'TOR', 'Serge', 'Ibaka', 'PF', 28)");
		executePlainSQL("insert into Player values ('tor10', 'TOR', 'DeMar', 'DeRozan', 'SG', 28)");
		executePlainSQL("insert into Player values ('tor17', 'TOR', 'Jonas', 'Valanciunas', 'C', 25)");
		executePlainSQL("insert into Player values ('bos0', 'BOS', 'Jayson', 'Tatum', 'PG', 19)");
		executePlainSQL("insert into Player values ('bos7', 'BOS', 'Jaylen', 'Brown', 'SG', 21)");
		executePlainSQL("insert into Player values ('bos11', 'BOS', 'Kyrie', 'Irving', 'PG', 25)");
		executePlainSQL("insert into Player values ('bos12', 'BOS', 'Terry', 'Rozier', 'PG', 23)");
		executePlainSQL("insert into Player values ('bos42', 'BOS', 'Al', 'Horford', 'C', 31)");
		executePlainSQL("insert into Player values ('cle0', 'CLE', 'Kevin', 'Love', 'PF', 29)");
		executePlainSQL("insert into Player values ('cle23', 'CLE', 'LeBron', 'James', 'SF', 33)");
		executePlainSQL("insert into Player values ('det0', 'DET', 'Andre', 'Drummond', 'C', 24)");
		executePlainSQL("insert into Player values ('det23', 'DET', 'Blake', 'Griffin', 'PF', 28)");
		executePlainSQL("insert into Player values ('hou3', 'HOU', 'Chris', 'Paul', 'PG', 32)");
		executePlainSQL("insert into Player values ('hou13', 'HOU', 'James', 'Harden', 'SG', 28)");
		executePlainSQL("insert into Player values ('gsw30', 'GSW', 'Stephen', 'Curry', 'PG', 29)");
		executePlainSQL("insert into Player values ('gsw35', 'GSW', 'Kevin', 'Durant', 'SF', 29)");
		executePlainSQL("insert into Player values ('por0', 'POR', 'Damian', 'Lillard', 'PG', 27)");
		executePlainSQL("insert into Player values ('por3', 'POR', 'CJ', 'McCollum', 'SG', 26)");
		executePlainSQL("insert into Player values ('okc0', 'OKC', 'Russell', 'Westbrook', 'PG', 29)");
		executePlainSQL("insert into Player values ('okc13', 'OKC', 'Paul', 'George', 'SF', 27)");
		executePlainSQL("insert into Player values ('sas2', 'SAS', 'Kawhi', 'Leonard', 'SF', 26)");
		executePlainSQL("insert into Player values ('sas12', 'SAS', 'LaMarcus', 'Aldridge', 'C', 32)");
		executePlainSQL("insert into Player values ('phi12', 'PHI', 'Joel', 'Embiid', 'C', 23)");
		executePlainSQL("insert into Player values ('phi25', 'PHI', 'Ben', 'Simmons', 'PG', 21)");
		executePlainSQL("insert into Player values ('phi33', 'PHI', 'Robert', 'Covington', 'SF', 27)");
		executePlainSQL("create table Team_Stats (tsID varchar2(50) not NULL, teamID varchar2(50) not NULL, win number not NULL, loss number not NULL, primary key (tsID, teamID), foreign key (teamID) references Team(teamID) on delete cascade, check (win >= 0 And win <= 82), check (loss >= 0 and loss <= 82), check ((win + loss) <= 82))");
		executePlainSQL("insert into Team_Stats values ('ts1', 'TOR', 59, 23)");
		executePlainSQL("insert into Team_Stats values ('ts2', 'BOS', 55, 27)");
		executePlainSQL("insert into Team_Stats values ('ts3', 'PHI', 52, 30)");
		executePlainSQL("insert into Team_Stats values ('ts4', 'CLE', 50, 32)");
		executePlainSQL("insert into Team_Stats values ('ts5', 'HOU', 65, 17)");
		executePlainSQL("insert into Team_Stats values ('ts6', 'GSW', 58, 24)");
		executePlainSQL("insert into Team_Stats values ('ts7', 'POR', 49, 33)");
		executePlainSQL("insert into Team_Stats values ('ts8', 'OKC', 48, 34)");
		executePlainSQL("insert into Team_Stats values ('ts9', 'SAS', 47, 35)");
		executePlainSQL("insert into Team_Stats values ('ts10', 'DET', 39, 43)");
		executePlainSQL("create table Game (gameID number not NULL, homeTeamID varchar2(50) not NULL, awayTeamID varchar2(50) not NULL, homeScore number not NULL, awayScore number not NULL, primary key (gameID), foreign key (homeTeamID) references Team(teamID) on delete cascade, foreign key (awayTeamID) references Team(teamID) on delete cascade)");
		executePlainSQL("insert into Game values (2017102101, 'TOR', 'PHI', 128, 94)");
		executePlainSQL("insert into Game values (2017111601, 'BOS', 'GSW', 92, 88)");
		executePlainSQL("insert into Game values (2018020301, 'CLE', 'HOU', 88, 120)");
		executePlainSQL("insert into Game values (2018032501, 'OKC', 'POR', 105, 108)");
		executePlainSQL("insert into Game values (2017120401, 'SAS', 'DET', 96, 93)");
		executePlainSQL("create table Player_Stats (sID varchar2(50) not NULL, player varchar2(50) not NULL, ppg number, rpg number, apg number, primary key (sID, player), foreign key (player) references Player(playerID) on delete cascade)");
		executePlainSQL("insert into Player_Stats values ('dderozan17', 'tor10', 23.0, 3.9, 5.2)");
		executePlainSQL("insert into Player_Stats values ('klowry17', 'tor7', 16.2, 5.6, 6.9)");
		executePlainSQL("insert into Player_Stats values ('sibaka17', 'tor9', 12.6, 6.3, 0.8)");
		executePlainSQL("insert into Player_Stats values ('jvalanciunas17', 'tor17', 12.7, 8.6, 1.1)");
		executePlainSQL("insert into Player_Stats values ('og17', 'tor3', 5.9, 2.5, 0.7)");
		executePlainSQL("insert into Player_Stats values ('kirving17', 'bos11', 24.4, 3.8, 5.1)");
		executePlainSQL("insert into Player_Stats values ('jbrown17', 'bos7', 14.5, 4.9, 1.6)");
		executePlainSQL("insert into Player_Stats values ('jtatum17', 'bos0', 13.9, 5.0, 1.6)");
		executePlainSQL("insert into Player_Stats values ('ahorford17', 'bos42', 12.9, 7.4, 4.7)");
		executePlainSQL("insert into Player_Stats values ('trozier17', 'bos12', 11.3, 4.7, 2.9)");
		executePlainSQL("insert into Player_Stats values ('jembiid17', 'phi12', 22.9, 11.0, 3.2)");
		executePlainSQL("insert into Player_Stats values ('bsimmons17', 'phi25', 15.8, 8.1, 8.2)");
		executePlainSQL("insert into Player_Stats values ('rcovington17', 'phi33', 12.6, 5.4, 2.0)");
		executePlainSQL("insert into Player_Stats values ('lbj17', 'cle23', 27.5, 8.6, 9.1)");
		executePlainSQL("insert into Player_Stats values ('klove17', 'cle0', 17.6, 9.3, 1.7)");
		executePlainSQL("insert into Player_Stats values ('bgriffin17', 'det23', 19.8, 6.6, 6.2)");
		executePlainSQL("insert into Player_Stats values ('adrummond17', 'det0', 15.0, 16.0, 3.0)");
		executePlainSQL("insert into Player_Stats values ('jharden17', 'hou13', 30.4, 5.4, 8.8)");
		executePlainSQL("insert into Player_Stats values ('cpaul17', 'hou3', 18.6, 5.4, 7.9)");
		executePlainSQL("insert into Player_Stats values ('scurry17', 'gsw30', 26.4, 5.1, 6.1)");
		executePlainSQL("insert into Player_Stats values ('kdurant17', 'gsw35', 26.4, 6.8, 5.4)");
		executePlainSQL("insert into Player_Stats values ('dlillard17', 'por0', 26.9, 4.5, 6.6)");
		executePlainSQL("insert into Player_Stats values ('cjmccollum17', 'por3', 21.4, 4.0, 3.4)");
		executePlainSQL("insert into Player_Stats values ('rwestbrook17', 'okc0', 25.4, 10.1, 10.3)");
		executePlainSQL("insert into Player_Stats values ('pgeorge17', 'okc13', 21.9, 5.7, 3.3)");
		executePlainSQL("insert into Player_Stats values ('laldridge17', 'sas12', 23.1, 8.5, 2.0)");
		executePlainSQL("insert into Player_Stats values ('kleonard17', 'sas2', 16.2, 4.7, 2.3)");
		executePlainSQL("create table Player_Contract (contractID varchar2(30) not NULL, tID varchar2(50) not NULL, pID varchar2(50) not NULL, yearlySal number, yearsLeft number, primary key (contractID, tID, pID), foreign key (pID) references Player(playerID) on delete cascade, foreign key (tID) references Team(teamID) on delete cascade)");
		executePlainSQL("insert into Player_Contract values('pc1', 'TOR', 'tor10', 27739975, 3)");
		executePlainSQL("insert into Player_Contract values('pc2', 'TOR', 'tor7', 28903704, 2)");
		executePlainSQL("insert into Player_Contract values('pc3', 'TOR', 'tor9', 20061729, 2)");
		executePlainSQL("insert into Player_Contract values('pc4', 'TOR', 'tor17', 15460675, 2)");
		executePlainSQL("insert into Player_Contract values('pc5', 'TOR', 'tor3', 1645200, 4)");
		executePlainSQL("insert into Player_Contract values('pc6', 'BOS', 'bos11', 18868626, 1)");
		executePlainSQL("insert into Player_Contract values('pc7', 'BOS', 'bos7', 4956480, 2)");
		executePlainSQL("insert into Player_Contract values('pc8', 'BOS', 'bos0', 5645400, 3)");
		executePlainSQL("insert into Player_Contract values('pc9', 'BOS', 'bos42', 27734405, 2)");
		executePlainSQL("insert into Player_Contract values('pc10', 'BOS', 'bos12', 1988520, 1)");
		executePlainSQL("insert into Player_Contract values('pc11', 'PHI', 'phi12', 6100266, 3)");
		executePlainSQL("insert into Player_Contract values('pc12', 'PHI', 'phi25', 6168840, 2)");
		executePlainSQL("insert into Player_Contract values('pc13', 'PHI', 'phi33', 16698103, 4)");
		executePlainSQL("insert into Player_Contract values('pc14', 'CLE', 'cle23', 33285709, 0)");
		executePlainSQL("insert into Player_Contract values('pc15', 'CLE', 'cle0', 22642350, 0)");
		executePlainSQL("insert into Player_Contract values('pc16', 'DET', 'det23', 29512900, 4)");
		executePlainSQL("insert into Player_Contract values('pc17', 'DET', 'det0', 23775506, 3)");
		executePlainSQL("insert into Player_Contract values('pc18', 'HOU', 'hou13', 27739975, 3)");
		executePlainSQL("insert into Player_Contract values('pc19', 'HOU', 'hou3', 24268959, 0)");
		executePlainSQL("insert into Player_Contract values('pc20', 'GSW', 'gsw30', 34682550, 4)");
		executePlainSQL("insert into Player_Contract values('pc21', 'GSW', 'gsw35', 25000000, 1)");
		executePlainSQL("insert into Player_Contract values('pc22', 'POR', 'por0', 26153057, 3)");
		executePlainSQL("insert into Player_Contract values('pc23', 'POR', 'por3', 23962573, 3)");
		executePlainSQL("insert into Player_Contract values('pc24', 'OKC', 'okc0', 28299399, 3)");
		executePlainSQL("insert into Player_Contract values('pc25', 'OKC', 'okc13', 19300000, 0)");
		executePlainSQL("insert into Player_Contract values('pc26', 'SAS', 'sas12', 27739975, 3)");
		executePlainSQL("insert into Player_Contract values('pc27', 'SAS', 'sas2', 18868625, 1)");
		executePlainSQL("create view Positions as select p.pos pos, p.team team from Player p");
		OCICommit($db_conn);

	}

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: manager-view.php");
	} else {
                // Test display of the players.
		// $result = executePlainSQL("select * from Player");
		// printResult($result);
	}

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}

?>
