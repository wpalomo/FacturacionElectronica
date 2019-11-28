/*
Faltan detalles del índice de SQLQuery5.sql - JPSANCHEZ.BIZ_FAC (sa (65))
El procesador de consultas estima que la implementación del siguiente índice podría mejorar el costo de la consulta en un 21.2507%.
*/


USE [BIZ_CNT]
GO
CREATE NONCLUSTERED INDEX [IX_TB_BAN_PRO_CMPR_RETENCION_01]
ON [dbo].[TB_BAN_PRO_CMPR_RETENCION] ([CCI_EMPRESA],[DFM_RETENCION])
INCLUDE ([CCI_SUCURSAL],[CMP_CODIGO],[NCI_RETENCION],[DFM_PROCESO],[CCI_USUARIO],[CES_FE],[CCI_CLAVE_ACCESO],[ID_LOG_FE])
GO

