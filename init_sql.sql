DROP TABLE ADDRESSLOOKUP CASCADE CONSTRAINTS;

DROP TABLE LABEL CASCADE CONSTRAINTS;

DROP TABLE AGENT CASCADE CONSTRAINTS;

DROP TABLE BAND CASCADE CONSTRAINTS;

DROP TABLE CONTRACT CASCADE CONSTRAINTS;

DROP TABLE AGENTCONTRACTMANAGEMENT CASCADE CONSTRAINTS;

DROP TABLE REVENUELOOKUP CASCADE CONSTRAINTS;

DROP TABLE ALBUM CASCADE CONSTRAINTS;

DROP TABLE CONTRIBUTESTO CASCADE CONSTRAINTS;

DROP TABLE ARTIST CASCADE CONSTRAINTS;

DROP TABLE MEMBEROF CASCADE CONSTRAINTS;

DROP TABLE SONG CASCADE CONSTRAINTS;

DROP TABLE SHOWREVENUELOOKUP CASCADE CONSTRAINTS;

DROP TABLE MUSICSHOW CASCADE CONSTRAINTS;

DROP TABLE TOUR CASCADE CONSTRAINTS;

DROP TABLE PERFORMS CASCADE CONSTRAINTS;

DROP TABLE CONSISTSOF CASCADE CONSTRAINTS;

DROP TABLE GOESON CASCADE CONSTRAINTS;

DROP TABLE PERFORMEDAT CASCADE CONSTRAINTS;

CREATE TABLE ADDRESSLOOKUP (
    COUNTRY CHAR(30),
    POSTALCODE CHAR(30),
    CITY CHAR(30),
    PROVINCEORSTATE CHAR(30),
    CONSTRAINT ADDRESS_PK PRIMARY KEY (COUNTRY, POSTALCODE)
);

INSERT INTO ADDRESSLOOKUP(
    COUNTRY,
    POSTALCODE,
    CITY,
    PROVINCEORSTATE
) VALUES (
    'Canada',
    'V1V1V1',
    'Vancouver',
    'British Columbia'
);

INSERT INTO ADDRESSLOOKUP(
    COUNTRY,
    POSTALCODE,
    CITY,
    PROVINCEORSTATE
) VALUES (
    'Canada',
    'V2V2V2',
    'Vancouver',
    'British Columbia'
);

INSERT INTO ADDRESSLOOKUP(
    COUNTRY,
    POSTALCODE,
    CITY,
    PROVINCEORSTATE
) VALUES (
    'Canada',
    'C2C2C2',
    'Calgary',
    'Alberta'
);

INSERT INTO ADDRESSLOOKUP(
    COUNTRY,
    POSTALCODE,
    CITY,
    PROVINCEORSTATE
) VALUES (
    'USA',
    '90210',
    'LA',
    'California'
);

INSERT INTO ADDRESSLOOKUP(
    COUNTRY,
    POSTALCODE,
    CITY,
    PROVINCEORSTATE
) VALUES (
    'USA',
    '91200',
    'San diego',
    'California'
);

CREATE TABLE LABEL (
    LABELNAME CHAR(30),
    STREETNUMBER CHAR(30),
    COUNTRY CHAR(30) NOT NULL,
    POSTALCODE CHAR(30) NOT NULL,
    CONSTRAINT LABEL_PK PRIMARY KEY (LABELNAME),
    CONSTRAINT LABEL_FK FOREIGN KEY (COUNTRY, POSTALCODE) REFERENCES ADDRESSLOOKUP(COUNTRY, POSTALCODE) ON DELETE SET NULL
);

INSERT INTO LABEL(
    LABELNAME,
    STREETNUMBER,
    COUNTRY,
    POSTALCODE
) VALUES (
    'XYZ Records',
    '1 Main Street',
    'USA',
    '91200'
);

INSERT INTO LABEL(
    LABELNAME,
    STREETNUMBER,
    COUNTRY,
    POSTALCODE
) VALUES (
    'ABC Records',
    '2 Main Street',
    'USA',
    '91200'
);

