<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('distributions.index');
    }

    /**
     * top up funds index page
     */
    public function topupfunds()
    {
        $accounts = DB::table('accounts')->get();
        $top_funds = DB::table('top_up_funds')->join('accounts', 'top_up_funds.account_id', '=', 'accounts.id')->get();
        return view('top-up-funds.index', compact('top_funds', 'accounts'));
    }

    /**
     * Add Funds
     */
    public function addfunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'account_id' => 'required',
        ]);

        $save = DB::table('top_up_funds')->insert([
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'top_up_date' => $request->top_up_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$save) {
            return redirect()->route('top-up-funds.index')->with('error', 'Failed to add funds');
        }

        return redirect()->route('top-up-funds.index')->with('success', 'Funds added successfully');
    }

    /**
     * funds disbursement index page
     */
    public function fundsdisbursement()
    {
        //get the total amount of funds in the system
        $total_funds = (DB::table('top_up_funds')->sum('amount')) - (DB::table('funds_disbursement')->sum('amount'));
        // get a list of all users except the authenticated user
        $beneficiaries = DB::table('users')->where('id', '!=', auth()->user()->id)->get();

        // accounts
        $accounts = DB::table('accounts')->get();
        return view('funds-disbursement.index', compact('beneficiaries', 'total_funds', 'accounts'));
    }

    /**
     * Disburse funds
     */
    public function disburseFunds(Request $request)
    {
        // Get the beneficiaries data from the request
        $beneficiaries = $request->input('beneficiaries');

        // Ensure $beneficiaries is an array
        if (!is_array($beneficiaries)) {
            return redirect()->route('funds-disbursement.index')->with('error', 'Invalid beneficiaries data');
        }

        // Validate and prepare data for insertion
        $insertData = [];
        foreach ($beneficiaries as $beneficiary) {
            // Ensure each beneficiary has the required fields
            if (!isset($beneficiary['value']) || !isset($beneficiary['amount'])) {
                return redirect()->route('funds-disbursement.index')->with('error', 'Invalid beneficiary data');
            }

            $insertData[] = [
                'account_id' => $beneficiary['accountId'],
                'user_id' => $beneficiary['value'],
                'amount' => $beneficiary['amount'],
                'description' => $beneficiary['reason'],
                'disbursement_date' => $beneficiary['disbursementDate'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert the data into the funds_disbursement table
        $save = DB::table('funds_disbursement')->insert($insertData);

        // Check if the insertion was successful
        if (!$save) {
            return redirect()->route('funds-disbursement.index')->with('error', 'Failed to disburse funds');
        }

        return redirect()->route('funds-disbursement.index')->with('success', 'Funds disbursed successfully');
    }

    // payout management
    public function payoutmanagement()
    {
        $payouts = DB::table('funds_disbursement')->join('users', 'funds_disbursement.user_id', '=', 'users.id')->get();
        return view('payout-management.index', compact('payouts'));
    }

    /**
     * accounts management
     */
    public function accountsmanagement()
    {
        //get accounts with their balances as the sum of top up funds minus the sum of funds disbursed
        $accounts = DB::table('accounts')->get();
        foreach ($accounts as $account) {
            $account->balance = (DB::table('top_up_funds')->where('account_id', $account->id)->sum('amount')) - (DB::table('funds_disbursement')->where('account_id', $account->id)->sum('amount'));
        }
        return view('accounts-management.index', compact('accounts'));
    }

    /**
     * Add account
     */

    public function addaccount(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string',
        ]);

        $save = DB::table('accounts')->insert([
            'name' => $request->account_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$save) {
            return redirect()->route('accounts-management')->with('error', 'Failed to add account');
        }

        return redirect()->route('accounts-management')->with('success', 'Account added successfully');
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
    public function show(Distribution $distribution)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distribution $distribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Distribution $distribution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distribution $distribution)
    {
        //
    }
}
