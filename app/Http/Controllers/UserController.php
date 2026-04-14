<?php

namespace App\Http\Controllers;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $datauser = User::all();
        return view('user', compact('datauser'));
    } 

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'isadmin' => ['required'],
            'ispenyiar' => ['required'],
            'isgatekeeper' => ['required'],
            'ispjProgram' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required'],
        ]);

        User::create([
            'name' => $request->name,
            'role' => $request->role,
            'email' => $request->email,
            'is_admin' => $request->isadmin,
            'is_dokter' => $request->isdokter,
            'is_penyiar' => $request->ispenyiar,
            'is_gatekeeper' => $request->isgatekeeper,
            'is_pjprogram' => $request->ispjProgram,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/akun')->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'isadmin' => ['required'],
            'ispenyiar' => ['required'],
            'isgatekeeper' => ['required'],
            'ispjProgram' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        if($request->password != ''){
            $akun->password = Hash::make($request->password);
        }
        $akun->email = $request->email;
        $akun->is_admin = $request->isadmin;
        $akun->is_penyiar = $request->ispenyiar;
        $akun->is_gatekeeper = $request->isgatekeeper;
        $akun->is_pjprogram = $request->ispjProgram;
        $akun->save();

        return redirect('/akun')->with('success', 'Data berhasil diubah');
    }

    // public function edit($id)
    // {
    //     $user = User::find($id);
    //     return view('edit-akun', [
    //         'user' => $user
    //     ]);
    // }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect('/akun')->with('success', 'Data berhasil dihapus');
    }

    // public function tambahakun()
    // {
    //     return view('tambah-akun-form');
    // }

    public function logut(Request $request)
    {
        Auth::logout();
 
        request()->session()->invalidate();
 
        request()->session()->regenerateToken();
 
        return redirect('/');
    }
}
