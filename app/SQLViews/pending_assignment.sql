USE [pickandpack-dev]
GO

/****** Object:  View [dbo].[pending_assignment]    Script Date: 14/03/2024 05:03:53 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO








CREATE OR ALTER   VIEW [dbo].[pending_assignment] as(
SELECT
    a.order_no,
    CASE WHEN a.shp_name = '' THEN a.customer_name ELSE a.shp_name END AS [shp_name],
    ISNULL(a.ext_doc_no,'') [ext_doc_no],
    a.shp_date,
    a.sp_code,
    a.sp_code+'|'+a.sp_name [sp_name],
        ISNULL(ROUND(SUM(CASE WHEN c.part = 'A' THEN c.[weight] ELSE 0 END), 0), 0) AS A_Weight,
        ISNULL(ROUND(SUM(CASE WHEN c.part = 'B' THEN c.[weight] ELSE 0 END), 0), 0) AS B_Weight,
        ISNULL(ROUND(SUM(CASE WHEN c.part = 'C' THEN c.[weight] ELSE 0 END), 0), 0) AS C_Weight,
        ISNULL(ROUND(SUM(CASE WHEN c.part = 'D' THEN c.[weight] ELSE 0 END), 0), 0) AS D_Weight,
	   ISNULL(ROUND(SUM(CASE WHEN c.part = 'A' THEN c.[items] ELSE 0 END), 0), 0) AS A_Items,
	   ISNULL(ROUND(SUM(CASE WHEN c.part = 'B' THEN c.[items] ELSE 0 END), 0), 0) AS B_Items,
	   ISNULL(ROUND(SUM(CASE WHEN c.part = 'C' THEN c.[items] ELSE 0 END), 0), 0) AS C_Items,
	   ISNULL(ROUND(SUM(CASE WHEN c.part = 'D' THEN c.[items] ELSE 0 END), 0), 0) AS D_Items,

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
    a.confirmed = 1 and a.shp_date>=DATEADD(dd, DATEDIFF(dd, 0, GETDATE()) - 1, 0)
	and ((select count(*) from [pickandpack-dev].[dbo].[order_parts])> (select count (*) from [pickandpack-dev].[dbo].[assignment_lines] as h where h.order_no=a.order_no) )

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