INSERT INTO LABEL(
    LABELNAME,
    STREETNUMBER,
    COUNTRY,
    POSTALCODE
) VALUES (
    'The Best Records',
    '10 Wall Street',
    'Canada',
    'V1V1V1'
);

INSERT INTO LABEL(
    LABELNAME,
    STREETNUMBER,
    COUNTRY,
    POSTALCODE
) VALUES (
    'The Worst Records',
    '11 Wall Street',
    'Canada',
    'V2V2V2'
);

INSERT INTO LABEL(
    LABELNAME,
    STREETNUMBER,
    COUNTRY,
    POSTALCODE
) VALUES (
    'The Records',
    'Granville Street',
    'Canada',
    'C2C2C2'
);

CREATE TABLE AGENT (
    AGENTID CHAR(30),
    AGENTNAME CHAR(30) NOT NULL,
    LABELNAME CHAR(30) NOT NULL,
    CONSTRAINT AGENT_PK PRIMARY KEY (AGENTID),
    CONSTRAINT AGENT_FK FOREIGN KEY (LABELNAME) REFERENCES LABEL(LABELNAME) ON DELETE CASCADE
);

INSERT INTO AGENT(
    AGENTID,
    AGENTNAME,
    LABELNAME
) VALUES (
    '007',
    'james bond',
    'XYZ Records'
);

INSERT INTO AGENT(
    AGENTID,
    AGENTNAME,
    LABELNAME
) VALUES (
    '006',
    'agent 6',
    'ABC Records'
);

INSERT INTO AGENT(
    AGENTID,
    AGENTNAME,
    LABELNAME
) VALUES (
    '005',
    'agent 5',
    'The Best Records'
);

INSERT INTO AGENT(
    AGENTID,
    AGENTNAME,
    LABELNAME
) VALUES (
    '004',
    'agent 4',
    'The Worst Records'
);

INSERT INTO AGENT(
    AGENTID,
    AGENTNAME,
    LABELNAME
) VALUES (
    '003',
    'agent 3',
    'The Records'
);

CREATE TABLE BAND(
    BANDNAME CHAR(30),
    CONSTRAINT GROUP_PK PRIMARY KEY (BANDNAME)
);

INSERT INTO BAND(
    BANDNAME
) VALUES (
    'Green Day'
);

INSERT INTO BAND(
    BANDNAME
) VALUES (
    'Eagles'
);

INSERT INTO BAND(
    BANDNAME
) VALUES (
    'BTS'
);

INSERT INTO BAND(
    BANDNAME
) VALUES (
    'The Beatles'
);

INSERT INTO BAND(
    BANDNAME
) VALUES (
    'Travis Scott'
);

CREATE TABLE CONTRACT (
    CONTRACTID CHAR(30) NOT NULL,
    LABELNAME CHAR(30) NOT NULL,
    BANDNAME CHAR(30) NOT NULL,
    SIGNINGBONUS FLOAT(10) NOT NULL,
    ROYALTY FLOAT(10) NOT NULL,
    TIMEBASEDCONTRACTDURATION CHAR(30),
    CONTENTBASEDNUMBEROFALBUMS CHAR(30),
    CONSTRAINT CONTRACT_PK PRIMARY KEY (CONTRACTID),
    CONSTRAINT CONTRACT_FK_LABEL FOREIGN KEY (LABELNAME) REFERENCES LABEL(LABELNAME) ON DELETE CASCADE,
    CONSTRAINT CONTRACT_FK_GROUP FOREIGN KEY (BANDNAME) REFERENCES BAND(BANDNAME) ON DELETE CASCADE
);

INSERT INTO CONTRACT(
    CONTRACTID,
    LABELNAME,
    BANDNAME,
    SIGNINGBONUS,
    ROYALTY,
    TIMEBASEDCONTRACTDURATION,
    CONTENTBASEDNUMBEROFALBUMS
) VALUES (
    'c1',
    'XYZ Records',
    'Green Day',
    1000.0,
    3.5,
    '2 years',
    NULL
);

INSERT INTO CONTRACT(
    CONTRACTID,
    LABELNAME,
    BANDNAME,
    SIGNINGBONUS,
    ROYALTY,
    TIMEBASEDCONTRACTDURATION,
    CONTENTBASEDNUMBEROFALBUMS
) VALUES (
    'c999',
    'XYZ Records',
    'Green Day',
    1000.0,
    3.5,
    '2 years',
    NULL
);

