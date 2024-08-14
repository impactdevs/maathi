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
        $accounts = DB::table('accounts')->where('deleted_at', null)->get();
        $top_funds = DB::table('top_up_funds')->join('accounts', 'top_up_funds.account_id', '=', 'accounts.id')
            ->select('top_up_funds.*', 'accounts.name as name')->get();
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

        // if the account_type is usd, set amount_usd to the amount
        if ($request->account_type == 'usd') {
            $request->amount_usd = $request->amount;
        } else {
            $request->amount_ugx = $request->amount;
        }

        $save = DB::table('top_up_funds')->insert([
            'account_id' => $request->account_id,
            'amount_ugx' => $request->amount_ugx,
            'amount_usd' => $request->amount_usd,
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

    // un do add funds by deleting it permanently
    public function deletefunds(Request $request)
    {
        $request->validate([
            'top_up_id' => 'required',
        ]);

        $delete = DB::table('top_up_funds')->where('id', $request->top_up_id)->delete();

        if (!$delete) {
            return redirect()->back()->with('error', 'Failed to revert top up');
        }

        return redirect()->back()->with('success', 'Top up reverted');
    }

    /**
     * funds disbursement index page
     */
    public function fundsdisbursement()
    {
        //get the total amount of funds in the system
        $total_funds_ugx = (DB::table('top_up_funds')->sum('amount_ugx')) - (DB::table('funds_disbursement')->sum('amount_ugx'));
        $total_funds_usd = (DB::table('top_up_funds')->sum('amount_usd')) - (DB::table('funds_disbursement')->sum('amount_usd'));
        // get a list of all users except the authenticated user
        $beneficiaries = DB::table('users')->where('id', '!=', auth()->user()->id)->where('deleted_at', null)->get();

        // accounts
        $accounts = DB::table('accounts')->where('deleted_at', null)->get();
        return view('funds-disbursement.index', compact('beneficiaries', 'total_funds_ugx', 'total_funds_usd', 'accounts'));
    }

    //un do disbursement by deleting it permanently
    public function deletedisbursement(Request $request)
    {
        $request->validate([
            'disbursement_id' => 'required',
        ]);

        $delete = DB::table('funds_disbursement')->where('id', $request->disbursement_id)->delete();

        if (!$delete) {
            return redirect()->back()->with('error', 'Failed to revert disbursement');
        }

        return redirect()->back()->with('success', 'Disbursement revert successfully');
    }

        //un do disbursement by deleting it permanently
        public function deletecashout(Request $request)
        {
            $request->validate([
                'cashout_id' => 'required',
            ]);

            $delete = DB::table('cash_outs')->where('id', $request->cashout_id)->delete();

            if (!$delete) {
                return redirect()->back()->with('error', 'Failed to revert cash out');
            }

            return redirect()->back()->with('success', 'Cash out reverted');
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

            //if account type is usd, set amount_usd to the amount
            if ($beneficiary['accountType'] == 'usd') {
                $beneficiary['amount_usd'] = $beneficiary['amount'];
            } else {
                $beneficiary['amount_ugx'] = $beneficiary['amount'];
            }

            $insertData[] = [
                'account_id' => $beneficiary['accountId'],
                'user_id' => $beneficiary['value'],
                'amount_ugx' => $beneficiary['amount_ugx'] ?? null,
                'amount_usd' => $beneficiary['amount_usd'] ?? null,
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
        $payouts = DB::table('funds_disbursement')
            ->join('users', 'funds_disbursement.user_id', '=', 'users.id')
            ->select('funds_disbursement.*', 'users.name as name')->get();

        //add account name to the payout data
        foreach ($payouts as $payout) {
            $payout->account_name = DB::table('accounts')->where('id', $payout->account_id)->value('name');
        }
        return view('payout-management.index', compact('payouts'));
    }

    // payout management
    public function cashoutmanagement()
    {
        $cashouts = DB::table('cash_outs')->get();
        return view('cashout-management.index', compact('cashouts'));
    }

    /**
     * accounts management
     */
    public function accountsmanagement()
    {
        //get accounts exluding trashed with their balances as the sum of top up funds minus the sum of funds disbursed each account
        $accounts = DB::table('accounts')->whereNull('deleted_at')->get();
        foreach ($accounts as $account) {
            $account->balance_ugx = (DB::table('top_up_funds')->where('account_id', $account->id)->sum('amount_ugx')) - (DB::table('funds_disbursement')->where('account_id', $account->id)->sum('amount_ugx'));
            $account->balance_usd = (DB::table('top_up_funds')->where('account_id', $account->id)->sum('amount_usd')) - (DB::table('funds_disbursement')->where('account_id', $account->id)->sum('amount_usd'));
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

    //delete account
    public function deleteaccount(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
        ]);

        //soft delete the account by updating the deleted_at column
        $delete = DB::table('accounts')->where('id', $request->account_id)->update([
            'deleted_at' => now(),
        ]);

        if (!$delete) {
            return redirect()->back()->with('error', 'Failed to delete account');
        }

        return redirect()->back()->with('success', 'Account deleted successfully');
    }


    //edit account
    public function editaccount(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
        ]);

        $update = DB::table('accounts')->where('id', $request->account_id)->update([
            'name' => $request->account_name,
            'updated_at' => now(),
        ]);

        if (!$update) {
            return redirect()->route('accounts-management')->with('error', 'Failed to update account');
        }

        return redirect()->route('accounts-management')->with('success', 'Account updated successfully');
    }

    // get account details
    public function getaccountdetails($id)
    {
        // get the balance of the account
        $balance_ugx = (DB::table('top_up_funds')->where('account_id', $id)->sum('amount_ugx')) - (DB::table('funds_disbursement')->where('account_id', $id)->sum('amount_ugx'));
        $balance_usd = (DB::table('top_up_funds')->where('account_id', $id)->sum('amount_usd')) - (DB::table('funds_disbursement')->where('account_id', $id)->sum('amount_usd'));
        $account = DB::table('accounts')->where('id', $id)->first();
        $account->balance_ugx = $balance_ugx;
        $account->balance_usd = $balance_usd;
        $account->top_ups = DB::table('top_up_funds')->where('account_id', $id)->orderBy('top_up_date', 'desc')->get();
        $account->disbursements = DB::table('funds_disbursement')->where('account_id', $id)->orderBy('disbursement_date', 'desc')->join('users', 'funds_disbursement.user_id', '=', 'users.id')->get();
        if (!$account) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        return view('accounts-management.account-details', compact('account'));
    }

    //delete beneficiary
    public function deletebeneficiary(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        //soft delete the account by updating the deleted_at column
        $delete = DB::table('users')->where('id', $request->user_id)->update([
            'deleted_at' => now(),
        ]);
        if (!$delete) {
            return redirect()->back()->with('error', 'Failed to delete beneficiary');
        }

        return redirect()->back()->with('success', 'Beneficiary deleted successfully');
    }

    //edit beneficiary
    public function editbeneficiary(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->account_type == 'cash_out') {
            $update = DB::table('users')->where('id', $request->id)->update([
                'name' => $request->name,
                'beneficiary_type' => 'cash_out',
                'updated_at' => now(),
            ]);
        } else {
            $update = DB::table('users')->where('id', $request->id)->update([
                'name' => $request->name,
                'beneficiary_type' => 'client',
                'updated_at' => now(),
            ]);
        }


        if (!$update) {
            return redirect()->back()->with('error', 'Failed to update beneficiary');
        }

        return redirect()->back()->with('success', 'Beneficiary updated successfully');
    }

    public function cashOut(Request $request)
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

            //if account type is usd, set amount_usd to the amount
            if ($beneficiary['accountType'] == 'usd') {
                $beneficiary['amount_usd'] = $beneficiary['amount'];
            } else {
                $beneficiary['amount_ugx'] = $beneficiary['amount'];
            }

            $insertData[] = [
                'user_id' => $beneficiary['value'],
                'amount_ugx' => $beneficiary['amount_ugx'] ?? null,
                'amount_usd' => $beneficiary['amount_usd'] ?? null,
                'description' => $beneficiary['reason'],
                'disbursement_date' => $beneficiary['disbursementDate'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert the data into the cash_outs table
        $save = DB::table('cash_outs')->insert($insertData);

        // Check if the insertion was successful
        if (!$save) {
            return redirect()->route('funds-disbursement.index')->with('error', 'Failed to disburse funds');
        }

        return redirect()->route('funds-disbursement.index')->with('success', 'Funds disbursed successfully');
    }
}
