CREATE TABLE "tblfeeds" (
  "feedId" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "feedDate" DATE NOT NULL,
  "feedTime" TIME NOT NULL,
  "feedAmount" int NOT NULL,
  "feedNotes" TEXT,
  "feedTemperature" FLOAT (5,2),
  "created" timestamp,
  "updated" timestamp
);