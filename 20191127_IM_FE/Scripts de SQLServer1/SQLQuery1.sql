USE [BIZ_FAC]
GO

IF OBJECT_ID('DBO.SP_FE_EMPRESA') IS NOT NULL
	DROP PROCEDURE DBO.SP_FE_EMPRESA
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ============================================================================
-- AUTHOR......: JUAN PABLO SANCHEZ
-- CREATE DATE.: 27-NOV-2019
-- VERSION.....: 1.0.01
-- DESCRIPTION.: PROCEDIMIENTO 
--              
-- MODIFICACION: 27-NOV-2019
-- COMENTARIOS.: 
-- ============================================================================
-- PARAMETROS
-- @IN_OPERACION.: OPERACION A SER EJECUTADA
-- ============================================================================
CREATE PROCEDURE [dbo].[SP_FE_EMPRESA]
(
	@IN_CCI_EMPRESA VARCHAR(3) = NULL,
	@IN_CCI_SUCURSAL VARCHAR(6) = NULL,
	@IN_CCI_TIPOCMPR VARCHAR(5) = NULL,
	@IN_NCI_DOCUMENTO NUMERIC = NULL,
	@IN_CES_FE CHAR(1) = NULL,
	@IN_ENVIAR_MAIL CHAR(1) = NULL,
	@IN_GENERAR_PDF CHAR(1) = NULL,
	@IN_OPERACION VARCHAR(3)
)	
AS

-- ============================================================================
-- QE: QUERY EMPRESAS, VER LAS EMPRESAS QUE ESTAN EN EL PROCESO DE FACTURACION
--     ELECTRONICA
-- ============================================================================
IF @IN_OPERACION = 'QE'
BEGIN
	SELECT P.CCI_EMPRESA AS [value], 
	E.CNO_EMPRESA AS label 
	FROM BIZ_FAC..TB_FAC_FE_PARAMETROS P INNER JOIN BIZ_GEN..TB_SEG_EMPRESA E ON
	P.CCI_EMPRESA = E.CCI_EMPRESA
	WHERE E.CES_EMPRESA = 'A'
	AND P.CES_PARAMETROS = 'A'
	ORDER BY P.CCI_EMPRESA
END