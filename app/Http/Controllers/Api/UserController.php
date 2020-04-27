<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Upload;
use Storage;
class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registeration Function
    |--------------------------------------------------------------------------
    */
    public function register(Request $request){
        // Make validation before add
        $validator = Validator::make($request->all(),[
                    'image' => 'image|mimes:jpg,jpeg,png,gif,bmp,svg,jfif',
                    'name' => 'required|string|max:255',
                    'info' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8'
                ]);

        // Check if validator has errors
        if($validator->fails()){
            // If has errors return it
            return $validator->errors();
        }else{
            // Chek if user upload image
            if(request()->hasFile('image')){
                // Use Upload train to upload images
                $image = Upload::upload([
                    'new_name'      => '',
                    'old_image'      => '',
                    'file'          => 'image',
                    'path'          => 'users',
                    'upload_type'   => 'single',
                ]);
            }
            // Store data in database
            $data = User::create([
                'name'      => $request->name,
                'image'      => $image,
                'info'      => $request->info,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'api_token' => Str::random(60),
            ]);
            // return user data
            return $data;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Login Function
    |--------------------------------------------------------------------------
    */
    public function login(Request $request){
        if(auth()->attempt(['email' => $request->email, 'password' => $request->password]))
        {
            $user = auth()->user();
            $user->api_token = Str::random(60);
            $user->save();
            return $user;
        }
        else {
            return "error in login data";
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Logout Function
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request){
        if(auth()->user())
        {
            $user = auth()->user();
            $user->api_token = null;
            $user->save();
            return response()->json(['message' => 'Logout success']);
        }
        else {
            return response()->json([
                'error' => 'Unable to logout',
                'code' => 401,
            ], 401);
        }

    }



    /*
    |--------------------------------------------------------------------------
    | Update Function
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        // Make validation before update
        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,gif,bmp,svg,jfif',
            'name' => 'required|string|max:255',
            'info' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$request->id,
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            // If has errors return it
            return $validator->errors();
        }else{
            $data = $request->only(['name', 'email', 'password', 'info', 'image']);
            $user = User::find($request->id);

            if(request()->hasFile('image')){
                $image = Upload::upload([
                    'new_name'      => '',
                    'old_image'      => $user->image,
                    'file'          => 'image',
                    'path'          => 'users',
                    'upload_type'   => 'single',
                ]);
             }

             $user->name = $request->name;
             $user->info = $request->info;
             $user->email = $request->email;
             $user->image = $image;
             $user->password = Hash::make($request->password);
             $user->save();

             return response()->json(['message' => 'Updated successfuly']);
        }
    }
}
