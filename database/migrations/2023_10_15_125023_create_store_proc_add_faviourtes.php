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
        CREATE PROCEDURE [dbo].[sp_proc_Add_Favourites]
            @Country_ID [smallint] = 1
            ,@User_ID	 [int]
            ,@Vendor	 [nvarchar](100)
            ,@Item_Key	 [bigint]
            ,@Price		 [numeric](19, 3) 
        AS
        BEGIN
            SET NOCOUNT ON;
            DECLARE @Time_Start datetime = getdate()

            SET NOCOUNT ON;

            IF (SELECT COUNT(1)
                FROM [User_Favourites]
                WHERE   [Country_ID]= @Country_ID
                    and [User_ID]	= @User_ID
                    and [Vendor]	= @Vendor
                    and [Item_Key]	= @Item_Key ) > 0
            BEGIN
                RAISERROR('Error: Supplied Favorites already exists, cannot add again!', 16, 1);
                RETURN -1;		-- Return a non-zero value to indicate error
            END
            ELSE
            BEGIN
                INSERT INTO dbo.[User_Favourites] 
                    (  [Country_ID]
                    ,[User_ID]
                    ,[Vendor]
                    ,[Item_Key]
                    ,[Price_when_Added]
                    ,[Date_Added]
                    )
                VALUES (
                    @Country_ID	
                    ,@User_ID		
                    ,@Vendor
                    ,@Item_Key
                    ,@Price
                    ,GETDATE()	--[Date_Added]
                );  

                print 'Favorite Added'
                PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
                RETURN 0; --sucess
            END

        END";
        
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Add_Favourites");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Add_Favourites");
    }
};
