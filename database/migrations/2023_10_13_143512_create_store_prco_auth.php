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
        $procedure ="CREATE   PROCEDURE [dbo].[sp_proc_User_Authentication]
               @Email_ID	nvarchar(100)	
              ,@Paswd nvarchar(100)	
        AS
        BEGIN
            SET NOCOUNT ON;
        
            if (select count(1) from dbo.users where email_id = @Email_ID and Paswd = @Paswd	) = 0
            BEGIN
                RAISERROR('Error: Supplied @EmailID and @PASWD combination does not exists.', 16, 1);
                if (select count(1) from dbo.users where email_id = @Email_ID ) = 1
                    UPDATE dbo.Users
                    SET [Last_Login_Failure] = GETDATE()
                    WHERE email_id = @Email_ID;
                RETURN -1;		-- Return a non-zero value to indicate error
            END
            ELSE
            BEGIN
                UPDATE dbo.Users
                SET [Last_Login_Success] = GETDATE()
                WHERE email_id = @Email_ID;
        
                print @Email_ID + ' Authenticated'
                RETURN 0; --sucess
            END
        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_User_Authentication");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_User_Authentication");
    }
};
