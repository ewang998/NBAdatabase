<form method="POST" action="manager-view.php">
<p><input type="submit" value="Return to Manager View" name="managerview"></p>
</form>

<p>Update a player's stats by filling in the following:</p>

<form method="POST" action="manager-playerstats.php">
<p>
<p><input type="text" name="inssID" size="6"> sID (char(50))</p>
<p><input type="text" name="insPlayer" size="6"> Player (char(50))</p>
<p><input type="text" name="insPPG" size="6"> PPG (number)</p>
<p><input type="text" name="insRPG" size="6"> RPG (number)</p>
<p><input type="text" name="insAPG" size="6"> APG (number)</p>
<input type="submit" value="Update Player Stats" name="updatesubmit">
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
	echo "<br><br>Here are the stats of all currently added players:<br>";
	echo "<table>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["SID"] . "</td><td>" . $row["PLAYER"] . "</td><td>" . $row["PPG"] . "</td><td>" . $row["RPG"] . "</td><td>" . $row["APG"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('updatesubmit', $_POST)) {
		//Getting the values from user and insert data into the table
		$tuple = array (
			":bind1" => $_POST['inssID'],
			":bind2" => $_POST['insPlayer'],
			":bind3" => $_POST['insPPG'],
			":bind4" => $_POST['insRPG'],
			":bind5" => $_POST['insAPG']
		);
		$alltuples = array (
			$tuple
		);
		executeBoundSQL("update Player_Stats set ppg=:bind3 where sID=:bind1 AND player=:bind2", $alltuples);
		executeBoundSQL("update Player_Stats set rpg=:bind4 where sID=:bind1 AND player=:bind2", $alltuples);
		executeBoundSQL("update Player_Stats set apg=:bind5 where sID=:bind1 AND player=:bind2", $alltuples);
		OCICommit($db_conn);

	}

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: manager-playerstats.php");
	} else {
		$result = executePlainSQL("select * from Player_Stats");
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
