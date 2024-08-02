<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all users except one logged in
        $beneficiaries = DB::table('users')->where('id', '!=', auth()->id())->where('deleted_at', null)->get();
        return view('user-management.index', compact('beneficiaries'));
    }

    /**
     * add a beneficiary
     */
    public function addBeneficiary(Request $request)
    {
        //validate name
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $save = DB::table('users')->insert([
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$save) {
            return redirect()->route('user-management.index')->with('error', 'Failed to add beneficiary');
        }

        return redirect()->route('user-management.index')->with('success', 'Beneficiary added successfully');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
