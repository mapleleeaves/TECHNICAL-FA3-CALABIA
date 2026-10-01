<?php

namespace App\Controllers;

use App\Models\CustomerModel;

class Customers extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index()
    {
        $customerModel = new CustomerModel();

        return view('customers/index', [
            'customers' => $customerModel->findAll()
        ]);
    }

    public function new()
    {
        return view('customers/new');
    }

    public function create()
    {
        $rules = [
            'full_name' => 'required|min_length[2]|max_length[100]',
            'email'     => 'required|valid_email|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $customerModel = new CustomerModel();

        $customerModel->insert([
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $this->request->getPost('email')
        ]);

        return redirect()
            ->to('/customers')
            ->with('message', 'Customer added successfully.');
    }
    public function edit($id)
{
    $customerModel = new CustomerModel();

    return view('customers/edit', [
        'customer' => $customerModel->find($id)
    ]);
}

public function update($id)
{
    $rules = [
        'full_name' => 'required|min_length[2]|max_length[100]',
        'email'     => 'required|valid_email|max_length[100]'
    ];

    if (!$this->validate($rules)) {
        return redirect()
            ->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $customerModel = new CustomerModel();

    $customerModel->update($id, [
        'full_name' => $this->request->getPost('full_name'),
        'email'     => $this->request->getPost('email')
    ]);

    return redirect()
        ->to('/customers')
        ->with('message', 'Customer updated successfully.');
    }
}