INSERT INTO CONTRACT(
    CONTRACTID,
    LABELNAME,
    BANDNAME,
    SIGNINGBONUS,
    ROYALTY,
    TIMEBASEDCONTRACTDURATION,
    CONTENTBASEDNUMBEROFALBUMS
) VALUES (
    'c2',
    'ABC Records',
    'Eagles',
    2000.0,
    4.5,
    NULL,
    5
);

INSERT INTO CONTRACT(
    CONTRACTID,
    LABELNAME,
    BANDNAME,
    SIGNINGBONUS,
    ROYALTY,
    TIMEBASEDCONTRACTDURATION,
    CONTENTBASEDNUMBEROFALBUMS
) VALUES (
    'c3',
    'The Best Records',
    'The Beatles',
    3000.0,
    5.5,
    '4 years',
    NULL
);

INSERT INTO CONTRACT(
    CONTRACTID,
    LABELNAME,
    BANDNAME,
    SIGNINGBONUS,
    ROYALTY,
    TIMEBASEDCONTRACTDURATION,
    CONTENTBASEDNUMBEROFALBUMS
) VALUES (
    'c4',
    'The Worst Records',
    'BTS',
    4000.0,
    6.5,
    NULL,
    1
);

INSERT INTO CONTRACT(
    CONTRACTID,
    LABELNAME,
    BANDNAME,
    SIGNINGBONUS,
    ROYALTY,
    TIMEBASEDCONTRACTDURATION,
    CONTENTBASEDNUMBEROFALBUMS
) VALUES (
    'c5',
    'The Records',
    'Travis Scott',
    5000.0,
    7.5,
    '1 years',
    NULL
);

CREATE TABLE AGENTCONTRACTMANAGEMENT (
    AGENTID CHAR(30) NOT NULL,
    CONTRACTID CHAR(30) NOT NULL,
    CONSTRAINT AGENTCONTRACT_PK PRIMARY KEY (AGENTID, CONTRACTID),
    CONSTRAINT AGENTCONTRACT_FK_AGENT FOREIGN KEY (AGENTID) REFERENCES AGENT(AGENTID) ON DELETE CASCADE,
    CONSTRAINT AGENTCONTRACT_FK_CONTRACT FOREIGN KEY (CONTRACTID) REFERENCES CONTRACT(CONTRACTID) ON DELETE CASCADE
);

INSERT INTO AGENTCONTRACTMANAGEMENT(
    AGENTID,
    CONTRACTID
) VALUES (
    '007',
    'c1'
);

INSERT INTO AGENTCONTRACTMANAGEMENT(
    AGENTID,
    CONTRACTID
) VALUES (
    '007',
    'c999'
);

INSERT INTO AGENTCONTRACTMANAGEMENT(
    AGENTID,
    CONTRACTID
) VALUES (
    '006',
    'c2'
);

INSERT INTO AGENTCONTRACTMANAGEMENT(
    AGENTID,
    CONTRACTID
) VALUES (
    '005',
    'c3'
);

INSERT INTO AGENTCONTRACTMANAGEMENT(
    AGENTID,
    CONTRACTID
) VALUES (
    '004',
    'c4'
);

INSERT INTO AGENTCONTRACTMANAGEMENT(
    AGENTID,
    CONTRACTID
) VALUES (
    '003',
    'c5'
);

CREATE TABLE REVENUELOOKUP (
    GENRE CHAR(30) NOT NULL,
    EXPECTEDSALES INT NOT NULL,
    PRICE FLOAT(10) NOT NULL,
    CONSTRAINT REVENUE_PK PRIMARY KEY (GENRE, EXPECTEDSALES)
);

INSERT INTO REVENUELOOKUP(
    GENRE,
    EXPECTEDSALES,
    PRICE
) VALUES (
    'pop',
    10,
    100
);

INSERT INTO REVENUELOOKUP(
    GENRE,
    EXPECTEDSALES,
    PRICE
) VALUES (
    'rock',
    100,
    1000
);

