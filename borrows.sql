CREATE TABLE Borrows (
    Reader_ID INT,
    Books_ID INT,
    Reserve_Date DATE,
    Due_date DATE,
    Return_date DATE,
    Staff_ID INT,
    PRIMARY KEY (Reader_ID, Books_ID),
    FOREIGN KEY (Reader_ID) REFERENCES Reader(Reader_ID),
    FOREIGN KEY (Books_ID) REFERENCES Books(ID),
    FOREIGN KEY (Staff_ID) REFERENCES Library_staff(ID)
);
