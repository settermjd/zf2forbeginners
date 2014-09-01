CREATE TABLE tblfeeds (
  feedId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  feedDate DATE NOT NULL,
  feedTime TIME NOT NULL,
  feedAmount INT NOT NULL,
  feedNotes TEXT,
  feedTemperature FLOAT(5,2),
  created timestamp,
  updated timestamp
);