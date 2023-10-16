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
        $procedure ="CREATE PROCEDURE [dbo].[sp_proc_User_Prof_Update]
               @Login_Type	char(2) = 'EM'
              ,@Name		nvarchar(100)
              ,@Mobile		nvarchar(20)
              ,@Email_ID	nvarchar(100)	--Email cannot be changed
              ,@Gender		char(1)
              ,@City		nvarchar(50)
              ,@Area		nvarchar(50)
              ,@Nationality	nvarchar(50)
              ,@DOB			date
              ,@YOB			int
              --,@Paswd		nvarchar(50)	--to change password, use a different procedure
              --,@Date_Reg	date
              ,@User_ID_Google	nvarchar(100)
              ,@User_ID_Apple	nvarchar(100)
        AS
        BEGIN
            SET NOCOUNT ON;
        
            DECLARE @User_ID int = 0;
            DECLARE @Return_Value varchar(100) = 'Success';
        
            if (select count(1) from dbo.users where email_id = @Email_ID) = 0
                set @Return_Value = 'Error: EmailID Does not exists'
            else
            BEGIN
        
                UPDATE DBO.Users
                SET 
                       [Login_Type]		= @Login_Type	
                      ,[Name]			= @Name		
                      ,[Mobile]			= @Mobile		
                      --,@Email_ID	--Email cannot be changed
                      ,[Gender]			= @Gender		
                      ,[City]			= @City		
                      ,[Area]			= @Area		
                      ,[Nationality]	= @Nationality	
                      ,[DOB]			= @DOB			
                      ,[YOB]			= @YOB			
                      ,[User_ID_Google] = @User_ID_Google	
                      ,[User_ID_Apple]	= @User_ID_Apple	
                WHERE [Email_ID] = @Email_ID
                ;
            END
            
            print @Email_ID + ', ' + @Return_Value
            select @Email_ID, @Return_Value
        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_User_Prof_Update");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP procedure IF EXISTS sp_proc_User_Prof_Update");
    }
};
