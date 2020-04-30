CREATE TABLE IF NOT EXISTS users(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    isStudent bit DEFAULT 1,
    name varchar(255) NOT NULL,
    middlename varchar(255),
    surname varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    password varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tasks(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    type varchar(255),
    task text
);

CREATE TABLE IF NOT EXISTS checks(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    studentID int(10),
    professorID int(10),
    taskID int(10),
    score int(10),
    answer text,

    FOREIGN KEY (studentID) REFERENCES users(id),
    FOREIGN KEY (professorID) REFERENCES users(id),  
    FOREIGN KEY (taskID) REFERENCES tasks(id) 
);


CREATE TABLE IF NOT EXISTS petitions(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    studentID int(10),
    professorID int(10),
    petition text,
    decision text,

    FOREIGN KEY (studentID) REFERENCES users(id),
    FOREIGN KEY (professorID) REFERENCES users(id)
);