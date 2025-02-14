CREATE TABLE [dbo].[HorariosSemanais] (
    [IdHorarioSemanal] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [IdProfissional] INT NOT NULL,
    [DataInicio] DATE NOT NULL,
    [DataFim] DATE NOT NULL,
    [Ativo] BIT DEFAULT 1,
    CONSTRAINT [FK_HorariosSemanais_ToProfissionais] 
        FOREIGN KEY ([IdProfissional]) 
        REFERENCES [UtilizadoresProfissionais]([Id])
)