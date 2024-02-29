create view [loading_session_list] AS
(
SELECT
       a.[id] [loading_session_id]
      ,[vehicle_id]
      ,e.[plate] [vehicle_plate]
      ,b.[id] [loader_id]
      ,b.[name] [loader_name]
      ,[sp_code]
	  ,v.[search_name] [sp_name]
      ,[shp_date]
      ,isnull(a.[status],'pending')[status]
      ,isnull([system_entry],0)[system_entry]
  FROM [dbo].[loading_sessions] as a
  inner join [users] as b on a.[user_id]=b.id
  inner join [dbo].[vehicles] as e on e.[id]=a.vehicle_id
  inner join [dbo].[sales_persons] as v on v.[code]=a.[sp_code]
  --order by a.[id] desc
  )
