CREATE TABLE [dbo].[PeriodosAtendimento] (
    [IdPeriodo] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [IdHorarioSemanal] INT NOT NULL,
    [DiaSemana] TINYINT NOT NULL, -- 1 = Domingo, 2 = Segunda, etc.
    [HoraInicio] TIME NOT NULL,
    [HoraFim] TIME NOT NULL,
    CONSTRAINT [FK_PeriodosAtendimento_ToHorariosSemanais] 
        FOREIGN KEY ([IdHorarioSemanal]) 
        REFERENCES [HorariosSemanais]([IdHorarioSemanal]),
    CONSTRAINT [CK_DiaSemana] CHECK ([DiaSemana] BETWEEN 1 AND 7)
)