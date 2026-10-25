<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'last_30_days' => DB::selectOne("
                    SELECT COUNT(v.id) AS total_visits,
                           COUNT(DISTINCT v.customer_id) AS unique_customers,
                           COALESCE(SUM(v.amount), 0) AS gross_revenue,
                           COALESCE(SUM(v.discount_applied), 0) AS total_discount,
                           COALESCE(SUM(v.final_amount), 0) AS net_revenue
                    FROM visits v
                    WHERE v.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                "),
                'retention' => DB::selectOne("
                    SELECT
                        COUNT(CASE WHEN total_visits = 1 THEN 1 END) AS one_time,
                        COUNT(CASE WHEN total_visits >= 2 THEN 1 END) AS repeat
                    FROM customers WHERE is_active = TRUE
                "),
            ],
        ]);
    }

    public function customers(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'top' => DB::select("
                    SELECT id, full_name, phone, loyalty_level,
                           total_visits, total_points, total_spent
                    FROM customers WHERE is_active = TRUE
                    ORDER BY total_spent DESC LIMIT 10
                "),
            ],
        ]);
    }
}