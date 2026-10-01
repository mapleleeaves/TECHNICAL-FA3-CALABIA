<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index()
    {
        $userModel = new UserModel();

        return view('users/index', [
            'users' => $userModel->findAll()
        ]);
    }

    public function new()
    {
        return view('users/new');
    }

    public function create()
    {
        $rules = [
            'username'  => 'required|max_length[50]|is_unique[users.username]',
            'full_name' => 'required|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $userModel->insert([
            'username'  => $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name')
        ]);

        return redirect()
            ->to('/users')
            ->with('message', 'User added successfully.');
    }
    public function edit($id)
{
    $userModel = new UserModel();

    return view('users/edit', [
        'user' => $userModel->find($id)
    ]);
}

    public function update($id)
    {
        $rules = [
            'username' => 'required|max_length[50]|is_unique[users.username,id,' . $id . ']',
            'full_name' => 'required|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name')
        ];

        $avatar = $this->request->getFile('avatar');

        if ($avatar->getError() !== UPLOAD_ERR_NO_FILE) {
            $avatarRules = [
                'avatar' => 'uploaded[avatar]|max_size[avatar,2048]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png]'
            ];

            if (!$this->validate($avatarRules)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $newName = $avatar->getRandomName();
            $temporaryName = 'original_' . $newName;
            $uploadPath = FCPATH . 'uploads/avatars/';

            $avatar->move($uploadPath, $temporaryName);

            service('image')
                ->withFile($uploadPath . $temporaryName)
                ->fit(300, 300, 'center')
                ->save($uploadPath . $newName);

            unlink($uploadPath . $temporaryName);

            $data['avatar'] = $newName;
        }

        $userModel = new UserModel();
        $userModel->update($id, $data);

        return redirect()
            ->to('/users')
            ->with('message', 'User updated successfully.');
    }
}