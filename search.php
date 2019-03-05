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
        <title>Search NBA Database</title>
</head>

<body>
<div class="header">
  <img src="https://theundefeated.com/wp-content/uploads/2017/05/nba-logo.png" alt="NBA logo" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <h1>Search NBA Database</h1>
</div>
<br>
<button onclick="location.href='fan.php'" type="button">Back to Portal</button>
<br>
<form method="POST" action="search.php">

<label><p><b>Search by Coach Name:</b></p></label>
<input type="text" name="coachSearch"><br>

<label><p><b>Search by Player:</b></p></label>
<p><tr><th>First Name</th>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<th>Last Name</th>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<th>Position </th>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<th>Age </th></tr></p>
<input type="text" name="FNPlayerSearch">
<input type="text" name="LNPlayerSearch">
<input type="text" name="position">
<input type="text" name="age"><br>

<label><p><b>Search by Team:</b></p></label>
<p><tr><th>Team Name</th>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<th>Location </th></tr></p>
<input type="text" name="teamSearch">
<input type="text" name="location"><br>

<label><p><b>Search by Team Stats:</b></p></label>
<p><tr><th>Min Win</th>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<th>Max Win</th>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<th>Min Loss</th>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<th>Max Loss</th></tr></p>
<input type="text" name="minWin">
<input type="text" name="maxWin">
<input type="text" name="minLoss">
<input type="text" name="maxLoss"><br>

<label><p><b>Search by Player Stats:</b></p></label>
<p><tr><th>Min PPG</th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<th>Max PPG</th></tr></p>
<input type="text" name="minPPG">
<input type="text" name="maxPPG"><br>
<p><tr><th>Min RPG</th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<th>Max RPG</th></tr></p>
<input type="text" name="minRPG">
<input type="text" name="maxRPG"><br>
<p><tr><th>Min APG</th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<th>Max APG</th></tr></p>
<input type="text" name="minAPG">
<input type="text" name="maxAPG"><br>
<br>

<!-- Sorting Options -->
<?php 
	$sort2 = 't.teamName';
?>
Sort (Descending by default):
	<input type="radio" name="sort" <?php if (isset($sort2) && $sort2 == 't.teamName') echo "checked";?> value = "t.teamName">
	Team Name
	<input type="radio" name="sort" <?php if (isset($sort2) && $sort2 == 'p.age') echo "checked";?> value = "p.age">
	Player Age
	<input type="radio" name="sort" <?php if (isset($sort2) && $sort2 == 'ps.ppg') echo "checked";?> value = "ps.ppg">
	PPG
	<input type="radio" name="sort" <?php if (isset($sort2) && $sort2 == 'ps.rpg') echo "checked";?> value = "ps.rpg">
	RPG
	<input type="radio" name="sort" <?php if (isset($sort2) && $sort2 == 'ps.apg') echo "checked";?> value = "ps.apg">
	APG
	<input type="radio" name="sort" <?php if (isset($sort2) && $sort2 == 'ts.win') echo "checked";?> value = "ts.win">
	Win
	<input type="radio" name="sort" <?php if (isset($sort2) && $sort2 == 'ts.loss') echo "checked";?> value = "ts.loss">
	Loss
<br>
<br>

<input type="submit" name="searchDB" value="Search NBA DB">
</form>


<?php
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_v4w9a", "a14702147", "dbhost.ugrad.cs.ubc.ca:1522/ug");

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

function printSearch($result) { //prints results from a select statement
	echo "Showing all results from the database:<br><br>";
	echo "<table border=2 cellpadding=5px cellspacing=5px style=font-family:Arial; font-size:7px>";
	echo "<tr><th>Player First Name</th><th>Player Last Name</th><th>Position</th><th>Age</th><th>Points Per Game</th><th>Rebounds Per Game</th><th>Assists Per Game</th><th>City</th><th>Team</th><th>Win</th><th>Loss</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td align=center>" . $row[0] . "</td><td align=center>" . $row[1] . "</td><td align=center>" . $row[2] . "</td><td align=center>"
		. $row[3] . "</td><td align=center>" . $row[4] . "</td><td align=center>" . $row[5] . "</td><td align=center>" . $row[6] . "</td><td align=center>"
		. $row[7] . "</td><td align=center>" . $row[8] . "</td><td align=center>" . $row[9] . "</td><td align=center>" . $row[10] . "</td></tr>";
	}
	echo "</table>";

}