INSERT INTO REVENUELOOKUP(
    GENRE,
    EXPECTEDSALES,
    PRICE
) VALUES (
    'soul',
    20,
    200
);

INSERT INTO REVENUELOOKUP(
    GENRE,
    EXPECTEDSALES,
    PRICE
) VALUES (
    'rap',
    30,
    300
);

INSERT INTO REVENUELOOKUP(
    GENRE,
    EXPECTEDSALES,
    PRICE
) VALUES (
    'rNb',
    40,
    400
);

CREATE TABLE ALBUM (
    ALBUMID CHAR(30),
    LABELNAME CHAR(30),
    TITLE CHAR(30) NOT NULL,
    RELEASEDATE DATE,
    GENRE CHAR(30),
    NUMBERSOLD INT,
    EXPECTEDSALES INT,
    CONSTRAINT ALBUM_PK PRIMARY KEY (ALBUMID),
    CONSTRAINT ALBUM_FK_LABEL FOREIGN KEY (LABELNAME) REFERENCES LABEL(LABELNAME) ON DELETE SET NULL,
    CONSTRAINT ALBUM_FK_REVENUE FOREIGN KEY (GENRE, EXPECTEDSALES) REFERENCES REVENUELOOKUP(GENRE, EXPECTEDSALES) ON DELETE SET NULL
);

INSERT INTO ALBUM(
    ALBUMID,
    LABELNAME,
    TITLE,
    RELEASEDATE,
    GENRE,
    NUMBERSOLD,
    EXPECTEDSALES
) VALUES (
    'a1',
    'XYZ Records',
    'American Idiot',
    '21-SEP-2004',
    'rock',
    16000000,
    100
);

INSERT INTO ALBUM(
    ALBUMID,
    LABELNAME,
    TITLE,
    RELEASEDATE,
    GENRE,
    NUMBERSOLD,
    EXPECTEDSALES
) VALUES (
    'a2',
    'ABC Records',
    'Eagles',
    '01-JUN-1972',
    'rock',
    1000000,
    100
);

INSERT INTO ALBUM(
    ALBUMID,
    LABELNAME,
    TITLE,
    RELEASEDATE,
    GENRE,
    NUMBERSOLD,
    EXPECTEDSALES
) VALUES (
    'a3',
    'The Best Records',
    'Abbey Road',
    '26-SEP-1969',
    'soul',
    31000000,
    20
);

INSERT INTO ALBUM(
    ALBUMID,
    LABELNAME,
    TITLE,
    RELEASEDATE,
    GENRE,
    NUMBERSOLD,
    EXPECTEDSALES
) VALUES (
    'a4',
    'The Worst Records',
    'Proof',
    '10-JUN-2022',
    'pop',
    3000000,
    10
);

INSERT INTO ALBUM(
    ALBUMID,
    LABELNAME,
    TITLE,
    RELEASEDATE,
    GENRE,
    NUMBERSOLD,
    EXPECTEDSALES
) VALUES (
    'a5',
    'The Records',
    'Rodeo',
    '04-SEP-2015',
    'rap',
    5000000,
    30
);

CREATE TABLE CONTRIBUTESTO(
    ALBUMID CHAR(30),
    BANDNAME CHAR(30),
    CONSTRAINT CONTRIBUTESTO_PK PRIMARY KEY (ALBUMID, BANDNAME),
    CONSTRAINT CONTRIBUTESTO_FK_ALBUM FOREIGN KEY (ALBUMID) REFERENCES ALBUM(ALBUMID) ON DELETE CASCADE,
    CONSTRAINT CONTRIBUTESTO_FK_GROUP FOREIGN KEY (BANDNAME) REFERENCES BAND(BANDNAME) ON DELETE CASCADE
);

INSERT INTO CONTRIBUTESTO(
    ALBUMID,
    BANDNAME
) VALUES (
    'a1',
    'Green Day'
);

INSERT INTO CONTRIBUTESTO(
    ALBUMID,
    BANDNAME
) VALUES (
    'a2',
    'Eagles'
);

INSERT INTO CONTRIBUTESTO(
    ALBUMID,
    BANDNAME
) VALUES (
    'a3',
    'The Beatles'
);

