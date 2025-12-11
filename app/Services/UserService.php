<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll($fields)
    {
        $users = $this->userRepository->getAll($fields);

        return $users->map(function ($item) {
            if ($item->photo) {
                $item->photo = asset('storage/' . $item->photo);
            }
            return $item;
        });
    }

    public function getById($id, $fields)
    {
        $item = $this->userRepository->getById($id, $fields ?? ['*']);

        if ($item && $item->photo) {
            $item->photo = asset('storage/' . $item->photo);
        }

        return $item;
    }

    public function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->userRepository->create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->userRepository->getById($id, ['photo']);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if ($user->photo) {
                $this->deletePhoto($user->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->userRepository->update($id, $data);
    }

    public function delete($id)
    {
        $user = $this->userRepository->getById($id, ['photo']);

        if ($user->photo) {
            $this->deletePhoto($user->photo);
        }

        return $this->userRepository->delete($id);
    }



    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('categories', 'public');
    }

    private function deletePhoto($photoPath)
    {
        $relativePath = 'categories/' . basename($photoPath); 
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }


}