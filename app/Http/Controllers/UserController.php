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

    public function login(Request $request) {
        $params = $request->all();

        $Email_ID = "";
        $Paswd = "";

        if(sizeof($params) > 0) {
            foreach ($params as $key => $value) {
                switch($key) {
                    case "Email_ID":
                        $Email_ID = $params["Email_ID"];
                    break;

                    case "Paswd":
                        $Paswd = $params["Paswd"];
                    break;
                }
            }
        }

        try {
            
            $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Authentication] @Email_ID='". $Email_ID ."',@Paswd='".$Paswd."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function changePassword(Request $request) {
        $params = $request->all();

        $Email_ID = "";
        $Paswd = "";

        if(sizeof($params) > 0) {
            foreach ($params as $key => $value) {
                switch($key) {
                    case "Email_ID":
                        $Email_ID = $params["Email_ID"];
                    break;

                    case "Paswd":
                        $Paswd = $params["Paswd"];
                    break;
                }
            }
        }

        try {
            
            $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Password] @Email_ID='". $Email_ID ."',@Paswd='".$Paswd."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function deleteAccount(Request $request) {
        $params = $request->all();

        $Email_ID = "";

        if(sizeof($params) > 0) {
            foreach ($params as $key => $value) {
                switch($key) {
                    case "Email_ID":
                        $Email_ID = $params["Email_ID"];
                    break;
                }
            }
        }

        try {
            
            $arrData = DB::statement("EXEC [dbo].[sp_proc_User_UnRegister] @Email_ID='". $Email_ID ."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
        
    }

    public function updateProfile(Request $request) {
        $params = $request->all();

        $Name = "";
        $Mobile = "";
        $Email_ID	= '';
        $Gender		= '';
        $City		= '';
        $Area		= '';
        $Nationality	= '';
        $DOB			= Null;
        $YOB			= Null;
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

                        
                        case "User_ID_Google":
                            $User_ID_Google = $params["User_ID_Google"];
                        break;

                        case "User_ID_Apple":
                            $User_ID_Apple = $params["User_ID_Apple"];
                        break;
                    }
                }
            }

            $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Registration] @Name='".$Name."', @Mobile='".$Mobile."',@Email_ID='". $Email_ID ."',@Gender='". $Gender ."',@City='". $City ."',@Area='". $Area ."',@Nationality='". $Nationality ."',@DOB='".$DOB."',@YOB=".$YOB.",@User_ID_Google='".$User_ID_Google."',@User_ID_Apple='".$User_ID_Apple."'");
            if(sizeof($errors) > 0 ) {
                return response()->json(['data' => $errors, 'status' => 400, "success" => false]);
            } else {

                return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            }
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function addToFavourites(Request $request) {
        $params = $request->all();

        $Country_ID = "";
        $User_ID = "";
        $Vendor	= '';
        $Item_Key		= '';
        $Price		= '';

        $errors = [];

        try {
            if(sizeof($params) > 0) {
                foreach ($params as $key => $value) {
                    switch($key) {
                        case "Name";
                            $Country_ID = $params['Country_ID'];
                            if($Country_ID == "") {
                                $errors = $errors->push((object)['Country_ID' => "Country_ID can not be empty"]);
                            }
                        break;

                        case "User_ID";
                            $User_ID = $params['User_ID'];
                            if($User_ID == "") {
                                $errors = $errors->push((object)['User_ID' => "User_ID can not be empty"]);
                            }
                        break;

                        case "Vendor";
                            $Vendor = $params['Vendor'];
                            if($Vendor == "") {
                                $errors = $errors->push((object)['Vendor' => "Vendor can not be empty"]);
                            }
                        break;

                        case "Item_Key";
                            $Item_Key = $params['Item_Key'];
                            if($Item_Key == "") {
                                $errors = $errors->push((object)['Item_Key' => "Item_Key can not be empty"]);
                            }
                        break;

                        case "Price";
                            $Price = $params['Price'];
                            if($Price == "") {
                                $errors = $errors->push((object)['Price' => "Price can not be empty"]);
                            }
                        break;
                    }
                }
            }

            $arrData = DB::statement("EXEC [dbo].[sp_proc_Add_Favourites] @Country_ID='".$Country_ID."', @User_ID='".$User_ID."',@Vendor='". $Vendor ."',@Item_Key='". $Item_Key ."',@Price='". $Price."'");
            if(sizeof($errors) > 0 ) {
                return response()->json(['data' => $errors, 'status' => 400, "success" => false]);
            } else {

                return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            }
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function getFavourites(Request $request) {
        $Country_ID = "";
        $User_ID = "";

        $errors = [];

        try {
            if(sizeof($params) > 0) {
                foreach ($params as $key => $value) {
                    switch($key) {
                        case "Name";
                            $Country_ID = $params['Country_ID'];
                            if($Country_ID == "") {
                                $errors = $errors->push((object)['Country_ID' => "Country_ID can not be empty"]);
                            }
                        break;

                        case "User_ID";
                            $User_ID = $params['User_ID'];
                            if($User_ID == "") {
                                $errors = $errors->push((object)['User_ID' => "User_ID can not be empty"]);
                            }
                        break;
                    }
                }
            }

            $arrData = DB::statement("EXEC [dbo].[sp_proc_Get_Favourites] @Country_ID='".$Country_ID."', @User_ID='".$User_ID."'");
            if(sizeof($errors) > 0 ) {
                return response()->json(['data' => $errors, 'status' => 400, "success" => false]);
            } else {

                return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            }
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function removeFavourites(Request $request) {
        $params = $request->all();

        $Country_ID = "";
        $User_ID = "";
        $Vendor	= '';
        $Item_Key		= '';

        $errors = [];

        try {
            if(sizeof($params) > 0) {
                foreach ($params as $key => $value) {
                    switch($key) {
                        case "Name";
                            $Country_ID = $params['Country_ID'];
                            if($Country_ID == "") {
                                $errors = $errors->push((object)['Country_ID' => "Country_ID can not be empty"]);
                            }
                        break;

                        case "User_ID";
                            $User_ID = $params['User_ID'];
                            if($User_ID == "") {
                                $errors = $errors->push((object)['User_ID' => "User_ID can not be empty"]);
                            }
                        break;

                        case "Vendor";
                            $Vendor = $params['Vendor'];
                            if($Vendor == "") {
                                $errors = $errors->push((object)['Vendor' => "Vendor can not be empty"]);
                            }
                        break;

                        case "Item_Key";
                            $Item_Key = $params['Item_Key'];
                            if($Item_Key == "") {
                                $errors = $errors->push((object)['Item_Key' => "Item_Key can not be empty"]);
                            }
                        break;
                    }
                }
            }

            $arrData = DB::statement("EXEC [dbo].[sp_proc_Remove_Favourite] @Country_ID='".$Country_ID."', @User_ID='".$User_ID."',@Vendor='". $Vendor ."',@Item_Key='". $Item_Key ."'");
            if(sizeof($errors) > 0 ) {
                return response()->json(['data' => $errors, 'status' => 400, "success" => false]);
            } else {

                return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            }
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }
}