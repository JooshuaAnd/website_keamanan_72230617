<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LMSController extends Controller
{
    protected $backendUrl = 'http://localhost:8085/api/v1';

    private function getBackendUrl()
    {
        return env('FASTAPI_URL', 'http://localhost:8085/api/v1');
    }

    private function getToken()
    {
        return Session::get('api_token');
    }

    private function apiGet($endpoint)
    {
        $token = $this->getToken();
        if (!$token) return null;
        $response = Http::withToken($token)->timeout(10)->get($this->getBackendUrl() . $endpoint);
        if (!$response->successful()) return null;
        return $response->json();
    }

    private function apiPost($endpoint, $data)
    {
        $token = $this->getToken();
        if (!$token) return null;
        $response = Http::withToken($token)->timeout(10)->post($this->getBackendUrl() . $endpoint, $data);
        return $response;
    }

    private function apiPut($endpoint, $data)
    {
        $token = $this->getToken();
        if (!$token) return null;
        $response = Http::withToken($token)->timeout(10)->put($this->getBackendUrl() . $endpoint, $data);
        return $response;
    }

    private function apiDelete($endpoint)
    {
        $token = $this->getToken();
        if (!$token) return null;
        $response = Http::withToken($token)->timeout(10)->delete($this->getBackendUrl() . $endpoint);
        return $response;
    }

    // ============ ADMIN ============

    public function adminDashboard()
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'admin',
            'full_name' => Session::get('user_name', 'Admin'),
            'email' => Session::get('user_email', '')
        ];
        $participants = $this->apiGet('/lms/participants') ?? [];
        $lecturers = $this->apiGet('/lms/lecturers') ?? [];
        $materials = $this->apiGet('/lms/materials') ?? [];
        return view('lms.admin.dashboard', compact('user', 'participants', 'lecturers', 'materials'));
    }

    public function adminParticipants()
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'admin',
            'full_name' => Session::get('user_name', 'Admin'),
            'email' => Session::get('user_email', '')
        ];
        $participants = $this->apiGet('/lms/participants') ?? [];
        return view('lms.admin.participants', compact('user', 'participants'));
    }

    public function adminLecturers()
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'admin',
            'full_name' => Session::get('user_name', 'Admin'),
            'email' => Session::get('user_email', '')
        ];
        $lecturers = $this->apiGet('/lms/lecturers') ?? [];
        return view('lms.admin.lecturers', compact('user', 'lecturers'));
    }

    public function adminMaterials()
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'admin',
            'full_name' => Session::get('user_name', 'Admin'),
            'email' => Session::get('user_email', '')
        ];
        $materials = $this->apiGet('/lms/materials') ?? [];
        return view('lms.admin.materials', compact('user', 'materials'));
    }

    // ============ DOSEN ============

    public function dosenDashboard()
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'dosen',
            'full_name' => Session::get('user_name', 'Dosen'),
            'email' => Session::get('user_email', '')
        ];
        $participants = $this->apiGet('/lms/participants') ?? [];
        $materials = $this->apiGet('/lms/materials') ?? [];
        return view('lms.dosen.dashboard', compact('user', 'participants', 'materials'));
    }

    public function dosenMaterials()
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'dosen',
            'full_name' => Session::get('user_name', 'Dosen'),
            'email' => Session::get('user_email', '')
        ];
        $materials = $this->apiGet('/lms/materials') ?? [];
        return view('lms.dosen.materials', compact('user', 'materials'));
    }

    public function dosenUpload()
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'dosen',
            'full_name' => Session::get('user_name', 'Dosen'),
            'email' => Session::get('user_email', '')
        ];
        return view('lms.dosen.upload', compact('user'));
    }

    public function dosenUploadStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : null,
        ];

        $response = $this->apiPost('/lms/materials', $data);
        if ($response && $response->successful()) {
            return redirect()->route('dosen.materials')->with('success', 'Material uploaded successfully');
        }
        $error = ($response && $response->json()) ? ($response->json()['detail'] ?? 'Failed to upload material') : 'Failed to upload material';
        return back()->withErrors(['message' => $error]);
    }

    // ============ PESERTA ============

    public function pesertaDashboard()
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'peserta',
            'full_name' => Session::get('user_name', 'Peserta'),
            'email' => Session::get('user_email', '')
        ];
        $materials = $this->apiGet('/lms/materials') ?? [];
        return view('lms.peserta.dashboard', compact('user', 'materials'));
    }

    public function pesertaSearch()
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'peserta',
            'full_name' => Session::get('user_name', 'Peserta'),
            'email' => Session::get('user_email', '')
        ];
        return view('lms.peserta.search', compact('user'));
    }

    public function pesertaSearchResults(Request $request)
    {
        $user = [
            'id' => Session::get('user_id'),
            'role' => 'peserta',
            'full_name' => Session::get('user_name', 'Peserta'),
            'email' => Session::get('user_email', '')
        ];
        $request->validate(['q' => 'required|string|max:100']);
        
        $token = $this->getToken();
        if (!$token) return redirect()->route('login');

        $response = Http::withToken($token)->timeout(10)->get($this->getBackendUrl() . '/lms/materials/search?q=' . urlencode($request->q));
        if ($response->failed()) {
            $error = $response->json()['detail'] ?? 'Input tidak valid';
            return view('lms.peserta.search', compact('user'))->withErrors(['message' => $error]);
        }
        $results = $response->json();
        return view('lms.peserta.search', compact('user', 'results'));
    }

    public function profile()
    {
        $apiUser = $this->apiGet('/users/me');
        if ($apiUser) {
            $user = [
                'id' => $apiUser['id'] ?? null,
                'role' => $apiUser['role'] ?? 'peserta',
                'full_name' => $apiUser['full_name'] ?? 'User',
                'email' => $apiUser['email'] ?? '',
                'is_verified' => $apiUser['is_verified'] ?? false,
                'hashed_password' => $apiUser['hashed_password'] ?? 'N/A',
            ];
        } else {
            $user = [
                'id' => Session::get('user_id'),
                'role' => Session::get('user_role', 'peserta'),
                'full_name' => Session::get('user_name', 'User'),
                'email' => Session::get('user_email', ''),
                'is_verified' => true,
                'hashed_password' => 'N/A',
            ];
        }
        return view('auth.profile', compact('user'));
    }

    // ============ SEARCH ENDPOINTS (for AJAX) ============

    public function searchParticipants(Request $request)
    {
        $request->validate(['q' => 'required|string|max:100']);
        $token = $this->getToken();
        if (!$token) return response()->json(['error' => 'Unauthorized'], 401);

        $response = Http::withToken($token)->timeout(10)->get($this->getBackendUrl() . '/lms/participants/search?q=' . urlencode($request->q));
        if ($response->failed()) {
            return response()->json(['error' => $response->json()['detail'] ?? 'Input tidak valid'], $response->status());
        }
        return response()->json($response->json());
    }

    public function searchLecturers(Request $request)
    {
        $request->validate(['q' => 'required|string|max:100']);
        $token = $this->getToken();
        if (!$token) return response()->json(['error' => 'Unauthorized'], 401);

        $response = Http::withToken($token)->timeout(10)->get($this->getBackendUrl() . '/lms/lecturers/search?q=' . urlencode($request->q));
        if ($response->failed()) {
            return response()->json(['error' => $response->json()['detail'] ?? 'Input tidak valid'], $response->status());
        }
        return response()->json($response->json());
    }

    public function deleteParticipant($id)
    {
        $response = $this->apiDelete('/lms/participants/' . $id);
        if ($response && $response->successful()) {
            return redirect()->route('admin.participants')->with('success', 'Participant deleted successfully');
        }
        $error = ($response && $response->json()) ? ($response->json()['detail'] ?? 'Failed to delete participant') : 'Failed to delete participant';
        return back()->withErrors(['message' => $error]);
    }
}
