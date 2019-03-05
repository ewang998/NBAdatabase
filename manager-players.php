<form method="POST" action="manager-view.php">
<p><input type="submit" value="Return to Manager View" name="managerview"></p>
</form>

<p>Insert a player by filling in the form below:</p>

<form method="POST" action="manager-players.php">
<p>
<p><input type="text" name="insPlayerID" size="6"> Player ID (char(50))</p>
<p><input type="text" name="insPlayerTeam" size="6"> Team (char(50))</p>
<p><input type="text" name="insPlayerFirstName" size="6"> First Name (char(50))</p>
<p><input type="text" name="insPlayerLastName" size="6"> Last Name (char(50))</p>
<p><input type="text" name="insPlayerPosition" size="6"> Position (char(50))</p>
<p><input type="text" name="insPlayerAge" size="6"> Age (integer)</p>
<input type="submit" value="Insert Player" name="insertsubmit">
</p>
</form>
<br>

<p>Delete a player by filling in the form below:</p>

<form method="POST" action="manager-players.php">
<p>
<p><input type="text" name="insPlayerID" size="6"> Player ID (char(50))</p>
<input type="submit" value="Delete Player" name="deletesubmit">
</p>
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
	echo "<br><br>Here are all currently added players:<br>";
	echo "<table>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["PLAYERID"] . "</td><td>" . $row["TEAM"] . "</td><td>" . $row["FNAME"] . "</td><td>" . $row["LNAME"] . "</td><td>" . $row["POS"] . "</td><td>" . $row["AGE"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('insertsubmit', $_POST)) {
		//Getting the values from user and insert data into the table
		$tuple = array (
			":bind1" => $_POST['insPlayerID'],
			":bind2" => $_POST['insPlayerTeam'],
			":bind3" => $_POST['insPlayerFirstName'],
			":bind4" => $_POST['insPlayerLastName'],
			":bind5" => $_POST['insPlayerPosition'],
			":bind6" => $_POST['insPlayerAge']
		);
		$alltuples = array (
			$tuple
		);
		executeBoundSQL("insert into Player values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6)", $alltuples);
		OCICommit($db_conn);

	} else
		if (array_key_exists('deletesubmit', $_POST)) {
			// Remove the player with the associated player ID.
			$tuple = array (
				":bind1" => $_POST['insPlayerID'],
			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("delete from Player where playerID=:bind1", $alltuples);
			OCICommit($db_conn);
		}

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: manager-players.php");
	} else {
		$result = executePlainSQL("select * from Player");
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
