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
        CREATE PROCEDURE [dbo].[sp_proc_User_Registration] 
               @Login_Type	char(2) = 'EM'
              ,@Name		nvarchar(100)
              ,@Mobile		nvarchar(20)
              ,@Email_ID	nvarchar(100)
              ,@Gender		char(1)
              ,@City		nvarchar(50)
              ,@Area		nvarchar(50)
              ,@Nationality	nvarchar(50)
              ,@DOB			date
              ,@YOB			int
              ,@Paswd		nvarchar(50)
              --,@Date_Reg	date
              ,@User_ID_Google	nvarchar(100)
              ,@User_ID_Apple	nvarchar(100)
        AS
        BEGIN
            SET NOCOUNT ON;
        
            DECLARE @User_ID int = 0;
            DECLARE @Return_Value varchar(100) = 'Success';
        
            if (select count(1) from dbo.users where email_id = @Email_ID) > 0
            BEGIN
                --set @Return_Value = 'Error: EmailID already registered'
                RAISERROR('Error: @Email_ID already registered', 16, 1);
                RETURN -1; -- Return a non-zero value to indicate error
            END
            else
            BEGIN
                SET @User_ID = isnull((select max([User_ID]) from dbo.Users), 0) + 1
                INSERT INTO dbo.Users 
                    (  [Country_ID]
                      ,[User_ID]
                      ,[Login_Type]
                      ,[Name]
                      ,[Mobile]
                      ,[Email_ID]
                      ,[Is_Email_Verified]
                      ,[Is_Mobile_Verified]
                      ,[Gender]
                      ,[City]
                      ,[Area]
                      ,[Nationality]
                      ,[Status]
                      ,[DOB]
                      ,[YOB]
                      ,[Paswd]
                      ,[Date_Reg]
                      ,[User_ID_Google]
                      ,[User_ID_Apple]
                      --,[Last_Device]
                      --,[Last_IP]
                      )
                VALUES (
                       1			--[Country_ID]
                      ,@User_ID		--[User_ID]
                      ,@Login_Type
                      ,@Name
                      ,@Mobile
                      ,@Email_ID
                      ,0			--Is_Email_Verified
                      ,0			--Is_Mobile_Verified
                      ,@Gender
                      ,@City
                      ,@Area
                      ,@Nationality
                      ,1			--Status
                      ,@DOB
                      ,@YOB
                      ,@Paswd
                      ,getdate()	--@Date_Reg
                      ,@User_ID_Google
                      ,@User_ID_Apple
                );  
            END
            
            print 'User ID: ' + STR(@User_ID) + ', ' + @Return_Value
            RETURN @User_ID
        END";
        DB::unprepared("DROP procedure IF EXISTS sp_proc_User_Registration");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sp_proc_User_Registration');
    }
};
