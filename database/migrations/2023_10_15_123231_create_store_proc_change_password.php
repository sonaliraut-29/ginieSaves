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
        $procedure ="CREATE   PROCEDURE [dbo].[sp_proc_User_Password]
               @Email_ID	nvarchar(100)	
              ,@Paswd nvarchar(100)	
        AS
        BEGIN
            SET NOCOUNT ON;
            DECLARE @Time_Start	datetime = getdate()
        
            if (select count(1) from dbo.users where email_id = @Email_ID) = 0
            BEGIN
                RAISERROR('Error: Supplied @EmailID does not exists.', 16, 1);
                RETURN -1;		-- Return a non-zero value to indicate error
            END
            ELSE
            BEGIN
                UPDATE dbo.Users
                SET [Paswd] = @Paswd
                WHERE email_id = @Email_ID;
        
                print @Email_ID + ' Password Changed'
                PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
                RETURN 0; --sucess
            END
        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_User_Password");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_User_Password");
    }
};
