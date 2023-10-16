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
        CREATE   PROCEDURE [dbo].[sp_proc_Get_Banners_N_Filters]
            @days_tolerance int		= -10
            ,@Start_offset int			=   0
            ,@num_of_rows_required int	=  10
            ,@Vendor nvarchar(50)		= '*'	--*: All Vendors
            ,@Category nvarchar(max)	= '*'	--*: All Categories
        AS
        BEGIN

            --Declare  @days_tolerance int = -100
            --		,@Start_offset	int = 0
            --		,@num_of_rows_required int = 100
            --		,@Vendor nvarchar(50) = '*'		--*: All Vendors
            --		,@Category nvarchar(max) = '*' 	--*: All Categories	--'Grocery'
            
            declare @Time_Start	datetime = getdate()

            select main.* 
            from [dbo].[Banners_Combined] as main
            where data_asof >= dateadd(d, @days_tolerance, getdate())	-- parametered
                and Vendor   in (iif(@Vendor   = '*', Vendor,   @Vendor))
                and Vendor in (select distinct Vendor from [dbo].Menus_Working where Category = iif(@Category = '*', Category, @Category))
            order by Vendor
            OFFSET @Start_offset ROWS FETCH NEXT @num_of_rows_required ROWS ONLY

            PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'

            Return 0;

        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Banners_N_Filters");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Banners_N_Filters");
    }
};
