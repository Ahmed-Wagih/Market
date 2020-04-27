<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Upload;
use Storage;
class UserController extends Controller
{
    public function edit($id)
    {
        $user = User::find($id);
        return view('auth.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'image|mimes:jpg,jpeg,png,gif,bmp,svg,jfif',
            'name' => 'required|string|max:255',
            'info' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id
        ]);

        if($request->password != null){
            $password = Hash::make($request->password);
        }else{
            $password = $request->old_password;
        }

        $data = $request->only(['name', 'email', 'password', 'info', 'image']);
        $user = User::find($id);

        if(request()->hasFile('image')){
            $image = Upload::upload([
                'new_name'      => '',
                'old_image'      => $user->image,
                'file'          => 'image',
                'path'          => 'users',
                'upload_type'   => 'single',
            ]);
         }else{
            $image = $request->old_image;
         }


         $user->name = $request->name;
         $user->info = $request->info;
         $user->email = $request->email;
         $user->image = $image;
         $user->password = $password;
         $user->save();

         session()->flash('success', 'Data Updated Succefuly');
         return redirect(route('user.edit', $id));
    }
}
