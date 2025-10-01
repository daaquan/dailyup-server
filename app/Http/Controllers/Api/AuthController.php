<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\SearchUsersRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->errorResponse(
                    ['email' => ['メールアドレスまたはパスワードが正しくありません']],
                    'ログインに失敗しました',
                    401
                );
            }

            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $token,
            ], 'ログイン成功');
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->errors(),
                '入力データに不備があります'
            );
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $token,
            ], 'ユーザー登録成功', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->errors(),
                '入力データに不備があります'
            );
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'ログアウト成功');
    }

    public function getUserInfo(Request $request)
    {
        $user = $request->user();

        return $this->successResponse([
            'user' => new UserResource($user),
        ], 'ユーザー情報を取得しました');
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $user->update($request->only(['name', 'email']));

        return $this->successResponse([
            'user' => new UserResource($user),
        ], 'プロフィールを更新しました');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->successResponse(null, 'パスワードを変更しました');
    }

    public function deleteAccount(DeleteAccountRequest $request)
    {
        $user = auth()->user();
        $user->delete();

        // トークンを削除してログアウト
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'アカウントを削除しました');
    }

    public function getUsers(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return $this->errorResponse(['role' => ['管理者権限が必要です']], 'アクセスが拒否されました', 403);
        }

        $users = User::paginate(10);

        return $this->successResponse([
            'users' => UserResource::collection($users),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ], 'ユーザー一覧を取得しました');
    }

    public function getUser($id)
    {
        if (!auth()->user()->isAdmin()) {
            return $this->errorResponse(['role' => ['管理者権限が必要です']], 'アクセスが拒否されました', 403);
        }

        $user = User::findOrFail($id);

        return $this->successResponse([
            'user' => new UserResource($user),
        ], 'ユーザー情報を取得しました');
    }

    public function searchUsers(SearchUsersRequest $request)
    {
        try {
            \Log::info('searchUsers called', ['q' => $request->q, 'user' => auth()->user(), 'url' => $request->fullUrl()]);
            if (!auth()->user()->isAdmin()) {
                \Log::warning('Non-admin user tried to access searchUsers', ['user' => auth()->user()]);
                return $this->errorResponse(['role' => ['管理者権限が必要です']], 'アクセスが拒否されました', 403);
            }

            $query = $request->q;
            \Log::info('Search query', ['query' => $query]);
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->paginate(10);

            \Log::info('Search results', ['count' => $users->count(), 'total' => $users->total()]);
            return $this->successResponse([
                'users' => UserResource::collection($users),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ], 'ユーザーを検索しました');
        } catch (\Exception $e) {
            \Log::error('searchUsers error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}