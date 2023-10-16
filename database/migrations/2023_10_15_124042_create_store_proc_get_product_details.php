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
        CREATE   PROCEDURE [dbo].[sp_proc_Get_Item_Details]
            @Vendor		nvarchar(100)	
            ,@Item_Key	bigint
        AS
        BEGIN
            SET NOCOUNT ON;
            DECLARE @Time_Start	datetime = getdate()

            Select top 1
                            Vendor,	--Page_Level1,	Page_Level2,	Page_Level3,	Page_Level4,	
                            Item_name,	categ,	Brand,	Item_Key,	Item_Key2,		--Item_Category,
                            Regular_Price,	Discounted_Price as Selling_Price,	Discount_Percent,	Discount_Start_Date,	Discount_End_Date,	
                            is_Item_Available,	Unit_Type,	Unit,	Item_Image_URL,	Item_URL, Data_Asof 
            from [Items_Combined] 
            where Vendor = @Vendor and Item_Key = @Item_Key

            PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
            RETURN 0; --success
        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Item_Details");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Item_Details");
    }
};
