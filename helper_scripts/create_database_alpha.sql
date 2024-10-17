-- Execute with: mysql -u root -p < create_database.sql
-- Create the database
CREATE DATABASE IF NOT EXISTS story_db;

-- Use the database
USE story_db;

-- Create the Users table
CREATE TABLE IF NOT EXISTS Users (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(50) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    EULA_Agreement BOOLEAN DEFAULT FALSE,
    Data_Collection_Agreement BOOLEAN DEFAULT FALSE,
    Preferences JSON
);

-- Create the Tags table
CREATE TABLE IF NOT EXISTS Tags (
    TagID INT AUTO_INCREMENT PRIMARY KEY,
    TagName VARCHAR(50) UNIQUE NOT NULL
);

-- Create the Stories table
CREATE TABLE IF NOT EXISTS Stories (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Story_Name VARCHAR(100) NOT NULL
);

-- Create the StoryTags table (Join table)
CREATE TABLE IF NOT EXISTS StoryTags (
    StoryID INT NOT NULL,
    TagID INT NOT NULL,
    PRIMARY KEY (StoryID, TagID),
    FOREIGN KEY (StoryID) REFERENCES Stories(ID) ON DELETE CASCADE,
    FOREIGN KEY (TagID) REFERENCES Tags(TagID) ON DELETE CASCADE
);

-- Create the Passages table
CREATE TABLE IF NOT EXISTS Passages (
    PassageID INT AUTO_INCREMENT PRIMARY KEY,
    StoryID INT NOT NULL,
    Text TEXT NOT NULL,
    FOREIGN KEY (StoryID) REFERENCES Stories(ID) ON DELETE CASCADE
);

-- Create the Choices table
CREATE TABLE IF NOT EXISTS Choices (
    ChoiceID INT AUTO_INCREMENT PRIMARY KEY,
    PassageID INT NOT NULL,
    ChoiceText VARCHAR(255) NOT NULL,
    NextPassageID INT,
    IsFinal BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (PassageID) REFERENCES Passages(PassageID) ON DELETE CASCADE,
    FOREIGN KEY (NextPassageID) REFERENCES Passages(PassageID) ON DELETE SET NULL
);

-- Create the StoriesPlayed table
CREATE TABLE IF NOT EXISTS StoriesPlayed (
    UserID INT NOT NULL,
    StoryID INT NOT NULL,
    CurrentPassageID INT,
    PRIMARY KEY (UserID, StoryID),
    FOREIGN KEY (UserID) REFERENCES Users(ID) ON DELETE CASCADE,
    FOREIGN KEY (StoryID) REFERENCES Stories(ID) ON DELETE CASCADE,
    FOREIGN KEY (CurrentPassageID) REFERENCES Passages(PassageID) ON DELETE SET NULL
);
