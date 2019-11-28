use biz_fac
go

create nonclustered index IX_TB_FAC_FACTURA_16
on dbo.TB_FAC_FACTURA(cci_tipocmpr, ces_factura)
INCLUDE ([CCI_CLIENTE],[DFM_FECHA],[DFX_REG_FECHA],[CCI_USUARIO],[CES_FE],[CCI_CLAVE_ACCESO],[ID_LOG_FE])


--drop index IX_TB_FAC_FACTURA_16 on dbo.TB_FAC_FACTURA