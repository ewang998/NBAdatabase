-- In fan.php file (4 total)
-- Include queries related to Deliverable 5 & 6 from Proj Formal Specifications
-- 1. display games
select *
from Game
order by gameID ASC;
-- 2. display league standings (Team join Team_Stats)
select team.location, team.teamName, team_stats.win, team_stats.loss
from team_stats
  join team on team.teamID = team_stats.teamID
order by win DESC;
-- 3. show all coaches
select name, experience
from Coach
order by experience ASC
-- 4. show all players (Player join Player_Stats)
select age, firstName, lastName, position, playerTeam, ppg, rpg, apg
from
  player p, player_stats ps
where p.playerID = ps.player
order by lastName ASC


-- In search.php file (at least 2)
-- Include queries related to Deliverable 5 & 6 from Proj Formal Specifications
-- 1. Join Player, Player_Stats, Team and Team_Stats together (dynamic query)
$search =
SELECT p.firstName, p.lastName, p.position, p.age,
  ps.ppg, ps.rpg, ps.apg,
  t.location, t.teamName, ts.win, ts.loss
FROM Player p, Team t, Player_Stats ps, Team_Stats ts
WHERE p.playerTeam = t.teamID AND p.playerID = ps.player AND t.teamID = ts.teamID
-- 2. Join Player, Player_Stats, Team, Team_Stats and Coach together (dynamic query)
SELECT c.name, c.experience, p.firstName, p.lastName, p.position, p.age,
  ps.ppg, ps.rpg, ps.apg,
  t.location, t.teamName, ts.win, ts.loss
FROM Player p
  Join Team t on p.playerTeam = t.teamID
  Join Coach c on t.coachID = c.coachID
  Join Player_Stats ps on p.playerID = ps.player
  Join Team_Stats ts on ts.teamID = t.teamID
Where c.name LIKE '" . $coach . "%'
ORDER BY ".$sort." DESC
-- 3. More dynamic queries
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

-- In manager-search file
--search
$search = SELECT p.firstName, p.lastName, p.position, p.age,
ps.ppg, ps.rpg, ps.apg,
t.location, t.teamName, ts.win, ts.loss, pc.yearlySal, pc.yearsLeft
FROM Player p, Team t, Player_Stats ps, Player_Contract pc, Team_Stats ts
WHERE p.playerTeam = t.teamID AND p.playerID = ps.player AND t.teamID = ts.teamID AND p.playerID = pc.pID;

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

if ($minSalary != "") {
  $search .= " AND pc.yearlySal > " . $minSalary;
}

if ($maxSalary != "") {
  $search .= " AND pc.yearlySal < " . $maxSalary;
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

-- In manager-groupby file
-- search based on position for average salary group by ages

$search = SELECT age, pos, AVG(yearlySal)
FROM Player Join Player_Contract on playerID = pID;
if ($position != "") {
  $search .= " WHERE pos LIKE '" . $position . "%'";
}
$search .= " GROUP BY age, pos ";
$search .= " ORDER BY age DESC";

-- in manager-contracts
--add contract
insert into Player_Contract values (:bind1, :bind2, :bind3, :bind4, :bind5);

-- in manager-team_stats
--update team stat
update Team_Stats set win=:bind3 where tsID=:bind1 AND teamID=:bind2;
update Team_Stats set loss=:bind4 where tsID=:bind1 AND teamID=:bind2;

-- in manager-player_stats
--update player stat
update Player_Stats set ppg=:bind3 where sID=:bind1 AND player=:bind2;
update Player_Stats set rpg=:bind4 where sID=:bind1 AND player=:bind2;
update Player_Stats set apg=:bind5 where sID=:bind1 AND player=:bind2;

-- in manager-team
select * from Team;

-- in manager-Coaches
--insert
insert into Coach values (:bind1, :bind2, :bind3);
--delete
delete from Coach where coachID=:bind1;
--view
select * from Coach;

-- in manage players
--insert
insert into Player values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6);
  --delete
delete from Player where playerID=:bind1;
  --view
select * from Player;

-- view players
create view Positions as select p.pos pos, p.team team from Player p;
select * from Positions;
