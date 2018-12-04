CREATE FUNCTION Check_User_Signup_Date(Check_Date DATE, Check_Username VARCHAR(255))
RETURNS BOOLEAN AS $$
DECLARE User_Signup_Date DATE;
BEGIN
	SELECT (SELECT (Signup_Date) FROM Users WHERE (Username=Check_Username)) INTO User_Signup_Date;
	
	IF (User_Signup_Date<=Check_Date) THEN
		RETURN 1;
	ELSE 
		RETURN 0;
	END IF;
END;$$ LANGUAGE 'plpgsql' IMMUTABLE;


CREATE FUNCTION Check_User_Participation_Date(Check_Date DATE, Check_Discussion_ID INTEGER, Check_Username VARCHAR(255))
RETURNS BOOLEAN AS $$
DECLARE User_Subscription_Date DATE;
DECLARE User_Unsubscription_Date DATE;
DECLARE Check_Event_ID INTEGER;
BEGIN
	SELECT (SELECT (Event_ID) FROM Events WHERE (Check_Discussion_ID=Discussion_Id)) INTO Check_Event_ID;
	SELECT (SELECT (Subscription_Date) FROM Participate WHERE (Username_Participant=Check_Username AND Event_ID=Check_Event_ID)) INTO User_Subscription_Date;
	SELECT (SELECT (Unsubscription_Date) FROM Participate WHERE (Username_Participant=Check_Username AND Event_ID=Check_Event_ID)) INTO User_Unsubscription_Date;

	IF (Check_Date>=User_Subscription_Date AND (User_Unsubscription_Date IS NULL)) THEN
		RETURN 1;
	ELSE 
		RETURN 0;
	END IF;
END;$$ LANGUAGE 'plpgsql' IMMUTABLE;

CREATE FUNCTION Check_Message_Sending_Date(Check_Date DATE, Check_Message_ID INTEGER)
RETURNS BOOLEAN AS $$
DECLARE Message_Sending_Date DATE;
BEGIN
	SELECT (SELECT (Sending_Date) FROM Message WHERE (Message_ID=Check_Message_ID)) INTO Message_Sending_Date;
	
	IF (Message_Sending_Date<=Check_Date) THEN
		RETURN 1;
	ELSE 
		RETURN 0;
	END IF;
END;$$ LANGUAGE 'plpgsql' IMMUTABLE;

CREATE FUNCTION Check_Discussion_Date(Check_Date DATE, Check_Discussion_ID INTEGER)
RETURNS BOOLEAN AS $$
DECLARE Discussion_Creation_Date DATE;
BEGIN
	SELECT (SELECT (Discussion_Date) FROM Discussion WHERE (Discussion_ID=Check_Discussion_ID)) INTO Discussion_Creation_Date;

	IF (Discussion_Creation_Date<=Check_Date) THEN
		RETURN 1;
	ELSE 
		RETURN 0;
	END IF;
END;$$ LANGUAGE 'plpgsql' IMMUTABLE;

CREATE FUNCTION Check_Proposition_Date(Check_Date DATE, Check_Event_ID INTEGER)
RETURNS BOOLEAN AS $$
DECLARE Event_Proposition_Date DATE;
BEGIN
	SELECT (SELECT (Proposition_Date) FROM Events WHERE (Event_ID=Check_Event_ID)) INTO Event_Proposition_Date;

	IF (Event_Proposition_Date<=Check_Date) THEN
		RETURN 1;
	ELSE 
		RETURN 0;
	END IF;
END;$$ LANGUAGE 'plpgsql' IMMUTABLE;


CREATE TABLE Account (
	Username VARCHAR(255) NOT NULL PRIMARY KEY,
	User_Password VARCHAR(255) NOT NULL,
	Is_Admin BOOLEAN
);


CREATE TABLE Users (
	Username VARCHAR(255) REFERENCES Account(Username) PRIMARY KEY,
	Email VARCHAR(255) NOT NULL,
	Last_Name VARCHAR(255) NOT NULL,
	First_Name VARCHAR(255) NOT NULL,
	Description TEXT,
	Birthday_User DATE,
	Phone_Number VARCHAR(63),
	Place_Of_Birth VARCHAR(255),
	Connected BOOLEAN,
	Confirmed BOOLEAN,
	User_Picture VARCHAR(255),
	Confirmation_Date DATE,
	Signup_Date DATE NOT NULL,
	Modification_Date DATE NOT NULL,
	Deletion_Date DATE,
	
	CONSTRAINT Users_Confirmation_Date_Check CHECK (Confirmation_Date >= Signup_Date OR Confirmation_Date=NULL),
	CONSTRAINT Users_Modification_Date_Check CHECK (Modification_Date >= Signup_Date AND Modification_Date >= Confirmation_Date AND Modification_Date >= Deletion_Date),
	CONSTRAINT Users_Deletion_Date_Check CHECK (Deletion_Date >= Signup_Date OR Deletion_Date=NULL)
);

CREATE TABLE Discussion (
	Discussion_ID SERIAL PRIMARY KEY,
	Discussion_Date DATE NOT NULL
);


CREATE TABLE Theme (
	Theme_ID SERIAL PRIMARY KEY,
	Theme_Title VARCHAR(255) NOT NULL
);

