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
        $procedure ="CREATE   PROCEDURE [dbo].[sp_proc_User_Get_Details]
              @Email_ID	nvarchar(100)	
        AS
        BEGIN
            SET NOCOUNT ON;
            declare @Time_Start	datetime = getdate()
        
            --DECLARE @User_ID int = 0;
            --DECLARE @Return_Value varchar(100) = 'Success';
        
            if (select count(1) from dbo.users where email_id = @Email_ID) = 0
            BEGIN
                --set @Return_Value = 'Error: EmailID Does not exists'
                RAISERROR('Error: Supplied @EmailID does not exists.', 16, 1);
                RETURN -1; -- Return a non-zero value to indicate error
            END
            else
            BEGIN
        
                SELECT top 1
                       [Email_ID]
                      ,[User_ID]
                      ,[Login_Type]
                      ,[Name]
                      ,[Mobile]
                      ,[Is_Email_Verified]
                      ,[Is_Mobile_Verified]
                      ,[Gender]
                      ,[City]
                      ,[Area]
                      ,[Nationality]
                      ,[Status]
                      ,[DOB]
                      ,[YOB]
                      --,[Paswd]
                      ,[Date_Reg]
                      ,[User_ID_Google]
                      ,[User_ID_Apple]
                      ,[Last_Device]
                      ,[Last_IP]
                      ,[Last_Login_Success]
                      ,[Last_Login_Failure]
                  FROM [dbo].[Users]
                WHERE [Email_ID] = @Email_ID
                ;
            END
            
            print @Email_ID 
            PRINT 'Total execution time taken: ' + ltrim(str(datediff(ms, @Time_Start, getdate()))) + ' ms'
            RETURN 0; --sucess
        
        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_User_Get_Details");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_User_Get_Details");
    }
};
