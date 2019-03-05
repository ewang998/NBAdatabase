<form method="POST" action="manager-view.php">
<p><input type="submit" value="Return to Manager View" name="managerview"></p>
</form>

<p>Update a team's stats by filling in the following:</p>

<form method="POST" action="manager-teamstats.php">
<p>
<p><input type="text" name="insTeamStatsID" size="6"> tsID (char(50))</p>
<p><input type="text" name="insTeamID" size="6"> teamID (char(50))</p>
<p><input type="text" name="insTeamWin" size="6"> Wins (number)</p>
<p><input type="text" name="insTeamLoss" size="6"> Losses (number)</p>
<input type="submit" value="Update Team Stats" name="updatesubmit">
</p>
</form>
<br>

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
	echo "<br><br>Here are the stats of all currently added teams:<br>";
	echo "<table>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["TSID"] . "</td><td>" . $row["TEAMID"] . "</td><td>" . $row["WIN"] . "</td><td>" . $row["LOSS"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('updatesubmit', $_POST)) {
		//Getting the values from user and insert data into the table
		$tuple = array (
			":bind1" => $_POST['insTeamStatsID'],
			":bind2" => $_POST['insTeamID'],
			":bind3" => $_POST['insTeamWin'],
			":bind4" => $_POST['insTeamLoss'],
		);
		$alltuples = array (
			$tuple
		);
		executeBoundSQL("update Team_Stats set win=:bind3 where tsID=:bind1 AND teamID=:bind2", $alltuples);
		executeBoundSQL("update Team_Stats set loss=:bind4 where tsID=:bind1 AND teamID=:bind2", $alltuples);
		OCICommit($db_conn);

	}

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: manager-teamstats.php");
	} else {
		$result = executePlainSQL("select * from Team_Stats");
		printResult($result);
	}

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}

?>
