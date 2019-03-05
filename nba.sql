-- Initialize all the tables for the db
CREATE TABLE Coach(
  coachID CHAR(30) NOT NULL,
  name CHAR(50) NOT NULL,
  experience INTEGER,
  PRIMARY KEY (coachID)
);

grant select on Coach to public;

CREATE TABLE Team(
  teamID CHAR(50) NOT NULL,
  coachID CHAR(30),
  teamName CHAR(50) NOT NULL,
  location CHAR(50) NOT NULL,
  PRIMARY KEY (teamID),
  FOREIGN KEY (coachID)
    REFERENCES Coach(coachID)
    ON DELETE SET NULL
);

grant select on Team to public;

CREATE TABLE Team_Stats(
  tsID CHAR(50) NOT NULL,
  teamID CHAR(50) NOT NULL,
  win INTEGER NOT NULL,
  loss INTEGER NOT NULL,
  PRIMARY KEY (tsID, teamID),
  FOREIGN KEY (teamID) 
    REFERENCES Team(teamID)
    ON DELETE CASCADE,
  CHECK (win >= 0 And win <= 82),
  CHECK (loss >= 0 And loss <= 82),
  CHECK ((win + loss) <= 82)
);

grant select on Team_Stats to public;

CREATE TABLE Game(
  gameID INTEGER NOT NULL,
  homeTeamID CHAR(50) NOT NULL,
  awayTeamID CHAR(50) NOT NULL,
  homeScore INTEGER NOT NULL,
  awayScore INTEGER NOT NULL,
  PRIMARY KEY (gameID),
  FOREIGN KEY (homeTeamID) 
    REFERENCES Team(teamID)
    ON DELETE CASCADE,
  FOREIGN KEY (awayTeamID) 
    REFERENCES Team(teamID)
    ON DELETE CASCADE
);

grant select on Game to public;

CREATE TABLE Player(
  playerID CHAR(50) NOT NULL,
  playerTeam CHAR(50) NOT NULL,
  firstName CHAR(50) NOT NULL,
  lastName CHAR(50) NOT NULL,
  position CHAR(50),
  age INTEGER NOT NULL,
  PRIMARY KEY (playerID),
  FOREIGN KEY (playerTeam) 
    REFERENCES Team(teamID)
    ON DELETE CASCADE
);

grant select on Player to public;

CREATE TABLE Player_Stats(
  sID CHAR(50) NOT NULL,
  player CHAR(50) NOT NULL,
  ppg DECIMAL(9,1),
  rpg DECIMAL(9,1),
  apg DECIMAL(9,1),
  PRIMARY KEY (sID, player),
  FOREIGN KEY (player)
    REFERENCES Player(playerID)
    ON DELETE CASCADE
);

grant select on Player_Stats to public;

CREATE TABLE Player_Contract(
  contractID CHAR(30) NOT NULL,
  tID CHAR(50) NOT NULL,
  pID CHAR(50) NOT NULL,
  yearlySal INTEGER,
  yearsLeft INTEGER,
  PRIMARY KEY (contractID, tID, pID),
  FOREIGN KEY (pID)
    REFERENCES Player(playerID)
    ON DELETE CASCADE,
  FOREIGN KEY (tID)
    REFERENCES Team(teamID)
    ON DELETE CASCADE
);

grant select on Player_Contract to public;

commit;