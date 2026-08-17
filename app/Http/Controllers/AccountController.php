<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    private const ROLES = ['staf', 'lurah', 'admin'];

    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        return view('akun', [
            'akun' => User::orderBy('name')->paginate(15)->withQueryString(),
            'roles' => self::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:' . implode(',', self::ROLES),
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        AuditLog::log('DATA.MUTATION', "Menambahkan akun {$user->email} dengan role {$user->role}.");

        return back()->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, User $akun)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $akun->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:' . implode(',', self::ROLES),
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $akun->update($data);

        AuditLog::log('DATA.MUTATION', "Memperbarui akun {$akun->email}.");

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $akun)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);
        abort_if($akun->id === auth()->id(), 403, 'Tidak bisa menghapus akun sendiri.');

        AuditLog::log('DATA.MUTATION', "Menghapus akun {$akun->email}.");
        $akun->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