function printSearchCoach($result) { //prints results from a select statement
	echo "Showing all results from the database:<br><br>";
	echo "<table border=2 cellpadding=5px cellspacing=5px style=font-family:Arial; font-size:7px>";
	echo "<tr><th>Coach Name</th><th>Coach Experience (yrs)</th><th>Player First Name</th><th>Player Last Name</th><th>Position</th><th>Age</th><th>Points Per Game</th><th>Rebounds Per Game</th><th>Assists Per Game</th><th>City</th><th>Team</th><th>Win</th><th>Loss</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td align=center>" . $row[0] . "</td><td align=center>" . $row[1] . "</td><td align=center>" . $row[2] . "</td><td align=center>"
		. $row[3] . "</td><td align=center>" . $row[4] . "</td><td align=center>" . $row[5] . "</td><td align=center>" . $row[6] . "</td><td align=center>"
		. $row[7] . "</td><td align=center>" . $row[8] . "</td><td align=center>" . $row[9] . "</td><td align=center>" . $row[10] . "</td><td align=center>" . $row[11] . "</td><td align=center>" . $row[12] ."</td></tr>";
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('searchDB', $_POST)) {
		$coach = $_POST['coachSearch'];
		
		$player_fn = $_POST['FNPlayerSearch'];
		$player_ln = $_POST['LNPlayerSearch'];
		$position = $_POST['position'];
		$age = $_POST['age'];
		
		$teamName = $_POST['teamSearch'];
		$location = $_POST['location'];
		
		$minWin = $_POST['minWin'];
		$maxWin = $_POST['maxWin'];
		$minLoss = $_POST['minLoss'];
		$maxLoss = $_POST['maxLoss'];
		
		$minPPG = $_POST['minPPG'];
		$maxPPG = $_POST['maxPPG'];
		
		$minRPG = $_POST['minRPG'];
		$maxRPG = $_POST['maxRPG'];
		
		$minAPG = $_POST['minAPG'];
		$maxAPG = $_POST['maxAPG'];

		$sort = $_POST['sort'];

		$search = "SELECT p.firstName, p.lastName, p.position, p.age,
		ps.ppg, ps.rpg, ps.apg, 
		t.location, t.teamName, ts.win, ts.loss
		FROM Player p, Team t, Player_Stats ps, Team_Stats ts
		WHERE p.playerTeam = t.teamID AND p.playerID = ps.player AND t.teamID = ts.teamID";

		if($coach != "") {
			$searchCoach = "SELECT c.name, c.experience, p.firstName, p.lastName, p.position, p.age,
			ps.ppg, ps.rpg, ps.apg, 
			t.location, t.teamName, ts.win, ts.loss
			FROM Player p
			Join Team t on p.playerTeam = t.teamID
			Join Coach c on t.coachID = c.coachID
			Join Player_Stats ps on p.playerID = ps.player
			Join Team_Stats ts on ts.teamID = t.teamID
			Where c.name LIKE '" . $coach . "%'
			ORDER BY ".$sort." DESC";

			$result = executePlainSQL($searchCoach);
			printSearchCoach($result);
		} else {
		
		if ($player_fn != "") {
			$search .= " AND p.firstName LIKE '" . $player_fn . "%'";
		}

		if ($player_ln != "") {
			$search .= " AND p.lastName LIKE '" . $player_ln . "%'";
		}
		
		if ($teamName != "") {
			$search .= " AND t.teamName LIKE '" . $teamName . "%'";
		}
		
		if ($location != "") {
			$search .= " AND t.location LIKE '" . $location . "%'";
		}
		
		if ($position != "") {
			$search .= " AND p.position LIKE '" . $position . "%'";
		}

		if ($age != "") {
			$search .= " AND p.age = " . $age;
		}
		
		if ($minWin != "") {
			$search .= " AND ts.win > " . $minWin;
		}
		
		if ($maxWin != "") {
			$search .= " AND ts.win < " . $maxWin;
		}
		
		if ($minLoss != "") {
			$search .= " AND ts.loss > " . $minLoss;
		}
		
		if ($maxLoss != "") {
			$search .= " AND ts.loss < " . $maxLoss;
		}
		
		if ($minPPG != "") {
			$search .= " AND ps.ppg > " . $minPPG;
		}
		
		if ($maxPPG != "") {
			$search .= " AND ps.ppg < " . $maxPPG;
		}
		
		if ($minRPG != "") {
			$search .= " AND ps.rpg > " . $minRPG;
		}
		
		if ($maxRPG != "") {
			$search .= " AND ps.rpg < " . $maxRPG;
		}
		
		if ($minAPG != "") {
			$search .= " AND ps.apg > " . $minAPG;
		}
		
		if ($maxAPG != "") {
			$search .= " AND ps.apg < " . $maxAPG;
		}

		$search .= " ORDER BY ".$sort." DESC";

		$result = executePlainSQL($search);
		printSearch($result);
		}
	}
}
?>

