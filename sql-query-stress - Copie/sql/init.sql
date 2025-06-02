-- init.sql : Script de création de base de test pour SQL Server

-- Crée une base de données nommée TestStress
CREATE DATABASE TestStress;
GO

-- Utilise cette base
USE TestStress;
GO

-- Crée une table Clients
CREATE TABLE Clients (
    Id INT PRIMARY KEY IDENTITY(1,1),
    Nom NVARCHAR(100) NOT NULL,
    Email NVARCHAR(100) NOT NULL
);
GO

-- Insère des données de test
INSERT INTO Clients (Nom, Email) VALUES
('Jean Dupont', 'jean@example.com'),
('Marie Curie', 'marie@example.com'),
('Alan Turing', 'alan@example.com'),
('Ada Lovelace', 'ada@example.com'),
('Grace Hopper', 'grace@example.com');
GO
