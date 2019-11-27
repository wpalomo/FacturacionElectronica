use BIZ_FAC
go 

create view dbo.VI_FAC_FE_DOCUMENTOS
AS
SELECT f.cci_empresa, 
(select cno_empresa from BIZ_GEN..tb_seg_empresa where CCI_EMPRESA = f.CCI_EMPRESA) as cno_empresa,
f.cci_sucursal, 
f.cci_cliente, 
c.cno_cliprov,
f.dfm_fecha,
f.cci_tipocmpr, 
f.nci_documento,
f.id_log_fe,
f.cci_usuario,
f.dfx_reg_fecha,
f.ces_fe,
f.cci_clave_acceso
FROM (
        SELECT F.CCI_EMPRESA, 
        F.CCI_SUCURSAL, 
        F.CCI_CLIENTE, 
        F.DFM_FECHA,
        F.CCI_TIPOCMPR, 
        F.NCI_FACTURA AS NCI_DOCUMENTO,
        F.ID_LOG_FE,
        F.CCI_USUARIO,
        F.DFX_REG_FECHA,
        F.CES_FE,
        F.CCI_CLAVE_ACCESO
        FROM BIZ_FAC..TB_FAC_FACTURA F
        WHERE F.CCI_TIPOCMPR = 'FAC'
        AND F.CES_FACTURA IS NULL

        UNION

        SELECT F.CCI_EMPRESA, 
        F.CCI_SUCURSAL, 
        F.CCI_CLIENTE, 
        F.DFM_FECHA,
        F.CCI_TIPOCMPR, 
        F.NCI_FACTURA,
        F.ID_LOG_FE,
        F.CCI_USUARIO,
        F.DFX_REG_FECHA,
        F.CES_FE,
        F.CCI_CLAVE_ACCESO
        FROM BIZ_FAC..TB_FAC_FACTURA F
        WHERE F.CCI_TIPOCMPR = 'NC'
        AND F.CES_FACTURA IS NULL

        UNION

        SELECT DISTINCT R.CCI_EMPRESA,
        R.CCI_SUCURSAL,
        CMPR.COD_PROV AS CCI_CLIENTE,
        R.DFM_RETENCION,
        'RET' AS CCI_TIPOCMPR,
        R.NCI_RETENCION AS NCI_DOCUMENTO,
        R.ID_LOG_FE,
        R.CCI_USUARIO,
        R.DFM_PROCESO,
        R.CES_FE,
        R.CCI_CLAVE_ACCESO
        FROM BIZ_CNT..TB_BAN_PRO_CMPR CMPR WITH(NOLOCK) INNER JOIN BIZ_CNT..TB_BAN_PRO_CMPR_RETENCION R WITH(NOLOCK) ON
        CMPR.CCI_EMPRESA = R.CCI_EMPRESA
        AND CMPR.CCI_SUCURSAL = R.CCI_SUCURSAL
        AND CMPR.CMP_CODIGO = R.CMP_CODIGO

        UNION

        SELECT R.CCI_EMPRESA,
        R.CCI_SUCURSAL,	
        R.CCI_CLIENTE,
        R.DFM_EMISION,
        'GUI' AS CCI_TIPOCMPR,
        R.NCI_GUIA AS NCI_DOCUMENTO,
        R.ID_LOG_FE,
        R.CCI_USUARIO,
        R.DFM_REGISTRO,
        R.CES_FE,
        R.CCI_CLAVE_ACCESO
        FROM BIZ_INV_REP..TB_INV_GUIA_REMISION R
        ) F INNER JOIN BIZ_GEN..TB_GEN_CLIPROV C ON 
F.CCI_EMPRESA = C.CCI_EMPRESA
AND F.CCI_CLIENTE = C.CCI_CLIPROV INNER JOIN BIZ_FAC..TB_FAC_FE_PARAMETROS PFE ON
C.CCI_EMPRESA = PFE.CCI_EMPRESA	
WHERE F.DFM_FECHA >= PFE.DFM_FECHA_INICIO