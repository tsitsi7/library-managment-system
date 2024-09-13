CREATE TABLE Rates_Reviews (
    Rating INT PRIMARY KEY,
    Review VARCHAR(1000),
    Book_ID INT,
    Reader_ID INT,
    FOREIGN KEY (Book_ID) REFERENCES Books(ID),
    FOREIGN KEY (Reader_ID) REFERENCES Reader(Reader_ID)
);
