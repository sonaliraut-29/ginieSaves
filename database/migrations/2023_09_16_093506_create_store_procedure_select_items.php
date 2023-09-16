<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $procedure = "
        CREATE PROCEDURE [dbo].[sp_proc_get_items] 
            
            @category1			nvarchar(100) = N'',
            @category2			nvarchar(100) = N'',
            @category3			nvarchar(100) = N'',
            @price_from			int = 0,
            @price_to			int = 999999,
            @vendor				nvarchar(500) = N'', 
            @brand				nvarchar(500) = N'',
            @exclude_accessory  bit = 1,
            @only_discounted	bit = 0,
            @available_only		bit = 1,
            @search_text		nvarchar(200) = N'',
        
            @order_by			nvarchar(100) = N'ORDER BY NEWID()',	--' ORDER BY Discounted_Price',
            @offset_rows		int = 0,
            @page_size			int = 20
        AS
        BEGIN
            --Stag 0: init
            SET NOCOUNT ON;
            declare 
                 @query_full	nvarchar(3000)
                ,@query_base	varchar(3000)
                ,@query_where	nvarchar(3000)
                ,@query_offset	nvarchar(3000)
                --,@offset_rows	int		=	0
                --,@page_size	int		=	0	--4*2
        
                ,@Time_Start	datetime= getdate()
                ,@exclude		nvarchar(100) = N''
        
                --declare @search_text nvarchar(100) = 'IPHONE 256 GB i5 14'
                declare @search_text1 nvarchar(100) = '', 
                        @search_text2 nvarchar(100) = '', 
                        @search_text3 nvarchar(100) = '', 
                        @search_text4 nvarchar(100) = '', 
                        @search_text5 nvarchar(100) = ''
        
                declare @num_of_words smallint = 
                            case when @search_text = Null or @search_text = '' then 0 
                                    else LEN(@search_text) - LEN(REPLACE(@search_text, ' ', '')) + 1
                            end
        
                declare @count smallint = 0
                declare @search_id nvarchar(100)  = 0
                declare @start_pos smallint = 1, @end_pos smallint = 0
                        ,@end_pos2 smallint = 0
                declare @search_str varchar(max) = iif(@num_of_words > 0, 'and (', '')
                declare @str varchar(max) = ''
        
                --Stag 1: process text search
                WHILE (@count < @num_of_words)
                BEGIN
                    set @start_pos = @end_pos +1
                    set @end_pos = CHARINDEX(' ', @search_text, @start_pos)
                    set @search_id = trim(substring(@search_text, @start_pos, iif(@end_pos>0, @end_pos-@start_pos,99)))
        
                    --select @count, @search_id, @start_pos, @end_pos
        
                    if @count+1 = 1
                        set @search_text1 = @search_id
                    if @count+1 = 2
                        set @search_text2 = @search_id
                    if @count+1 = 3
                        set @search_text3 = @search_id
                    if @count+1 = 4
                        set @search_text4 = @search_id
                    if @count+1 = 5
                        set @search_text5 = @search_id
        
                    --select @str, @search_str
                    set @count = @count + 1
                END
        
            --select @search_text, @search_text1, @search_text2, @search_text3, @search_text4, @search_text5
            --Stag 3: build dynamic query
            SET @query_base = 
                  N'     Select --top 1000 
                     Vendor,	--Page_Level1,	Page_Level2,	Page_Level3,	Page_Level4,	
                     Item_name,	categ,	Brand,	--Item_Category, Item_Key,	Item_Key2,		
                     Regular_Price,	Discounted_Price as Selling_Price,	Discount_Percent,	Discount_Start_Date,	Discount_End_Date,	
                     is_Item_Available,	Unit_Type,	Unit,	Item_Image_URL,	Item_URL, Data_Asof 
                    from [Items_Combined] '							+ CHAR(10) + CHAR(9)
            SET @query_where = N''								
        
            IF @search_text1 <> ''
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', ' AND')
                SET @query_where = @query_where	+ N'   Item_name like ''%' + @search_text1 + '%'' '			+ CHAR(10) + CHAR(9)
            END
            IF @search_text2 <> ''
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', ' AND')
                SET @query_where = @query_where	+ N'   Item_name like ''%' + @search_text2 + '%'' '			+ CHAR(10) + CHAR(9)
            END
        
            IF @search_text3 <> ''
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', ' AND')
                SET @query_where = @query_where	+ N'   Item_name like ''%' + @search_text3 + '%'' '			+ CHAR(10) + CHAR(9)
            END
        
            IF @search_text4 <> ''
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', ' AND')
                SET @query_where = @query_where	+ N'   Item_name like ''%' + @search_text4 + '%'' '			+ CHAR(10) + CHAR(9)
            END
        
            IF @search_text5 <> ''
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', ' AND')
                SET @query_where = @query_where	+ N'   Item_name like ''%' + @search_text5 + '%'' '			+ CHAR(10) + CHAR(9)
            END
        
            --SET @where = @where	+ IIF(len(@where) > 7, IIF(@search_text1 <> N'', N' OR page_level3   like ''%' + @search_text1 + '%'' ' + CHAR(10) + CHAR(9), ''), '')
            --SET @where = @where	+ IIF(len(@where) > 7, IIF(@search_text1 <> N'', N' OR page_level4   like ''%' + @search_text1 + '%'' ' + CHAR(10) + CHAR(9), ''), '')
            --SET @where = @where	+ IIF(len(@where) > 7, IIF(@search_text1 <> N'', N' OR categ         like ''%' + @search_text1 + '%'' ' + CHAR(10) + CHAR(9), ''), '')
        
            --SET @query_where = @query_where	+ IIF(len(@query_where) > 7, ')', '')  + CHAR(10) + CHAR(9)
            IF @vendor <> ''
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', '   AND')
                SET @query_where = @query_where	+ ' Vendor in (' + @vendor  + ')'		+ CHAR(10) + CHAR(9)
            END
        
            IF @brand <> ''
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', '   AND')
                SET @query_where = @query_where	+ ' Brand in (' + @brand  + ')'		+ CHAR(10) + CHAR(9)
            END
        
            if @price_from <> 0 or @price_to <> 999999
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', '   AND')
                SET @query_where = @query_where	+ ' Discounted_Price between ' + trim(str(@price_from)) + ' and ' + + trim(str(@price_to)) + CHAR(10) + CHAR(9)
            END
        
            IF @only_discounted = 1
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', '   AND')
                SET @query_where = @query_where	+ ' Discount_Percent > 0'	+ CHAR(10) + CHAR(9)
            END
        
            IF @available_only = 1
            BEGIN
                SET @query_where = @query_where	+ IIF(@query_where = '', 'WHERE ', '   AND')
                SET @query_where = @query_where	+ ' is_Item_Available = 1'	+ CHAR(10) + CHAR(9)
            END
            SET @query_where = @query_where	+ IIF(len(@query_where) > 7, IIF(@category1  <> N'', N' AND categ   like ''%' + @category1     + '%'' ' + CHAR(10) + CHAR(9), ''), '')
            --SET @query_where = @query_where	+ IIF(len(@query_where) > 7, IIF(@brand     <> N'', N' AND Brand   like ''%' + @brand        + '%'' ' + CHAR(10) + CHAR(9), ''), '')
            --SET @query_where = @query_where	+ IIF(len(@query_where) > 7, IIF(@vendor	<> N'', N' AND Vendor  like ''%' + @vendor       + '%'' ' + CHAR(10) + CHAR(9), ''), '')
            --Excludes
            SET @query_where = @query_where	+ IIF(len(@query_where) > 7, IIF(@exclude   <> N'', N' AND categ '       + IIF(LEFT(@exclude,1) = '-', 'NOT', '') + ' like ''%' + IIF(LEFT(@exclude,1) = '-', SUBSTRING(@exclude, 2, 99), '')  + '%'' ' + CHAR(10) + CHAR(9), ''), '')
            SET @query_where = @query_where	+ IIF(len(@query_where) > 7, IIF(@exclude   <> N'', N' AND Page_Level3 ' + IIF(LEFT(@exclude,1) = '-', 'NOT', '') + ' like ''%' + IIF(LEFT(@exclude,1) = '-', SUBSTRING(@exclude, 2, 99), '')  + '%'' ' + CHAR(10) + CHAR(9), ''), '')
            SET @query_where = @query_where	+ IIF(len(@query_where) > 7, IIF(@exclude   <> N'', N' AND Page_Level2 ' + IIF(LEFT(@exclude,1) = '-', 'NOT', '') + ' like ''%' + IIF(LEFT(@exclude,1) = '-', SUBSTRING(@exclude, 2, 99), '')  + '%'' '                     , ''), '')
        
            --SET @query_where = @query_where	+ IIF(CHARINDEX(')', @query_where) = 0, ')', '')  + CHAR(10) + CHAR(9)
            SET @query_offset = IIF(@page_size > 0, ' OFFSET ' + CAST(@offset_rows as nvarchar) + ' ROWS FETCH NEXT ' + CAST(@page_size as nvarchar) + ' ROWS ONLY', '')	 + CHAR(10) + CHAR(9)
            SET @query_full = @query_base + @query_where + @order_by + CHAR(10) + CHAR(9)+ @query_offset
            print @query_full
        
            --Stage 4: execute the query
            IF	(   @query_full like '%xp_cmdshell%'
                OR	@query_full like '%DROP%'
                OR	@query_full like '%TRUNCATE%'
                OR	@query_full like '%CREATE%'
                OR	@query_full like '%UPDATE%'
                OR	@query_full like '%DELETE%')
                    PRINT '*** Illegal operation... SQL Aborted!!! ***';
            ELSE
                EXEC sp_executesql @query_full;
        
            PRINT ''	
            PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
        END";
        
        DB::unprepared("DROP procedure IF EXISTS sp_proc_get_items");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_get_items");
    }
};