CREATE TABLE Guest (
	Guest_ID SERIAL PRIMARY KEY,
	Guest_Title VARCHAR(255) NOT NULL
);

CREATE TABLE Events (
	Event_ID SERIAL PRIMARY KEY,
	Event_Time TIME NOT NULL,
	Event_Date DATE NOT NULL,
	Event_Address VARCHAR(255) NOT NULL,
	Event_City  VARCHAR(255) NOT NULL,
	Event_Title VARCHAR(255) NOT NULL,
	Description TEXT,
	Capacity INTEGER,
	Event_Picture VARCHAR(255),
	Confirmed BOOLEAN,
	Deadline_Date DATE,
	Proposition_Date DATE NOT NULL,
	Confirmation_Date DATE,
	Modification_Date DATE NOT NULL,
	Deletion_Date DATE,
	Theme_ID INTEGER REFERENCES Theme(Theme_ID),
	Guest_ID INTEGER REFERENCES Guest(Guest_ID),
	Username_Organizer VARCHAR(255) REFERENCES Users(Username),
	Discussion_ID INTEGER REFERENCES Discussion(Discussion_ID),
	
	CONSTRAINT Events_Confirmation_Date_Check CHECK (Confirmation_Date >= Proposition_Date OR Confirmation_Date=NULL),
	CONSTRAINT Events_Modification_Date_Check CHECK (Modification_Date >= Proposition_Date AND Modification_Date >= Confirmation_Date AND Modification_Date >= Deletion_Date),
	CONSTRAINT Events_Deletion_Date_Check CHECK (Deletion_Date >= Proposition_Date OR Deletion_Date=NULL),
	CONSTRAINT Events_Deadline_Date_Check CHECK (Deadline_Date >= Proposition_Date OR Deadline_Date=NULL),
	CONSTRAINT Events_Proposition_Date_Check CHECK (Check_User_Signup_Date(Proposition_Date, Username_Organizer))
);
CREATE TABLE Participate (
	Username_Participant VARCHAR(255) REFERENCES Users(Username),
	Event_ID INTEGER REFERENCES Events(Event_ID),
	Subscription_Date DATE NOT NULL,
	Unsubscription_Date DATE,
	
	PRIMARY KEY (Username_Participant, Event_ID),
	
	CONSTRAINT Participate_Unsubscription_Date_Check CHECK  (Unsubscription_Date >= Subscription_Date OR Unsubscription_Date=NULL),
	CONSTRAINT Participate_Subscription_Date_Check CHECK ((Check_User_Signup_Date(Subscription_date, Username_Participant)) AND (Check_Proposition_Date(Subscription_Date, Event_ID)))
);

CREATE TABLE Message (
	Message_ID SERIAL PRIMARY KEY,
	Message_Content TEXT NOT NULL,
	Sending_Date DATE NOT NULL,
	Sending_Time TIME NOT NULL,
	Username_Transmitter VARCHAR(255) NOT NULL REFERENCES Users(Username),
	Discussion_ID INTEGER REFERENCES Discussion(Discussion_ID),
	
	CONSTRAINT Message_Sending_Date_Check CHECK ((Check_Discussion_Date(Sending_Date, Discussion_ID) AND Check_User_Participation_Date(Sending_Date, Discussion_ID, Username_Transmitter)))
);


CREATE TABLE Notification (
	Notification_ID SERIAL PRIMARY KEY,
	Notification_Content TEXT NOT NULL,
	Notification_Date DATE NOT NULL,
	Notification_Time TIME NOT NULL,
	Seen BOOLEAN,
	Username_Receiver VARCHAR(255) REFERENCES Users(Username),
	
	CONSTRAINT Notification_Notification_Date_Check CHECK (Check_User_Signup_Date(Notification_Date, Username_Receiver))
);

CREATE TABLE Invitation (
	Invitation_ID SERIAL PRIMARY KEY,
	Invitation_Date DATE NOT NULL,
	Acceptance_Date DATE,
	Acceptance_Time TIME,
	Username_Receiver VARCHAR(255) NOT NULL REFERENCES Users(Username),
	Username_Sender VARCHAR(255) NOT NULL REFERENCES Users(Username),
	
	CONSTRAINT Invitation_Acceptance_Date_Check CHECK (Acceptance_Date >= Invitation_Date OR Acceptance_Date=NULL),
	CONSTRAINT Invitation_Invitation_Date_Check CHECK ((Check_User_Signup_Date(Invitation_Date, Username_Sender)) AND (Check_User_Signup_Date(Invitation_Date, Username_Receiver)))
);


CREATE TABLE Receive (
	Message_ID INTEGER REFERENCES Message(Message_ID),
	Username_Receiver VARCHAR(255) REFERENCES Users(Username),
	Seen_Time TIME,
	Seen_Date DATE,
	PRIMARY KEY (Message_ID, Username_Receiver),

	CONSTRAINT Receive_Seen_Date_Check CHECK ((Check_User_Signup_Date(Seen_Date, Username_Receiver) AND Check_Message_Sending_Date(Seen_Date, Message_ID)) OR Seen_Date=NULL)
);
