<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use DB,Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'create','logout']]);
    }

    public function create(Request $request) {
        $params = $request->all();

        $Login_Type = "EM";
        
        $User_ID_Google	= Null;
        $User_ID_Apple	= Null;

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
            'Name' => 'required|string',
            'Gender' => 'required|string',
            'City' => 'required|string',
            'DOB' => 'required',
            'YOB' => 'required',
            'Area' => 'required|string',
            'Mobile' => 'required',
            'Nationality'=> 'required|string'
        ]);

        
        try {
            $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Registration] @Name='".$request->Name."', @Mobile='".$request->Mobile."',@email='". $request->email ."',@Gender='". $request->Gender ."',@City='". $request->City ."',@Area='". $request->Area ."',@Nationality='". $request->Nationality ."',@DOB='".$request->DOB."',@YOB=".$request->YOB.",@password='".Hash::make($request->password)."',@User_ID_Google='".$User_ID_Google."',@User_ID_Apple='".$User_ID_Apple."'");
            $userId = User::max("User_ID");
            $user = User::where("User_ID", $userId)->first();
            
            $res = [
                "inserted" => $arrData,
                "userId" => $userId,
                "user" => $user
            ];
            return response()->json(['data' => $res, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }   
    }

    public function login(Request $request) {
        $params = $request->all();

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Authentication] @email='". $request->email ."',@password='".$Paswd."'");
            $credentials = $request->only(['email', 'password']);

            if (! $token = Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            return $this->jsonResponse($token);
        
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function changePassword(Request $request) {
        $params = $request->all();

        $Email_ID = "";
        $Paswd = "";
        
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
           
            $arrData = DB::select("EXEC [dbo].[sp_proc_User_Password] @email='". $request->email ."',@password='".Hash::make($request->password)."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function deleteAccount(Request $request) {
        $params = $request->all();

        $this->validate($request, [
            'email' => 'required|string|email',
        ]);

        try {
            
            $arrData = DB::select("EXEC [dbo].[sp_proc_User_UnRegister] @email='". $email ."'");
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

        $this->validate($request, [
            'email' => 'required|string',
            'Name' => 'required|string',
            'Gender' => 'required|string',
            'City' => 'required|string',
            'DOB' => 'required',
            'YOB' => 'required',
            'Ares' => 'required|string',
            'Mobile' => 'required|number',
            'Nationality'=> 'required|string'
        ]);

        try {
            
            $arrData = DB::select("EXEC [dbo].[sp_proc_User_Prof_Update] @Name='".$request->Name."', @Mobile='".$request->Mobile."',@email='". $request->email."',@Gender='". $request->Gender ."',@City='". $request->City ."',@Area='". $request->Area ."',@Nationality='". $request->Nationality ."',@DOB='".$request->DOB."',@YOB=".$request->YOB.",@User_ID_Google='".$User_ID_Google."',@User_ID_Apple='".$User_ID_Apple."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function addToFavourites(Request $request) {
        $params = $request->all();
        $this->validate($request, [
            'Country_ID' => 'required|string',
            'User_ID' => 'required|string',
            'Vendor' => 'required|string',
            'Item_Key' => 'required|string',
            'Price' => 'required|string',
        ]);

        try {
            
            $arrData = DB::select("EXEC [dbo].[sp_proc_Add_Favourites] @Country_ID='".$request->Country_ID."', @User_ID='".$request->User_ID."',@Vendor='". $request->Vendor ."',@Item_Key='". $request->Item_Key ."',@Price='". $request->Price."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function getFavourites(Request $request) {
        $this->validate($request, [
            'Country_ID' => 'required|string',
            'User_ID' => 'required|string',
        ]);
        try {
            
            $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Favourites] @Country_ID='".$request->Country_ID."', @User_ID='".$request->User_ID."'");

            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function removeFavourites(Request $request) {
        $params = $request->all();

        $this->validate($request, [
            'Country_ID' => 'required|string',
            'User_ID' => 'required|string',
            'Vendor' => 'required|string',
            'Item_Key' => 'required|string',
        ]);

        try {
            
            $arrData = DB::select("EXEC [dbo].[sp_proc_Remove_Favourite] @Country_ID='".$request->Country_ID."', @User_ID='".$request->User_ID."',@Vendor='". $request->Vendor ."',@Item_Key='". $request->Item_Key ."'");

            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function userDetails(Request $request) {
        
        $this->validate($request, [
            'email' => 'required|string|email',
        ]);


        try {
            $arrData = DB::select("EXEC [dbo].[sp_proc_User_Get_Details] @email='".$request->email."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    protected function jsonResponse($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'user'         => auth()->user(),
            'expires_in'   => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}