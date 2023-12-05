<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use DB,Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'create','logout', 'getCities','getGovernate','getCountries','forgotPassword','socialLogin','callback']]);
    }

    public function create(Request $request) {
        $params = $request->all();

        $Login_Type = "EM";
        
        $User_ID_Google	= Null;
        $User_ID_Apple	= Null;

        $rules =[
            'email' => 'required|string',
            'password' => 'required|string',
            'Name' => 'required|string',
            // 'Gender' => 'required|string',
            // 'City' => 'required|string',
            // 'DOB' => 'required',
            // 'YOB' => 'required',
            // 'Area' => 'required|string',
            'Mobile' => 'required',
            'Nationality'=> 'required|string'
        ];

        $messages = ["Nationality.required" => "Nationality is required."];
        $this->validate($request, $rules, $messages);

        
        try {
            $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Registration] @Name='".$request->Name."', @Mobile='".$request->Mobile."',@email='". $request->email ."',@Gender='". $request->Gender ."',@City='". $request->City ."',@Area='". $request->Area ."',@Nationality='". $request->Nationality ."',@DOB='".$request->DOB."',@YOB='".$request->YOB."',@password='".Hash::make($request->password)."',@User_ID_Google='".$User_ID_Google."',@User_ID_Apple='".$User_ID_Apple."'");
            $userId = User::max("User_ID");
            $user = User::where("User_ID", $userId)->first();
            
            $credentials = $request->only(['email', 'password']);

            if (! $token = Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            
            $res = [
                "inserted" => $arrData,
                "userId" => $userId,
                "user" => $user,
                'access_token' => $token,
                'token' => $token
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
           
            $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Password] @email='". $request->email ."',@password='".Hash::make($request->password)."'");
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


        $User_ID_Google	= Null;
        $User_ID_Apple	= Null;

        $this->validate($request, [
            'email' => 'required|string',
            'Name' => 'required|string',
            // 'Gender' => 'required|string',
            // 'City' => 'required|string',
            // 'DOB' => 'required',
            // 'YOB' => 'required',
            // 'Ares' => 'required|string',
            'Mobile' => 'required',
            'Nationality'=> 'required|string'
        ],['Nationality.required'=> "Nationality is required."]);

        try {
            
            $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Prof_Update] @Name='".$request->Name."', @Mobile='".$request->Mobile."',@email='". $request->email."',@Gender='". $request->Gender ."',@City='". $request->City ."',@Area='". $request->Area ."',@Nationality='". $request->Nationality ."',@DOB='".$request->DOB."',@YOB=".$request->YOB.",@User_ID_Google='".$User_ID_Google."',@User_ID_Apple='".$User_ID_Apple."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function addToFavourites(Request $request) {
        $params = $request->all();
        $this->validate($request, [
            'Country_ID' => 'required',
            'User_ID' => 'required',
            'Vendor' => 'required',
            'Item_Key' => 'required',
            'Price' => 'required',
            'Item_name' => 'required',
            'Item_Image_URL' => 'required',
            'Item_URL' => 'required'
        ]);

        try {
            
            $arrData = DB::statement("EXEC [dbo].[sp_proc_Add_Favourites] @Country_ID=".$request->Country_ID.", @User_ID=".$request->User_ID.",@Vendor='". $request->Vendor ."',@Item_Key=". $request->Item_Key .",@Price=". $request->Price.", @Item_name='".$request->Item_name."', @Item_Image_URL=\"".$request->Item_Image_URL."\", @Item_URL='".$request->Item_URL."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function getFavourites(Request $request,$Country_ID, $User_ID) {
        
        try {
            
            $exec = "EXEC [dbo].[sp_proc_Get_Favourites] @Country_ID=".$Country_ID.", @User_ID=".$User_ID.",@Start_offset=".$request->Start_offset.",@num_of_rows_required=".$request->num_of_rows_required;
            $pdo = \DB::connection()->getPdo();
            $sql = $exec;
            $stmt = $pdo->query($sql);
            $stmt->execute();
            $rowset1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);
           
            $stmt->nextRowset();
            $rowset2 = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if(sizeof($rowset2) > 0) {
                $rowset2 = $rowset2[0]["Num_Of_Rows"];
            }

            return response()->json(['data' => $rowset1, "totalCount" => $rowset2, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function removeFavourites(Request $request) {
        $params = $request->all();

        $this->validate($request, [
            'Country_ID' => 'required',
            'User_ID' => 'required',
            'Vendor' => 'required|string',
            'Item_Key' => 'required',
        ]);

        try {
            
            $arrData = DB::statement("EXEC [dbo].[sp_proc_Remove_Favourite] @Country_ID=".$request->Country_ID.", @User_ID=".$request->User_ID.",@Vendor='". $request->Vendor ."',@Item_Key=". $request->Item_Key);

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
            'token' => $token,
            'token_type'   => 'bearer',
            'user'         => auth()->user(),
            'expires_in'   => auth()->factory()->getTTL() * 60 * 24
        ]);
    }

    public function getCities(Request $request, $gov) {
        try {
            $string = str_replace("%20", " ", $gov);
            
            $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Cities] @Gov='".$string."'");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function getGovernate(Request $request) {
        try {
            $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Gov]");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function logout() {
        try {
            auth()->logout();

            return response()->json(['message' => 'Successfully logged out','status' => 200, "success" => true]);
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function refresh(){
        return $this->jsonResponse(auth()->refresh());
    }

    public function getCountries(Request $request) {
        try {
            $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Countries]");
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function forgotPassword(Request $request) {
        $this->validate($request, [
            'email' => 'required|string|email',
        ]);
        try {
            $user = DB::select("Select * from Users where email='".$request->email."'");
            
            if($user && sizeof($user) > 0) {
                $password = "Test1234";
                $data = [
                    "user" => $user[0],
                    "password" => $password
                ];

                Mail::send('email', $data, function($message) use ($data)
                {
                    $message->to($data["user"]->email)->subject('Regrading Forgot Password!');
                });
                DB::table('Users')->where('email', $request->email)->update(['password' => Hash::make($password)]); 

                return response()->json(['data' => ["message"=>"Email sent successfully."], 'status' => 200, "success" => true]);
            } else {
                return response()->json(['message' =>"Email does not exist." , 'status' => 400, "success" => false]);
            }
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function socialLogin(Request $request, $provider) {
        
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback(Request $request, $provider) {
        $socialite = Socialite::driver($provider)->stateless()->user();

        $user_by_email = User::where('email', $socialite->email)->first();

        if ($user_by_email) {
            $user = $user_by_email;
            $token = Auth::login($user);
            
        } 
        else {

            if("google" == $provider) {
                $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Registration] @Name='".$socialite->getName()."', @Mobile='',@email='". $socialite->getEmail() ."',@Gender='',@City='',@Area='',@Nationality='',@DOB='',@YOB='',@password='',@User_ID_Google='".$socialite->getId()."',@User_ID_Apple=''");
            }

            if("apple" == $provider) {
                $arrData = DB::statement("EXEC [dbo].[sp_proc_User_Registration] @Name='".$socialite->getName()."', @Mobile='',@email='". $socialite->getEmail() ."',@Gender='',@City='',@Area='',@Nationality='',@DOB='',@YOB='',@password='',@User_ID_Google='',@User_ID_Apple='".$socialite->getId()."'");
            }
            $user_by_email = User::where('email', $socialite->email)->first();
            $token = Auth::login($user_by_email);
            $user = $user_by_email;
        }
        

        return response()->json([
            'access_token' => $token,
            'token' => $token,
            'token_type'   => 'bearer',
            'user'         => auth()->user(),
            'expires_in'   => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}