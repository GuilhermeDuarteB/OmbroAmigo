CREATE TABLE ChatConsultas (
    ID INT PRIMARY KEY IDENTITY(1,1),
    ConsultaId INT,
    IdUser INT,
    IdProfissional INT,
    Mensagem NVARCHAR(MAX),
    DataHora DATETIME DEFAULT GETDATE(),
    EnviadoPor VARCHAR(20) -- 'paciente' ou 'profissional'
);