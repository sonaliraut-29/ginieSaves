<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use DB,Exception;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(Request $request) {
        $params = $request->all();

        $Login_Type = "EM";
        $Name = "";
        $Mobile = "";
        $Email_ID	= '';
        $Gender		= '';
        $City		= '';
        $Area		= '';
        $Nationality	= '';
        $DOB			= Null;
        $YOB			= Null;
        $Paswd		= '';
        $User_ID_Google	= Null;
        $User_ID_Apple	= Null;

        $errors = [];

        try {
            if(sizeof($params) > 0) {
                foreach ($params as $key => $value) {
                    switch($key) {
                        case "Name";
                            $Name = $params['Name'];
                            if($Name == "") {
                                $errors = $errors->push((object)['Name' => "Name can not be empty"]);
                            }
                        break;

                        case "Mobile";
                            $Mobile = $params['Mobile'];
                            if($Mobile == "") {
                                $errors = $errors->push((object)['Mobile' => "Mobile can not be empty"]);
                            }
                        break;

                        case "Email_ID";
                            $Email_ID = $params['Email_ID'];
                            if($Email_ID == "") {
                                $errors = $errors->push((object)['Email_ID' => "Email_ID can not be empty"]);
                            }
                        break;

                        case "Gender";
                            $Gender = $params['Gender'];
                            if($Gender == "") {
                                $errors = $errors->push((object)['Gender' => "Gender can not be empty"]);
                            }
                        break;

                        case "City";
                            $City = $params['City'];
                            if($City == "") {
                                $errors = $errors->push((object)['City' => "City can not be empty"]);
                            }
                        break;

                        case "Area";
                            $Area = $params['Area'];
                            if($Area == "") {
                                $errors = $errors->push((object)['Area' => "Area can not be empty"]);
                            }
                        break;

                        case "Nationality";
                            $Nationality = $params['Nationality'];
                            if($Nationality == "") {
                                $errors = $errors->push((object)['Nationality' => "Nationality can not be empty"]);
                            }
                        break;

                        case "DOB";
                            $DOB =  $params['DOB'];
                            if($DOB == "") {
                                $errors = $errors->push((object)['DOB' => "DOB can not be empty"]);
                            }
                        break;

                        case "YOB";
                            $YOB = (int) $params['YOB'];
                            if($YOB == "") {
                                $errors = $errors->push((object)['YOB' => "YOB can not be empty"]);
                            }
                        break;

                        case "Paswd";
                            $Paswd = $params['Paswd'];
                            if($Paswd == "") {
                                $errors = $errors->push((object)['Paswd' => "Paswd can not be empty"]);
                            }
                        break;
                        
                        case "User_ID_Google":
                            $User_ID_Google = $params["User_ID_Google"];
                        break;

                        case "User_ID_Apple":
                            $User_ID_Apple = $params["User_ID_Apple"];
                        break;
                    }
                }
            }
            $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Registration] @Name='".$Name."', @Mobile='".$Mobile."',@Email_ID='". $Email_ID ."',@Gender='". $Gender ."',@City='". $City ."',@Area='". $Area ."',@Nationality='". $Nationality ."',@DOB='".$DOB."',@YOB=".$YOB.",@Paswd='".$Paswd."',@User_ID_Google='".$User_ID_Google."',@User_ID_Apple='".$User_ID_Apple."'");
            $userId = User::max("User_ID");

            $res = [
                "inserted" => $arrData,
                "userId" => $userId
            ];
            return response()->json(['data' => $res, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }   
    }
}
