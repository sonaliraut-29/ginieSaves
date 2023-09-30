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
        CREATE PROCEDURE [dbo].[sp_proc_get_Popular_Items] 
            @num_of_rows_required int = 10
        AS
        BEGIN

            --Declare @num_of_rows_required int = 10

            Select --top 1000 
                Vendor, Item_name,	categ,	Brand,	Item_Category, Item_Key,	Item_Key2,		
                Regular_Price,	Discounted_Price as Selling_Price,	Discount_Percent,	Discount_Start_Date,	Discount_End_Date,	
                is_Item_Available,	Unit_Type,	Unit,	Item_Image_URL,	Item_URL, Data_Asof 
            from [Items_Combined] 
            WHERE is_Item_Available = 1
            ORDER BY NEWID()
            OFFSET 0 ROWS FETCH NEXT @num_of_rows_required ROWS ONLY

        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_get_Popular_Items");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_get_Popular_Items");
    }
};
