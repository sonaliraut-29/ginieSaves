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
        $procedure ="CREATE   PROCEDURE [dbo].[sp_proc_Remove_Favourite]
             @Country_ID [smallint] = 1
            ,@User_ID	 [int]
            ,@Vendor	 [nvarchar](100)
            ,@Item_Key	 [bigint]
            --,@Price		 [numeric](19, 3) 
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
                    and [Item_Key]	= @Item_Key ) = 0
            BEGIN
                RAISERROR('Error: Supplied Favorites not found !', 16, 1);
                RETURN -1;		-- Return a non-zero value to indicate error
            END
            ELSE
            BEGIN
                UPDATE  dbo.[User_Favourites]
                SET [Date_Removed] = GETDATE()
                WHERE   [Country_ID]= @Country_ID
                    and [User_ID]	= @User_ID
                    and [Vendor]	= @Vendor
                    and [Item_Key]	= @Item_Key;
                
                INSERT INTO dbo.[User_Favourites_Hist]
                SELECT * 
                FROM dbo.[User_Favourites]
                WHERE   [Country_ID]= @Country_ID
                    and [User_ID]	= @User_ID
                    and [Vendor]	= @Vendor
                    and [Item_Key]	= @Item_Key;
        
                DELETE dbo.[User_Favourites]
                WHERE   [Country_ID]= @Country_ID
                    and [User_ID]	= @User_ID
                    and [Vendor]	= @Vendor
                    and [Item_Key]	= @Item_Key;
        
                print 'Favorite Removed! '
                PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
                RETURN 0; --sucess
            END
        
        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Remove_Favourite");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Remove_Favourite");
    }
};
