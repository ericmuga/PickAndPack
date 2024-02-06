USE [pickandpack-dev]
GO

/****** Object:  View [dbo].[order_parts]    Script Date: 05/02/2024 17:11:04 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO








CREATE OR ALTER     VIEW [dbo].[order_parts] AS( SELECT DISTINCT
  b.shp_date,
  a.part,
  b.order_no,
  b.order_no+'|'+a.part [order_part],
  a.part+'|'+b.customer_no+'|'+b.shp_name+'|'+b.sp_code [description]
  ,ISNULL(sum(a.qty_base),0)[weight]
  ,ISNULL(COUNT(a.item_no),0)[items]

  FROM [pickandpack-dev].[dbo].[lines] as a
  INNER JOIN  [pickandpack-dev].[dbo].[orders] as b on a.order_no=b.order_no
  where b.shp_date>=DATEADD(dd, DATEDIFF(dd, 0, GETDATE()) -1, 0)

  group by
  b.shp_date,
  a.part,
  b.order_no,
  a.order_no,
  b.customer_no,b.customer_name,b.shp_name,b.sp_code
 )
GO


