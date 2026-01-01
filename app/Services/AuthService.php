<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AuthService
{
    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->authRepository->register($data);
    }

    public function login(array $data)
    {
        return $this->authRepository->login($data);
    }

    public function tokenLogin(array $data)
    {
        return $this->authRepository->tokenLogin($data);
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('users', 'public');
    }

    protected function normalizePhotoUrl($photo)
    {
        if (empty($photo)) {
            return null;
        }

        // If already full URL (http / https), return as is
        if (is_string($photo) && str_starts_with($photo, 'http')) {
            return $photo;
        }

        // If stored path like "categories/xxx.jpg"
        if (is_string($photo)) {
            return config('app.url') . '/storage/' . ltrim($photo, '/');
        }

        return null;
    }
}
