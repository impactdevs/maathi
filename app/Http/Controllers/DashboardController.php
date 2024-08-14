<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        //balance
        $balance_ugx = (DB::table('top_up_funds')->sum('amount_ugx')) - (DB::table('funds_disbursement')->sum('amount_ugx'));
        $balance_usd = (DB::table('top_up_funds')->sum('amount_usd')) - (DB::table('funds_disbursement')->sum('amount_usd'));

        //this balance is from te cash out accounts
        $balance_carried_forward_ugx = DB::table('top_up_funds')->sum('amount_ugx')-DB::table('cash_outs')->sum('amount_ugx');
        $balance_carried_forward_usd = DB::table('top_up_funds')->sum('amount_usd')-DB::table('cash_outs')->sum('amount_usd');

        //total beneficiaries
        $beneficiaries = DB::table('users')->where('id', '!=', auth()->user()->id)->count();

        //total disbursements
        $total_disbursements_ugx = DB::table('funds_disbursement')->sum('amount_ugx');
        $total_disbursements_usd = DB::table('funds_disbursement')->sum('amount_usd');

        //top up funds made this week
        $top_funds_week_ugx = DB::table('top_up_funds')->whereBetween('top_up_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount_ugx');
        $top_funds_week_usd = DB::table('top_up_funds')->whereBetween('top_up_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount_usd');

        // Get the start and end of the current month in YYYY-MM-DD format
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        //top up funds made this month
        // Retrieve the sum of the top up funds made this month
        $top_funds_month_ugx = DB::table('top_up_funds')
            ->whereBetween('top_up_date', [$startOfMonth, $endOfMonth])
            ->sum('amount_ugx');
        $top_funds_month_usd = DB::table('top_up_funds')
            ->whereBetween('top_up_date', [$startOfMonth, $endOfMonth])
            ->sum('amount_usd');

        //total top up funds made in last 6 months
        $top_funds_6_months_ugx = DB::table('top_up_funds')->whereBetween('top_up_date', [now()->subMonths(6), now()])->sum('amount_ugx');
        $top_funds_6_months_usd = DB::table('top_up_funds')->whereBetween('top_up_date', [now()->subMonths(6), now()])->sum('amount_usd');

        //top ups made this year
        $top_funds_year_ugx = DB::table('top_up_funds')->whereBetween('top_up_date', [now()->startOfYear(), now()->endOfYear()])->sum('amount_ugx');
        $top_funds_year_usd = DB::table('top_up_funds')->whereBetween('top_up_date', [now()->startOfYear(), now()->endOfYear()])->sum('amount_usd');

        //top ups made today
        $top_funds_today_ugx = DB::table('top_up_funds')->whereDate('top_up_date', now())->sum('amount_ugx');
        $top_funds_today_usd = DB::table('top_up_funds')->whereDate('top_up_date', now())->sum('amount_usd');

        $top_beneficiaries = DB::table('funds_disbursement')
            ->join('users', 'funds_disbursement.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(funds_disbursement.amount_ugx) as total_amount'))
            ->groupBy('users.name')
            ->orderBy('total_amount', 'desc')
            ->limit(3)
            ->get();

        //get top ups per month
        $top_ups_per_month = DB::table('top_up_funds')
            ->select(DB::raw('MONTHNAME(top_up_date) as month'), DB::raw('SUM(amount_ugx) as total_amount'))
            ->groupBy(DB::raw('MONTH(top_up_date)'), DB::raw('MONTHNAME(top_up_date)'))
            ->get();

        return view(
            'layouts.pages.dashboard',
            compact(
                'balance_ugx',
                'balance_usd',
                'balance_carried_forward_ugx',
                'balance_carried_forward_usd',
                'beneficiaries',
                'total_disbursements_ugx',
                'total_disbursements_usd',
                'top_funds_week_ugx',
                'top_funds_week_usd',
                'top_funds_month_ugx',
                'top_funds_month_usd',
                'top_funds_6_months_ugx',
                'top_funds_6_months_usd',
                'top_funds_year_ugx',
                'top_funds_year_usd',
                'top_funds_today_ugx',
                'top_funds_today_usd',
                'top_beneficiaries',
                'top_ups_per_month',
            )
        );
    }
}
