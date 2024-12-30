create database Vaccination;
use Vaccination;
create table regi(username varchar(20),email varchar(30) primary key,passw varchar(30),confirm varchar(30));
desc regi;
select * from regi;
alter table regi add mobno varchar(10);
truncate table regi;
create table admintable(username varchar(20),email varchar(30) primary key,passw varchar(30),confirm varchar(30),mobno varchar(10));
desc admintable;
select * from admintable;
create table vaccineschedule(VaccineName varchar(50), center varchar(100), fromdate date, todate date, age int);
desc vaccineschedule;
select * from vaccineschedule;
alter table vaccineschedule modify age varchar(30);
truncate vaccineschedule;
CREATE TABLE vaccinesweek (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vaccine_name VARCHAR(1000),
    min_age_week VARCHAR(50), 
    max_age_week VARCHAR(50)  
);
insert into vaccinesweek (vaccine_name,min_age_week,max_age_week) values("BCG","0","5 weeks"),("Hep B1","0","5 weeks"),("OPV","0","5 Weeks"),("DTwP/DTaP1","6 weeks","9 weeks"),("Hib-1", "6 weeks","9 weeks"),("IPV-1","6 weeks","9 weeks" ),("Hep B2","6 weeks","9 weeks"),("PCV 1","6 weeks","9 weeks") ,("Rota-1","6 weeks","9 weeks");
insert into vaccinesweek (vaccine_name,min_age_week,max_age_week) values("DTwP /DTaP2","10 weeks","13 weeks"),("Hib-2","10 weeks","13 weeks"),("IPV-2","10 weeks","13 weeks"),("Hep B3","10 weeks","13 weeks"),("PCV 2","10 weeks","13 weeks"), ("Rota-2","10 weeks","13 weeks" );
insert into vaccinesweek(vaccine_name,min_age_week,max_age_week) values ("DTwP /DTaP3","14 weeks","20 weeks"),("Hib-3","14 weeks","20 weeks"),("IPV-3","14 weeks","20 weeks"),("Hep B4","14 weeks","20 weeks"),("PCV 3","14 weeks","20 weeks"),("Rota-3","14 weeks","20 weeks");

select * from vaccinesweek;
-- create table childdetail(childname varchar(30),birtdate date, weight int,height int);
-- Create the users table first (parent table)

CREATE TABLE childdetail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    childname VARCHAR(100) NOT NULL,
    birthdate DATE NOT NULL,
    weight DECIMAL(5, 2) NOT NULL,
    height DECIMAL(4, 2) NOT NULL,
    FOREIGN KEY (email) REFERENCES regi(email) ON DELETE CASCADE ON UPDATE CASCADE
);


drop table childdetail;
select * from childdetail;
desc childdetail;



