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
        CREATE   PROCEDURE [dbo].[sp_proc_Get_Favourites]
            @Country_ID [smallint] = 1
            ,@User_ID	 [int]
        AS
        BEGIN
            SET NOCOUNT ON;
            DECLARE @Time_Start datetime = getdate()

            SELECT [Country_ID]
            ,[User_ID]
            ,[Vendor]
            ,[Item_Key]
            ,[Date_Added]
            --,[Date_Removed]
            ,[Price_when_Added]
            ,[Latest_Price]
            ,[is_Item_still_Available]
            ,[Last_item_checked]
            FROM [dbo].[User_Favourites]
            WHERE [Country_ID] = @Country_ID
                and [User_ID] = @User_ID
            ORDER BY [Date_Added];

            PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
            RETURN 0;

        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Favourites");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Favourites");
    }
};
