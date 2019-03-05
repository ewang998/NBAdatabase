# NBAdatabase
A simple NBA database 
Created in CPSC 304 at UBC

Brief Overview
When users access the database, they will first be welcomed to the home page (home.php file), then from there, depending on whether 
they are a fan or an NBA league employee, they will be directed to their respective portals (fan.php file or nba employee php file). 
The difference between those two types of users is that NBA league employees can modify the database, such as adding, removing, updating
players, coaches, player stats, team stats and etc. In addition, they will also have access to view Player Contracts which regular fans 
do not have in the fan portal.
For the fan portal, fans can access and search up anything other than Player Contracts in their search portal (search.php file). Some
sample functionalities for Fans can be found in the Screenshot/fans folder.
For the employee portal, GM/league or team employee can add/drop player and coaches, update the records of player/teams. Search player 
based on salary, find average salary of players by position and ages or view team player in different positions. (sample screenshot are provided).
Changelog
All new updates to the database schema can be found in the nba.sql file (nba_populate.sql are test data inserted to the database and 
all_queries.sql include all the SQL queries implemented in this project) and the Final Project Formal Specifications pdf file.
