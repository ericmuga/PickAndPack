USE [pickandpack-dev]
GO

/****** Object:  View [dbo].[pending_confirmation]    Script Date: 06/02/2024 03:59:33 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO






CREATE OR ALTER       VIEW [dbo].[pending_confirmation] as(
SELECT
    a.order_no,
    CASE WHEN a.shp_name = '' THEN a.customer_name ELSE a.shp_name END AS [shp_name],
    a.ext_doc_no,
    a.shp_date,
    a.ended_by,
    a.ending_time,
    a.ending_date,
    a.sp_code,
    a.sp_code+'|'+a.sp_name [sp_name],
    COUNT(CASE WHEN c.part = 'A' THEN 1 END) AS A_Count,
    COUNT(CASE WHEN c.part = 'B' THEN 1 END) AS B_Count,
    COUNT(CASE WHEN c.part = 'C' THEN 1 END) AS C_Count,
    COUNT(CASE WHEN c.part = 'D' THEN 1 END) AS D_Count,
    COALESCE(d.A_Confirmation_Count, 0) AS A_Confirmation_Count,
    COALESCE(d.B_Confirmation_Count, 0) AS B_Confirmation_Count,
    COALESCE(d.C_Confirmation_Count, 0) AS C_Confirmation_Count,
    COALESCE(d.D_Confirmation_Count, 0) AS D_Confirmation_Count
FROM
    [pickandpack-dev].[dbo].[orders] AS a
INNER JOIN
    [pickandpack-dev].[dbo].[order_parts] AS c ON a.order_no = c.order_no
LEFT JOIN
    (SELECT
         order_no,
         SUM(CASE WHEN part_no = 'A' THEN 1 ELSE 0 END) AS A_Confirmation_Count,
         SUM(CASE WHEN part_no = 'B' THEN 1 ELSE 0 END) AS B_Confirmation_Count,
         SUM(CASE WHEN part_no = 'C' THEN 1 ELSE 0 END) AS C_Confirmation_Count,
         SUM(CASE WHEN part_no = 'D' THEN 1 ELSE 0 END) AS D_Confirmation_Count
     FROM
         [confirmations]
     GROUP BY
         order_no
    ) AS d ON a.order_no = d.order_no
WHERE
    a.confirmed = 0 and a.shp_date>=DATEADD(dd, DATEDIFF(dd, 0, GETDATE()) - 1, 0)
GROUP BY
    a.order_no,
    CASE WHEN a.shp_name = '' THEN a.customer_name ELSE a.shp_name END,
    a.ext_doc_no,
    a.shp_date,
    a.ended_by,
    a.ending_time,
    a.ending_date,
    a.sp_code,
    a.sp_name,
	d.A_Confirmation_Count,
	d.B_Confirmation_Count,
	d.C_Confirmation_Count,
	d.D_Confirmation_Count

)

GO