INSERT INTO CONTRIBUTESTO(
    ALBUMID,
    BANDNAME
) VALUES (
    'a4',
    'BTS'
);

INSERT INTO CONTRIBUTESTO(
    ALBUMID,
    BANDNAME
) VALUES (
    'a5',
    'Travis Scott'
);

CREATE TABLE ARTIST(
    ARTISTNAME CHAR(30),
    DATEOFBIRTH DATE,
    CONSTRAINT ARTIST_PK PRIMARY KEY (ARTISTNAME)
);

INSERT INTO ARTIST(
    ARTISTNAME,
    DATEOFBIRTH
) VALUES (
    'Billy Joe Armstrong',
    '17-FEB-1972'
);

INSERT INTO ARTIST(
    ARTISTNAME,
    DATEOFBIRTH
) VALUES (
    'Don Henley',
    '22-JUL-1947'
);

INSERT INTO ARTIST(
    ARTISTNAME,
    DATEOFBIRTH
) VALUES (
    'John Lennon',
    '09-OCT-1940'
);

INSERT INTO ARTIST(
    ARTISTNAME,
    DATEOFBIRTH
) VALUES (
    'Kim Nam-joon',
    '12-SEP-1994'
);

INSERT INTO ARTIST(
    ARTISTNAME,
    DATEOFBIRTH
) VALUES (
    'Jeon Jung-kook',
    '01-SEP-1997'
);

INSERT INTO ARTIST(
    ARTISTNAME,
    DATEOFBIRTH
) VALUES (
    'Travis Scott',
    '30-APR-1991'
);

CREATE TABLE MEMBEROF(
    ARTISTNAME CHAR(30),
    BANDNAME CHAR(30),
    CONSTRAINT MEMBEROF_PK PRIMARY KEY (ARTISTNAME, BANDNAME),
    CONSTRAINT MEMBEROF_FK_ARTIST FOREIGN KEY (ARTISTNAME) REFERENCES ARTIST(ARTISTNAME) ON DELETE CASCADE,
    CONSTRAINT MEMBEROF_FK_BAND FOREIGN KEY (BANDNAME) REFERENCES BAND(BANDNAME) ON DELETE CASCADE
);

INSERT INTO MEMBEROF(
    ARTISTNAME,
    BANDNAME
) VALUES (
    'Billy Joe Armstrong',
    'Green Day'
);

INSERT INTO MEMBEROF(
    ARTISTNAME,
    BANDNAME
) VALUES (
    'Don Henley',
    'Eagles'
);

INSERT INTO MEMBEROF(
    ARTISTNAME,
    BANDNAME
) VALUES (
    'John Lennon',
    'The Beatles'
);

INSERT INTO MEMBEROF(
    ARTISTNAME,
    BANDNAME
) VALUES (
    'Kim Nam-joon',
    'BTS'
);

INSERT INTO MEMBEROF(
    ARTISTNAME,
    BANDNAME
) VALUES (
    'Jeon Jung-kook',
    'BTS'
);

INSERT INTO MEMBEROF(
    ARTISTNAME,
    BANDNAME
) VALUES (
    'Travis Scott',
    'Travis Scott'
);

CREATE TABLE SONG(
    TITLE CHAR(30),
    ALBUMID CHAR(30) NOT NULL,
    TRACKLISTPOSITION FLOAT,
    DURATION FLOAT,
    CONSTRAINT SONG_PK PRIMARY KEY (TITLE, ALBUMID),
    CONSTRAINT SONG_FK FOREIGN KEY (ALBUMID) REFERENCES ALBUM(ALBUMID) ON DELETE CASCADE
);

INSERT INTO SONG(
    TITLE,
    ALBUMID,
    TRACKLISTPOSITION,
    DURATION
) VALUES (
    'American Idiot',
    'a1',
    1,
    174
);

INSERT INTO SONG(
    TITLE,
    ALBUMID,
    TRACKLISTPOSITION,
    DURATION
) VALUES (
    'Take It Easy',
    'a2',
    1,
    212
);

INSERT INTO SONG(
    TITLE,
    ALBUMID,
    TRACKLISTPOSITION,
    DURATION
) VALUES (
    'Come Together',
    'a3',
    1,
    259
);

