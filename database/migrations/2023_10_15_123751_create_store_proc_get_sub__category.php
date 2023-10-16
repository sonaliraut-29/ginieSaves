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
        $procedure ="CREATE   PROCEDURE [dbo].[sp_proc_Get_Sub_Catagory]
               @Category	nvarchar(100) = '*'  --Pass a single category or '*' for all category
        AS
        BEGIN
            SET NOCOUNT ON;
            DECLARE @Time_Start	datetime = getdate()
        
            SELECT Category, Sub_Category
            FROM Category
            WHERE Sub_Category IS NOT NULL
                AND Category = (CASE WHEN @Category = '*' then Category ELSE @Category END)
            ORDER BY 1, 2
        
            PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
            RETURN 0; --success
        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Sub_Catagory");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_Get_Sub_Catagory");
    }
};
