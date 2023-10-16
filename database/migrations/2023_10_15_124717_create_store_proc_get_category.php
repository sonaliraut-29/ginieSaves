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
        CREATE   PROCEDURE [dbo].[sp_proc_Get_Category]
        AS
        BEGIN
            SET NOCOUNT ON;
            DECLARE @Time_Start	datetime = getdate()

            select distinct Category
            from dbo.Category
            order by 1

            PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
            RETURN 0; --success
        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Category");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Category");
    }
};
