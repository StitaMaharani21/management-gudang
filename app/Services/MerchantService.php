<?php

namespace App\Services;

use App\Models\Merchant;
use App\Repositories\MerchantRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class MerchantService
{
    private MerchantRepository $merchantRepository;

    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    public function getAll(array $fields)
    {
        $merchants = $this->merchantRepository->getAll($fields);

        return $merchants->map(function ($item) {
            $item->photo = $this->normalizePhotoUrl($item->photo);
            return $item;
        });
    }

    public function getById($id, array $fields)
    {
        $item = $this->merchantRepository->getById($id, $fields ?? ['*']);

        if ($item) {
            $item->photo = $this->normalizePhotoUrl($item->photo);
        }

        return $item;
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->merchantRepository->create($data);
    }

    public function update($id, array $data)
    {
        $fields = ['*'];
        $merchant = $this->merchantRepository->getById($id, $fields);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($merchant->photo)) {
                $this->deletePhoto($merchant->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->merchantRepository->update($id, $data);
    }

    public function delete($id)
    {
        $fields = ['*'];
        $merchant = $this->merchantRepository->getById($id, $fields);
        if (!empty($merchant->photo)) {
            $this->deletePhoto($merchant->photo);
        }
        $this->merchantRepository->delete($id);
    }

    public function getByKeeperId($keeperId)
    {
        $fields = ['*'];
        return $this->merchantRepository->getByKeeperId($keeperId, $fields);
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('merchants', 'public');
    }

    private function deletePhoto($photoPath)
    {
        $relativePath = 'merchants/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
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
