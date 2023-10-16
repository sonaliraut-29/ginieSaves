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
        CREATE PROCEDURE [dbo].[sp_proc_get_leaflets]
            @days_tolerance int = -10
            ,@num_of_rows_required int = 10
        AS
        BEGIN
            SET NOCOUNT ON;
            DECLARE @Time_Start datetime = getdate()
            --Declare @days_tolerance int = -10
            --	,@num_of_rows_required int = 10

            SELECT main.* 
            FROM [dbo].[Leaflets_Combined] as main
            WHERE data_asof >= dateadd(d, @days_tolerance, getdate())	-- parametered
            ORDER BY newid()
            OFFSET 0 ROWS FETCH NEXT @num_of_rows_required ROWS ONLY

            PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
            RETURN 0;

        END";
                
        DB::unprepared("DROP procedure IF EXISTS sp_proc_get_leaflets");
        DB::unprepared($procedure);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_get_leaflets");
    }
};
