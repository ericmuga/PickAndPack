USE [pickandpack]
GO

/****** Object:  View [dbo].[pending_assembly]    Script Date: 27/03/2024 08:46:41 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO



CREATE OR ALTER   VIEW [dbo].[pending_assembly] as(


SELECT
    a.order_no,
    CASE WHEN a.shp_name = '' THEN a.customer_name ELSE a.shp_name END AS [shp_name],
    ISNULL(a.ext_doc_no,'') [ext_doc_no],
    a.shp_date,
    a.sp_code,
    a.sp_code+'|'+a.sp_name [sp_name],
    COALESCE(d.A_Assignment_Count, 0) AS A_Assignment_Count,
    COALESCE(d.B_Assignment_Count, 0) AS B_Assignment_Count,
    COALESCE(d.C_Assignment_Count, 0) AS C_Assignment_Count,
    COALESCE(d.D_Assignment_Count, 0) AS D_Assignment_Count,

	COALESCE(e.A_Assembly_Count, 0) AS A_Assembly_Count,
    COALESCE(e.B_Assembly_Count, 0) AS B_Assembly_Count,
    COALESCE(e.C_Assembly_Count, 0) AS C_Assembly_Count,
    COALESCE(e.D_Assembly_Count, 0) AS D_Assembly_Count,
	CASE when (select count ([line_no]) from [lines] where [order_no]=a.[order_no] and order_qty>0)=
	           (SELECT
					 count (*)
					 FROM
				 [assembly_lines]
				  inner join [assembly_sessions] on assembly_session_id=assembly_sessions.id
				  inner join [lines] on [assembly_lines].line_no=lines.line_no and [lines].order_no=assembly_sessions.order_no

			    ) then 1 else 0 end as
	[Complete],
	d.[assignee_id]
FROM
    [dbo].[orders] AS a
INNER JOIN
    [dbo].[order_parts] AS c ON a.order_no = c.order_no

LEFT JOIN
    (SELECT
	     assembly_sessions.order_no,

          CASE WHEN SUM(CASE WHEN lines.part = 'A' THEN 1 ELSE 0 END)>0 then 1 else 0 end  AS A_Assembly_Count,
         CASE WHEN SUM(CASE WHEN lines.part = 'B' THEN 1 ELSE 0 END)>0 then 1 else 0 end AS B_Assembly_Count,
        CASE WHEN SUM(CASE WHEN lines.part = 'C' THEN 1 ELSE 0 END)>0 then 1 else 0 end AS C_Assembly_Count,
         CASE WHEN SUM(CASE WHEN lines.part = 'D' THEN 1 ELSE 0 END)>0 then 1 else 0 end AS D_Assembly_Count

     FROM
         [assembly_lines]
	      inner join [assembly_sessions] on assembly_session_id=assembly_sessions.id
		  inner join [lines] on [assembly_lines].line_no=lines.line_no and [lines].order_no=assembly_sessions.order_no

     GROUP BY
          assembly_sessions.order_no
		 )
		 AS e ON a.order_no = e.order_no

LEFT JOIN
    (SELECT
        distinct  order_no,
		 [assignments].[assignee_id],
         SUM(CASE WHEN part = 'A' THEN 1 ELSE 0 END) AS A_Assignment_Count,
         SUM(CASE WHEN part = 'B' THEN 1 ELSE 0 END) AS B_Assignment_Count,
         SUM(CASE WHEN part = 'C' THEN 1 ELSE 0 END) AS C_Assignment_Count,
         SUM(CASE WHEN part = 'D' THEN 1 ELSE 0 END) AS D_Assignment_Count
     FROM
         [assignment_lines]
		 inner join [assignments] on assignment_lines.assignment_id=assignments.id
     GROUP BY
         order_no,
		 [assignments].[assignee_id]
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
	d.D_Assignment_Count,
	A_Assembly_Count,
	B_Assembly_Count,
	C_Assembly_Count,
	D_Assembly_Count,
	d.[assignee_id]
)

GO


