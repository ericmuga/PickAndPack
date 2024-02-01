USE [pickandpack-dev]
GO

/****** Object:  View [dbo].[pending_assignment]    Script Date: 01/02/2024 01:10:14 ******/
DROP VIEW [dbo].[pending_assignment]
GO

/****** Object:  View [dbo].[pending_assignment]    Script Date: 01/02/2024 01:10:14 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO




CREATE VIEW [dbo].[pending_assignment] as(
SELECT
    a.order_no,
    CASE WHEN a.shp_name = '' THEN a.customer_name ELSE a.shp_name END AS [shp_name],
    ISNULL(a.ext_doc_no,'') [ext_doc_no],
    a.shp_date,
    a.sp_code,
    a.sp_code+'|'+a.sp_name [sp_name],
    ISNULL(SUM(CASE WHEN c.part = 'A' THEN c.[weight] END),0) AS A_Weight,
    ISNULL(SUM(CASE WHEN c.part = 'B' THEN c.[weight] END),0) AS B_Weight,
    ISNULL(SUM(CASE WHEN c.part = 'C' THEN c.[weight] END),0) AS C_Weight,
    ISNULL(SUM(CASE WHEN c.part = 'D' THEN c.[weight] END),0) AS D_Weight,

    COALESCE(d.A_Assignment_Count, 0) AS A_Assignment_Count,
    COALESCE(d.B_Assignment_Count, 0) AS B_Assignment_Count,
    COALESCE(d.C_Assignment_Count, 0) AS C_Assignment_Count,
    COALESCE(d.D_Assignment_Count, 0) AS D_Assignment_Count
FROM
    [pickandpack-dev].[dbo].[orders] AS a
INNER JOIN
    [pickandpack-dev].[dbo].[order_parts] AS c ON a.order_no = c.order_no
LEFT JOIN
    (SELECT
         order_no,
         SUM(CASE WHEN part = 'A' THEN 1 ELSE 0 END) AS A_Assignment_Count,
         SUM(CASE WHEN part = 'B' THEN 1 ELSE 0 END) AS B_Assignment_Count,
         SUM(CASE WHEN part = 'C' THEN 1 ELSE 0 END) AS C_Assignment_Count,
         SUM(CASE WHEN part = 'D' THEN 1 ELSE 0 END) AS D_Assignment_Count
     FROM
         [assignment_lines]
     GROUP BY
         order_no
    ) AS d ON a.order_no = d.order_no
WHERE
    a.confirmed = 1
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
	d.A_Assignment_Count,
	d.B_Assignment_Count,
	d.C_Assignment_Count,
	d.D_Assignment_Count





)

GO


