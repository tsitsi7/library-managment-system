CREATE TABLE Books (
    ID INT PRIMARY KEY,
    title VARCHAR(255),
    genre VARCHAR(100),
    edition INT,
    author_id INT,
    img VARCHAR(255), 
    FOREIGN KEY (author_id) REFERENCES Author(ID)
);