INSERT INTO SONG(
    TITLE,
    ALBUMID,
    TRACKLISTPOSITION,
    DURATION
) VALUES (
    'Run BTS',
    'a4',
    2,
    204
);

INSERT INTO SONG(
    TITLE,
    ALBUMID,
    TRACKLISTPOSITION,
    DURATION
) VALUES (
    'Run',
    'a4',
    3,
    204
);

INSERT INTO SONG(
    TITLE,
    ALBUMID,
    TRACKLISTPOSITION,
    DURATION
) VALUES (
    '90210',
    'a5',
    5,
    339
);

CREATE TABLE SHOWREVENUELOOKUP(
    TICKETSSOLD INTEGER,
    COSTPERTICKET FLOAT,
    REVENUE FLOAT,
    CONSTRAINT SHOWREVENUELOOKUP_PK PRIMARY KEY (TICKETSSOLD, COSTPERTICKET)
);

INSERT INTO SHOWREVENUELOOKUP(
    TICKETSSOLD,
    COSTPERTICKET,
    REVENUE
) VALUES (
    20,
    20,
    400
);

INSERT INTO SHOWREVENUELOOKUP(
    TICKETSSOLD,
    COSTPERTICKET,
    REVENUE
) VALUES (
    200,
    20,
    4000
);

INSERT INTO SHOWREVENUELOOKUP(
    TICKETSSOLD,
    COSTPERTICKET,
    REVENUE
) VALUES (
    10,
    300,
    3000
);

INSERT INTO SHOWREVENUELOOKUP(
    TICKETSSOLD,
    COSTPERTICKET,
    REVENUE
) VALUES (
    10000,
    10,
    100000
);

INSERT INTO SHOWREVENUELOOKUP(
    TICKETSSOLD,
    COSTPERTICKET,
    REVENUE
) VALUES (
    1,
    2000,
    2000
);

CREATE TABLE MUSICSHOW(
    VENUE CHAR(30),
    SHOWDATE DATE,
    TICKETSSOLD INTEGER,
    COSTPERTICKET FLOAT,
    CONSTRAINT SHOW_PK PRIMARY KEY (VENUE, SHOWDATE),
    CONSTRAINT SHOW_FK1 FOREIGN KEY (TICKETSSOLD, COSTPERTICKET) REFERENCES SHOWREVENUELOOKUP(TICKETSSOLD, COSTPERTICKET) ON DELETE SET NULL
);

INSERT INTO MUSICSHOW(
    VENUE,
    SHOWDATE,
    TICKETSSOLD,
    COSTPERTICKET
) VALUES (
    'Carnegie Hall',
    '01-JAN-2005',
    20,
    20
);

INSERT INTO MUSICSHOW(
    VENUE,
    SHOWDATE,
    TICKETSSOLD,
    COSTPERTICKET
) VALUES (
    'Woodstock',
    '20-JAN-1982',
    200,
    20
);

INSERT INTO MUSICSHOW(
    VENUE,
    SHOWDATE,
    TICKETSSOLD,
    COSTPERTICKET
) VALUES (
    'Madison Square Garden',
    '19-JUL-1986',
    10,
    300
);

INSERT INTO MUSICSHOW(
    VENUE,
    SHOWDATE,
    TICKETSSOLD,
    COSTPERTICKET
) VALUES (
    'Rogers Arena',
    '10-JUN-2022',
    10000,
    10
);

INSERT INTO MUSICSHOW(
    VENUE,
    SHOWDATE,
    TICKETSSOLD,
    COSTPERTICKET
) VALUES (
    'NRG Park',
    '05-NOV-2021',
    10000,
    10
);

CREATE TABLE TOUR(
    TOURNAME CHAR(30),
    STARTDATE DATE,
    ENDDATE DATE NOT NULL,
    CONSTRAINT TOUR_PK PRIMARY KEY (TOURNAME, STARTDATE),
    CONSTRAINT TOUR_CK UNIQUE (TOURNAME, ENDDATE)
);

