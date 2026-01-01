<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CategoryService
{

    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAll(array $fields)
    {
        $categories = $this->categoryRepository->getAll($fields);

        return $categories->map(function ($item) {
            $item->photo = $this->normalizePhotoUrl($item->photo);
            return $item;
        });
    }


    public function getById($id, array $fields)
    {
        $item = $this->categoryRepository->getById($id, $fields ?? ['*']);

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
        return $this->categoryRepository->create($data);
    }

    public function update(array $data, $id)
    {
        $fields = ['*'];
        $category = $this->categoryRepository->getById($id, $fields);
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($category->photo)) {
                $this->deletePhoto($category->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->categoryRepository->update($id, $data);
    }

    public function delete($id)
    {
        $fields = ['*'];

        $category = $this->categoryRepository->getById($id, $fields);

        if (!empty($category->photo)) {
            $this->deletePhoto($category->photo);
        }

        $this->categoryRepository->delete($id);
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
