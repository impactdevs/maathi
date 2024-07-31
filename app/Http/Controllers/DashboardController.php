<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Query\Expression;


class DashboardController extends Controller
{
    public function index()
    {
        //balance
        $balance = (DB::table('top_up_funds')->sum('amount')) - (DB::table('funds_disbursement')->sum('amount'));

        //total beneficiaries
        $beneficiaries = DB::table('users')->where('id', '!=', auth()->user()->id)->count();

        //total disbursements
        $total_disbursements = DB::table('funds_disbursement')->sum('amount');

        //top up funds made this week
        $top_funds_week = DB::table('top_up_funds')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');

        //top up funds made this month
        $top_funds_month = DB::table('top_up_funds')->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');

        //total top up funds made in last 6 months
        $top_funds_6_months = DB::table('top_up_funds')->whereBetween('created_at', [now()->subMonths(6), now()])->sum('amount');

        //top ups made this year
        $top_funds_year = DB::table('top_up_funds')->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->sum('amount');

        //top ups made today
        $top_funds_today = DB::table('top_up_funds')->whereDate('created_at', now())->sum('amount');

        $top_beneficiaries = DB::table('funds_disbursement')
            ->join('users', 'funds_disbursement.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(funds_disbursement.amount) as total_amount'))
            ->groupBy('users.name')
            ->orderBy('total_amount', 'desc')
            ->limit(3)
            ->get();

        //get top ups per month
        $top_ups_per_month = DB::table('top_up_funds')
        ->select(DB::raw('MONTHNAME(created_at) as month'), DB::raw('SUM(amount) as total_amount'))
        ->groupBy(DB::raw('MONTH(created_at)'), DB::raw('MONTHNAME(created_at)'))
        ->get();

        return view(
            'layouts.pages.dashboard',
            compact(
                'balance',
                'beneficiaries',
                'total_disbursements',
                'top_funds_week',
                'top_funds_month',
                'top_funds_6_months',
                'top_funds_year',
                'top_funds_today',
                'top_beneficiaries',
                'top_ups_per_month'
            )
        );
    }
}