INSERT INTO TOUR(
    TOURNAME,
    STARTDATE,
    ENDDATE
) VALUES (
    'American Idiot Tour',
    '01-JAN-2005',
    '02-JAN-2005'
);

INSERT INTO TOUR(
    TOURNAME,
    STARTDATE,
    ENDDATE
) VALUES (
    'Eagles Tour',
    '20-JAN-1982',
    '22-JAN-1982'
);

INSERT INTO TOUR(
    TOURNAME,
    STARTDATE,
    ENDDATE
) VALUES (
    'Abbey Road Tour',
    '19-JUL-1986',
    '21-JUL-1986'
);

INSERT INTO TOUR(
    TOURNAME,
    STARTDATE,
    ENDDATE
) VALUES (
    'Proof Tour',
    '10-JUN-2022',
    '12-JUN-2022'
);

INSERT INTO TOUR(
    TOURNAME,
    STARTDATE,
    ENDDATE
) VALUES (
    'Rodeo Tour',
    '05-NOV-2021',
    '05-NOV-2021'
);

CREATE TABLE PERFORMS(
    BANDNAME CHAR(30),
    VENUE CHAR(30),
    SHOWDATE DATE,
    CONSTRAINT PERFORMS_PK PRIMARY KEY (BANDNAME, VENUE, SHOWDATE),
    CONSTRAINT PERFORMS_FK1 FOREIGN KEY (BANDNAME) REFERENCES BAND(BANDNAME) ON DELETE CASCADE,
    CONSTRAINT PERFORMS_FK2 FOREIGN KEY(VENUE, SHOWDATE) REFERENCES MUSICSHOW(VENUE, SHOWDATE) ON DELETE CASCADE
);

INSERT INTO PERFORMS(
    BANDNAME,
    VENUE,
    SHOWDATE
) VALUES (
    'Green Day',
    'Carnegie Hall',
    '01-JAN-2005'
);

INSERT INTO PERFORMS(
    BANDNAME,
    VENUE,
    SHOWDATE
) VALUES (
    'Eagles',
    'Woodstock',
    '20-JAN-1982'
);

INSERT INTO PERFORMS(
    BANDNAME,
    VENUE,
    SHOWDATE
) VALUES (
    'The Beatles',
    'Madison Square Garden',
    '19-JUL-1986'
);

INSERT INTO PERFORMS(
    BANDNAME,
    VENUE,
    SHOWDATE
) VALUES (
    'BTS',
    'Rogers Arena',
    '10-JUN-2022'
);

INSERT INTO PERFORMS(
    BANDNAME,
    VENUE,
    SHOWDATE
) VALUES (
    'Travis Scott',
    'NRG Park',
    '05-NOV-2021'
);

CREATE TABLE CONSISTSOF(
    TOURNAME CHAR(30),
    STARTDATE DATE,
    VENUE CHAR(30),
    SHOWDATE DATE,
    CONSTRAINT CONSISTSOF_PK PRIMARY KEY (TOURNAME, STARTDATE, VENUE, SHOWDATE),
    CONSTRAINT CONSISTSOF_FK1 FOREIGN KEY (TOURNAME, STARTDATE) REFERENCES TOUR(TOURNAME, STARTDATE) ON DELETE CASCADE,
    CONSTRAINT CONSISTSOF_FK2 FOREIGN KEY (VENUE, SHOWDATE) REFERENCES MUSICSHOW(VENUE, SHOWDATE) ON DELETE CASCADE
);

INSERT INTO CONSISTSOF(
    TOURNAME,
    STARTDATE,
    VENUE,
    SHOWDATE
) VALUES (
    'American Idiot Tour',
    '01-JAN-2005',
    'Carnegie Hall',
    '01-JAN-2005'
);

INSERT INTO CONSISTSOF(
    TOURNAME,
    STARTDATE,
    VENUE,
    SHOWDATE
) VALUES (
    'Eagles Tour',
    '20-JAN-1982',
    'Woodstock',
    '20-JAN-1982'
);

INSERT INTO CONSISTSOF(
    TOURNAME,
    STARTDATE,
    VENUE,
    SHOWDATE
) VALUES (
    'Abbey Road Tour',
    '19-JUL-1986',
    'Madison Square Garden',
    '19-JUL-1986'
);

