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

<form method="POST" action="manager-view.php">
<p><input type="submit" value="Return to employee database" name="managerview"></p>
</form>

<form method="POST" action="manager-groupby.php">

<label><p><b>Search Average Salary by Position:</b></p></label>
<p><tr><th>Position</th></tr></p>

<input type="text" name="position"><br>
<br>

<input type="submit" name="searchDB" value="Search NBA DB">
</form>



<?php
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

function printSearch($result) { //prints results from a select statement
	echo "Showing all results from the database:<br><br>";
	echo "<table border=2 cellpadding=5px cellspacing=5px style=font-family:Arial; font-size:7px>";
	echo "<tr><th>Age</th><th>Position</th><th>Average Salary</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td align=center>" . $row[0] . "</td><td align=center>" . $row[1] . "</td><td align=center>" . $row[2] . "</td></tr>";
	}
	echo "</table>";

}


// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('searchDB', $_POST)) {

		$position = $_POST['position'];

		$search = "SELECT age, pos, AVG(yearlySal)
		FROM Player Join Player_Contract on playerID = pID";

    if ($position != "") {
      $search .= " WHERE pos LIKE '" . $position . "%'";
    }

    $search .= " GROUP BY age, pos ";


    $search .= " ORDER BY age DESC";




		$result = executePlainSQL($search);
		printSearch($result);
		}
	}

?>
