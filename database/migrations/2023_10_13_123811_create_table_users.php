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
        $createTableSqlString = "CREATE TABLE [dbo].[Users](
            [Country_ID] [smallint] NOT NULL,
            [User_ID] [int] NOT NULL,
            [Login_Type] [char](2) NULL,
            [Name] [nvarchar](100) NULL,
            [Mobile] [nvarchar](20) NULL,
            [Email_ID] [varchar](100) NULL,
            [Is_Email_Verified] bit null,
            [Is_Mobile_Verified] bit null,
            [Gender] [char](1) NULL,
            [City] [nvarchar](50) NULL,
            [Area] [nvarchar](50) NULL,
            [Nationality] [nvarchar](50) NULL,
            [Status] [smallint] NULL,
            [DOB] [date] NULL,
            [YOB] [int] NULL,
            [Paswd] [nvarchar](50) NULL,
            [Date_Reg] [smalldatetime] NULL,
            [Date_UnReg] [smalldatetime] NULL,
            [User_ID_Google] [varchar](100) NULL,
            [User_ID_Apple] [varchar](100) NULL,
            [Last_Device] [varchar](100) NULL,
            [Last_IP] [varchar](100) NULL,
            [Last_Paswd_Changed] [smalldatetime] NULL,
            [Last_Login_Success] [smalldatetime] NULL,
            [Last_Login_Failure] [smalldatetime] NULL,
         CONSTRAINT [PK_Users] PRIMARY KEY CLUSTERED 
        (	[Country_ID]  ASC,
            [User_ID] ASC
        )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
        ) ON [PRIMARY]";

        // DB::statement($createTableSqlString);
        // DB::statement('ALTER TABLE [dbo].[Users] ADD  CONSTRAINT [DF_Users_Country_ID]  DEFAULT ((1)) FOR [Country_ID]');
        // DB::statement('ALTER TABLE [dbo].[Users] ADD  CONSTRAINT [DF_Users_Status]  DEFAULT ((0)) FOR [Status]');
        // DB::statement('ALTER TABLE [dbo].[Users] ADD  CONSTRAINT [DF_Is_Email_Verified]  DEFAULT ((0)) FOR [Is_Email_Verified]');
        // DB::statement('ALTER TABLE [dbo].[Users] ADD  CONSTRAINT [DF_Users_Date_Reg]  DEFAULT (getdate()) FOR [Date_Reg]');
        // DB::statement("EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Country ID, User selects from a list' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'Users', @level2type=N'COLUMN',@level2name=N'Country_ID'");
        // DB::statement("EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'System Generated' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'Users', @level2type=N'COLUMN',@level2name=N'User_ID'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Users');
    }
};