INSERT INTO CONSISTSOF(
    TOURNAME,
    STARTDATE,
    VENUE,
    SHOWDATE
) VALUES (
    'Proof Tour',
    '10-JUN-2022',
    'Rogers Arena',
    '10-JUN-2022'
);

INSERT INTO CONSISTSOF(
    TOURNAME,
    STARTDATE,
    VENUE,
    SHOWDATE
) VALUES (
    'Rodeo Tour',
    '05-NOV-2021',
    'NRG Park',
    '05-NOV-2021'
);

CREATE TABLE GOESON(
    BANDNAME CHAR(30),
    TOURNAME CHAR(30),
    STARTDATE DATE,
    CONSTRAINT GOESON_PK PRIMARY KEY (BANDNAME, TOURNAME, STARTDATE),
    CONSTRAINT GOESON_FK1 FOREIGN KEY (BANDNAME) REFERENCES BAND(BANDNAME) ON DELETE CASCADE,
    CONSTRAINT GOESON_FK2 FOREIGN KEY (TOURNAME, STARTDATE) REFERENCES TOUR(TOURNAME, STARTDATE) ON DELETE CASCADE
);

INSERT INTO GOESON(
    BANDNAME,
    TOURNAME,
    STARTDATE
) VALUES (
    'Green Day',
    'American Idiot Tour',
    '01-JAN-2005'
);

INSERT INTO GOESON(
    BANDNAME,
    TOURNAME,
    STARTDATE
) VALUES (
    'Eagles',
    'Eagles Tour',
    '20-JAN-1982'
);

INSERT INTO GOESON(
    BANDNAME,
    TOURNAME,
    STARTDATE
) VALUES (
    'The Beatles',
    'Abbey Road Tour',
    '19-JUL-1986'
);

INSERT INTO GOESON(
    BANDNAME,
    TOURNAME,
    STARTDATE
) VALUES (
    'BTS',
    'Proof Tour',
    '10-JUN-2022'
);

INSERT INTO GOESON(
    BANDNAME,
    TOURNAME,
    STARTDATE
) VALUES (
    'Travis Scott',
    'Rodeo Tour',
    '05-NOV-2021'
);

CREATE TABLE PERFORMEDAT(
    VENUE CHAR(30),
    SHOWDATE DATE,
    TITLE CHAR(30),
    ALBUMID CHAR(30),
    CONSTRAINT PERFORMEDAT_PK PRIMARY KEY (VENUE, SHOWDATE, TITLE, ALBUMID),
    CONSTRAINT PERFORMEDAT_FK1 FOREIGN KEY (VENUE, SHOWDATE) REFERENCES MUSICSHOW(VENUE, SHOWDATE) ON DELETE CASCADE,
    CONSTRAINT PERFORMEDAT_FK2 FOREIGN KEY (TITLE, ALBUMID) REFERENCES SONG(TITLE, ALBUMID) ON DELETE CASCADE
);

INSERT INTO PERFORMEDAT(
    VENUE,
    SHOWDATE,
    TITLE,
    ALBUMID
) VALUES (
    'Carnegie Hall',
    '01-JAN-2005',
    'American Idiot',
    'a1'
);

INSERT INTO PERFORMEDAT(
    VENUE,
    SHOWDATE,
    TITLE,
    ALBUMID
) VALUES (
    'Woodstock',
    '20-JAN-1982',
    'Take It Easy',
    'a2'
);

INSERT INTO PERFORMEDAT(
    VENUE,
    SHOWDATE,
    TITLE,
    ALBUMID
) VALUES (
    'Madison Square Garden',
    '19-JUL-1986',
    'Come Together',
    'a3'
);

INSERT INTO PERFORMEDAT(
    VENUE,
    SHOWDATE,
    TITLE,
    ALBUMID
) VALUES (
    'Rogers Arena',
    '10-JUN-2022',
    'Run BTS',
    'a4'
);

INSERT INTO PERFORMEDAT(
    VENUE,
    SHOWDATE,
    TITLE,
    ALBUMID
) VALUES (
    'NRG Park',
    '05-NOV-2021',
    '90210',
    'a5'
);

COMMIT;